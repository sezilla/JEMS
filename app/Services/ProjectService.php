<?php

namespace App\Services;

use Exception;
use App\Models\User;
use App\Models\Project;
use App\Models\UserTask;
use App\Models\ChecklistUser;
use App\Services\PythonService;
use App\Services\TrelloService;
use App\Models\TrelloProjectTask;
use App\Events\AssignTaskSchedules;
use App\Events\SyncTrelloBoardToDB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Events\DueDateAssignedEvent;
use App\Events\TrelloBoardCreatedEvent;
use App\Events\TrelloBoardIsFinalEvent;
use Filament\Notifications\Notification;


class ProjectService
{
    private TrelloService $trello_service;
    private PythonService $python_service;
    protected $project;

    public function __construct(
        TrelloService $trello_service,
        PythonService $python_service,
        Project $project
    ) {
        $this->trello_service = $trello_service;
        $this->python_service = $python_service;
        $this->project = $project;
    }

    public function createTrelloBoardForProject(Project $project)
    {
        Log::info('Creating Trello board for project: ' . $project->name);

        // Ensure the package is loaded before accessing it
        if (!$project->package) {
            Log::error('Package is null for the project: ' . $project->name);
            return;
        }

        $packageName = $project->package->name;
        $boardResponse = $this->trello_service->createBoardFromTemplate($project->name, $packageName);

        if ($boardResponse && isset($boardResponse['id'])) {
            $project->trello_board_id = $boardResponse['id'];
            $project->save();
            Log::info('Trello board created with ID: ' . $boardResponse['id']);

            // Helper function to create or update card
            $createOrUpdateCard = function ($listId, $cardName, $cardData) use ($project) {
                $card = $this->trello_service->getCardByName($listId, $cardName);
                if (!$card) {
                    Log::info("$cardName card not found, creating new card.");
                    $card = $this->trello_service->createCardInList($listId, $cardName);
                }
                if ($card && isset($card['id'])) {
                    Log::info("Updating $cardName card.");
                    $this->trello_service->updateCard($card['id'], $cardData);
                    return $card['id'];
                }
                return null;
            };

            // Get the "Project details" and "Teams and Members" lists
            $projectDetailsList = $this->trello_service->getBoardListByName($project->trello_board_id, 'Project details');
            $coorList = $this->trello_service->getBoardListByName($project->trello_board_id, 'Coordinators');

            if ($coorList) {
                Log::info('Project Coordinator list found.');

                if ($project->groom_coordinator) {
                    $createOrUpdateCard($coorList['id'], 'groom coordinator', ['desc' => $project->groomCoordinator->name]);
                }

                if ($project->bride_coordinator) {
                    $createOrUpdateCard($coorList['id'], 'bride coordinator', ['desc' => $project->brideCoordinator->name]);
                }

                if ($project->head_coordinator) {
                    $createOrUpdateCard($coorList['id'], 'head coordinator', ['desc' => $project->headCoordinator->name]);
                }
            }

            if ($projectDetailsList) {
                Log::info('Project details list found.');
                $coupleCardId = $createOrUpdateCard(
                    $projectDetailsList['id'],
                    'name of couple',
                    ['desc' => "{$project->groom_name} & {$project->bride_name}", 'due' => $project->end]
                );
                $createOrUpdateCard($projectDetailsList['id'], 'package', ['desc' => $project->package->name]);
                $createOrUpdateCard($projectDetailsList['id'], 'description', ['desc' => $project->description]);
                $createOrUpdateCard($projectDetailsList['id'], 'venue of wedding', ['desc' => $project->venue]);
                $createOrUpdateCard($projectDetailsList['id'], 'wedding theme color', ['desc' => $project->theme_color]);
                $createOrUpdateCard($projectDetailsList['id'], 'special request', ['desc' => $project->special_request]);

                if ($project->thumbnail_path && $coupleCardId) {
                    Log::info('Adding thumbnail as cover to the couple name card.');
                    $this->trello_service->addAttachmentToCard($coupleCardId, $project->thumbnail_path);
                }
            } else {
                Log::error('Project details list not found.');
            }
        }
    }

    public function createSpecialRequest(Project $project)
    {
        Log::info('Classifying tasks due to special request', [
            'project_id' => $project->id,
            'special_request' => $project->special_request
        ]);

        $classificationResponse = $this->python_service->special_request(
            $project->id,
            $project->special_request
        );
        Log::info('Task classification response', ['response' => json_encode($classificationResponse)]);

        if (isset($classificationResponse['error'])) {
            throw new \Exception('Task Classification Error: ' . $classificationResponse['error']);
        }

        $departmentsList = $this->trello_service->getBoardListByName($project->trello_board_id, 'Departments');

        if ($departmentsList) {
            Log::info('Project details list found.');

            $departmentCards = $this->trello_service->getCardsByListId($departmentsList['id']);
            $departmentCardMap = [];

            foreach ($departmentCards as $card) {
                $departmentCardMap[$card['name']] = $card['id'];
            }

            foreach ($classificationResponse['special_request'] as $task) {
                list($department, $taskDescription) = $task;

                if (isset($departmentCardMap[$department])) {
                    $cardId = $departmentCardMap[$department];

                    $checklists = $this->trello_service->getChecklistsByCardId($cardId);
                    $checklistId = null;

                    foreach ($checklists as $checklist) {
                        if ($checklist['name'] === 'Special Requests') {
                            $checklistId = $checklist['id'];
                            break;
                        }
                    }

                    if (!$checklistId) {
                        $checklist = $this->trello_service->createChecklist($cardId, 'Special Requests');
                        $checklistId = $checklist['id'] ?? null;
                    }

                    if ($checklistId) {
                        $this->trello_service->createChecklistItem($checklistId, $taskDescription);
                    }
                } else {
                    Log::warning("No Trello card found for department: {$department}");
                }
            }
        } else {
            Log::error('Departments list not found on Trello.');
        }
    }

    public function syncTrelloToDatabase(Project $project)
    {
        if (!$project->trello_board_id) {
            Log::error('trello_board_id is null for the project: ' . $project->name);
            return;
        }

        $boardId = $project->trello_board_id;
        $departmentList = $this->trello_service->getBoardListByName($boardId, 'Departments');
        $cards = $this->trello_service->getCardsNameAndId($departmentList['id']);

        $structuredData = [];

        foreach ($cards as $card) {
            $departmentId = $card['id'];
            $departmentName = $card['name'];

            $checklists = $this->trello_service->getChecklistsByCardId($departmentId);

            foreach ($checklists as $checklist) {
                $categoryName = $checklist['name'];
                $items = $this->trello_service->getChecklistItems($checklist['id']);

                Log::info('Processing checklist', [
                    'checklist_id' => $checklist['id'],
                    'category_name' => $categoryName,
                    'item_count' => count($items),
                ]);

                foreach ($items as $item) {
                    $taskName = $item['name'];

                    if (!isset($structuredData[$departmentName])) {
                        $structuredData[$departmentName] = [];
                    }
                    if (!isset($structuredData[$departmentName][$categoryName])) {
                        $structuredData[$departmentName][$categoryName] = [];
                    }
                    $structuredData[$departmentName][$categoryName][] = $taskName;
                }
            }
        }

        Log::info('Final structured Trello data', ['structured_data' => $structuredData]);

        $result = TrelloProjectTask::updateOrCreate(
            [
                'project_id' => $project->id,
                'trello_board_id' => $boardId,
            ],
            [
                'trello_board_data' => json_encode($structuredData),
                'start_date' => $project->start,
                'event_date' => $project->end,
            ]
        );
        Log::info('Trello data saved to database', ['record_id' => $result->id]);
    }

    public function assignTaskSchedules(Project $project)
    {
        Log::info('Assigning task schedules for project: ' . $project->name);

        if (!$project->package) {
            Log::error('Package is null for the project: ' . $project->name);
            return;
        }

        $taskSchedulesResponse = $this->python_service->predictCategories($project->id);

        $boardId = $project->trello_board_id;
        $departmentList = $this->trello_service->getBoardListByName($boardId, 'Departments');
        $cards = $this->trello_service->getCardsNameAndId($departmentList['id']);

        if ($taskSchedulesResponse && isset($taskSchedulesResponse['trello_tasks'])) {
            $trelloTasks = $this->arrayChangeKeyCaseRecursive($taskSchedulesResponse['trello_tasks']);

            foreach ($cards as $card) {
                $departmentId   = $card['id'];  // This is the card ID.
                $departmentName = strtolower(trim($card['name']));

                $checklists = $this->trello_service->getChecklistsByCardId($departmentId);

                foreach ($checklists as $checklist) {
                    $categoryName = strtolower(trim($checklist['name']));
                    $items = $this->trello_service->getChecklistItems($checklist['id']);

                    Log::info('Processing checklist', [
                        'checklist_id'  => $checklist['id'],
                        'category_name' => $categoryName,
                        'item_count'    => count($items),
                    ]);

                    foreach ($items as $item) {
                        $taskName = strtolower(trim($item['name']));

                        if (
                            isset($trelloTasks[$departmentName])
                            && isset($trelloTasks[$departmentName][$categoryName])
                            && isset($trelloTasks[$departmentName][$categoryName][$taskName])
                        ) {
                            $dueDate = $trelloTasks[$departmentName][$categoryName][$taskName];

                            Log::info("Updating checklist item due date", [
                                'department' => $departmentName,
                                'category'   => $categoryName,
                                'task'       => $taskName,
                                'due_date'   => $dueDate,
                            ]);

                            $this->trello_service->setChecklistItemDueDate($departmentId, $item['id'], $dueDate);
                        } else {
                            Log::warning("No due date found for checklist item", [
                                'department' => $departmentName,
                                'category'   => $categoryName,
                                'task'       => $taskName,
                            ]);
                        }
                    }
                }
            }
        } else {
            Log::error('Failed to create task schedules', ['response' => json_encode($taskSchedulesResponse)]);
        }
    }

    private function arrayChangeKeyCaseRecursive(array $arr)
    {
        $result = [];
        foreach ($arr as $key => $value) {
            $lowerKey = strtolower($key);
            if (is_array($value)) {
                $result[$lowerKey] = $this->arrayChangeKeyCaseRecursive($value);
            } else {
                $result[$lowerKey] = $value;
            }
        }
        return $result;
    }

    public function allocateUserToTask(Project $project)
    {
        try {
            Log::info('Starting user allocation for project', [
                'project_id' => $project->id,
                'project_name' => $project->name
            ]);

            if (!$project->trello_board_id) {
                throw new \Exception('Trello board ID is missing for project: ' . $project->id);
            }

            // Get department list and cards in a single call
            $departmentList = $this->trello_service->getDepartmentsListId($project->trello_board_id);
            if (!$departmentList) {
                throw new \Exception('Department list not found for project: ' . $project->id);
            }

            $cards = $this->trello_service->getListCards($departmentList);
            if (empty($cards)) {
                throw new \Exception('No cards found in department list for project: ' . $project->id);
            }

            // Prepare task data with chunking for memory efficiency
            $dataArray = [
                'project_id' => $project->id,
                'data_array' => []
            ];

            foreach ($cards as $card) {
                $checklists = $this->trello_service->getChecklistsByCardId($card['id']);
                $checklistArray = [];

                foreach ($checklists as $checklist) {
                    $checkItems = $this->trello_service->getChecklistItems($checklist['id']);
                    $checkItemsArray = array_map(function ($item) {
                        return [
                            'check_item_id' => $item['id'],
                            'check_item_name' => $item['name']
                        ];
                    }, $checkItems);

                    $checklistArray[] = [
                        'checklist_id' => $checklist['id'],
                        'checklist_name' => $checklist['name'],
                        'check_items' => $checkItemsArray
                    ];
                }

                $dataArray['data_array'][] = [
                    'card_id' => $card['id'],
                    'card_name' => $card['name'],
                    'checklists' => $checklistArray
                ];
            }

            // Eager load teams with users and skills to avoid N+1 queries
            $teams = $project->teams()
                ->with(['users.skills', 'departments'])
                ->get();

            if ($teams->isEmpty()) {
                throw new \Exception('No teams found for project: ' . $project->id);
            }

            $usersArray = [];
            foreach ($teams as $team) {
                foreach ($team->departments as $department) {
                    $departmentName = $department->name;
                    $usersInDept = $team->users->map(function ($user) {
                        return [
                            'user_id' => $user->id,
                            'skills' => $user->skills->pluck('name')->toArray()
                        ];
                    })->toArray();

                    if (!isset($usersArray[$departmentName])) {
                        $usersArray[$departmentName] = $usersInDept;
                    } else {
                        $usersArray[$departmentName] = array_merge($usersArray[$departmentName], $usersInDept);
                    }
                }
            }

            if (empty($usersArray)) {
                throw new \Exception('No users found in any department for project: ' . $project->id);
            }

            // Retry logic for Python service call
            $maxRetries = 3;
            $retryCount = 0;
            $response = null;
            $lastError = null;

            while ($retryCount < $maxRetries) {
                try {
                    $response = $this->python_service->allocateUserToTask($project->id, $dataArray, $usersArray);

                    // Check if response is valid
                    if (isset($response['success']) && $response['success'] === true) {
                        break;
                    }

                    // If we get here, the response wasn't successful
                    $lastError = $response['error'] ?? 'Unknown error';
                    throw new \Exception('Invalid response from Python service: ' . $lastError);
                } catch (\Exception $e) {
                    $lastError = $e->getMessage();
                    Log::warning('Python service call failed', [
                        'attempt' => $retryCount + 1,
                        'error' => $lastError
                    ]);

                    $retryCount++;
                    if ($retryCount === $maxRetries) {
                        throw new \Exception('Failed to allocate users after ' . $maxRetries . ' attempts. Last error: ' . $lastError);
                    }

                    // Exponential backoff
                    sleep(pow(2, $retryCount));
                }
            }

            if (!isset($response['success']) || $response['success'] !== true) {
                throw new \Exception('User Allocation Error: ' . ($response['error'] ?? 'Unknown error'));
            }

            // Extract allocation data
            $allocation = $response['checklists'] ??
                $response['allocation'] ??
                $response['checklist_id'] ??
                ($response['response']['allocation'] ?? null);

            if (!$allocation) {
                throw new \Exception('No allocation data found in response');
            }

            // Use database transaction for atomic operations
            DB::beginTransaction();
            try {
                // Update or create checklist user record
                ChecklistUser::updateOrCreate(
                    ['project_id' => $project->id],
                    ['user_checklist' => $allocation]
                );

                // Batch insert user tasks
                $userTasks = [];

                foreach ($allocation as $checklistId => $tasks) {
                    foreach ($tasks as $task) {
                        $userTasks[] = [
                            'user_id' => $task['user_id'],
                            'check_item_id' => $task['check_item_id'],
                            'status' => 'incomplete',
                            'task_name' => $task['check_item_name'],
                            'card_id' => $task['card_id'],
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }
                }

                // Batch insert user tasks
                if (!empty($userTasks)) {
                    UserTask::upsert(
                        $userTasks,
                        ['user_id', 'check_item_id'],
                        ['status', 'task_name', 'card_id', 'updated_at']
                    );
                }

                // Send Filament notifications
                foreach ($allocation as $checklistId => $tasks) {
                    foreach ($tasks as $task) {
                        $user = User::find($task['user_id']);
                        if ($user) {
                            Notification::make()
                                ->title('New Task Assigned')
                                ->body('You have been assigned a new task: ' . $task['check_item_name'])
                                ->success()
                                ->sendToDatabase($user);
                        }
                    }
                }

                DB::commit();

                Log::info('User allocation completed successfully', [
                    'project_id' => $project->id,
                    'tasks_allocated' => count($userTasks)
                ]);

                return [
                    'success' => true,
                    'message' => 'User allocation completed successfully',
                    'tasks_allocated' => count($userTasks)
                ];
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Failed to save allocation data', [
                    'project_id' => $project->id,
                    'error' => $e->getMessage()
                ]);
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('User allocation failed', [
                'project_id' => $project->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function markAsDone(Project $project)
    {
        Log::info('Marking project as done: ' . $project->name);

        Log::info('Project marked as done: ' . $project->name);
        $this->trello_service->closeBoard($project->trello_board_id);
        Log::info('Trello board closed for project: ' . $project->name);
    }

    public function getProjectProgress(?Project $project)
    {
        if (!$project) {
            Log::warning('Attempted to get project progress with null project');
            return [];
        }

        Log::info('Getting project progress for project: ' . $project->name);

        if (!$project->trello_board_id) {
            Log::error('Trello board ID is null for the project: ' . $project->name);
            return [];
        }

        $boardId = $project->trello_board_id;
        $progress = $this->trello_service->getTrelloBoardProgress($boardId);

        Log::info('Project progress retrieved', ['progress' => $progress]);
        return $progress ?? [];
    }
}
