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

        $packageName = $project->package->name;
        $boardResponse = $this->trello_service->createBoardFromTemplate($project->name, $packageName);

        if ($boardResponse && isset($boardResponse['id'])) {
            $project->trello_board_id = $boardResponse['id'];
            $project->save();
            Log::info('Trello board created with ID: ' . $boardResponse['id']);

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

            $projectDetailsList = $this->trello_service->getBoardListByName($project->trello_board_id, 'Project details');
            $coordinatorList = $this->trello_service->getBoardListByName($project->trello_board_id, 'Coordinators');

            if ($coordinatorList) {
                Log::info('Project Coordinator list found.');

                if ($project->groom_coordinator) {
                    $createOrUpdateCard($coordinatorList['id'], 'groom coordinator', ['desc' => $project->groomCoordinator->name]);
                }

                if ($project->bride_coordinator) {
                    $createOrUpdateCard($coordinatorList['id'], 'bride coordinator', ['desc' => $project->brideCoordinator->name]);
                }

                if ($project->head_coordinator) {
                    $createOrUpdateCard($coordinatorList['id'], 'head coordinator', ['desc' => $project->headCoordinator->name]);
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

    public function syncChecklist(Project $project)
    {
        $boardId = $project->trello_board_id;
        $departmentList = $this->trello_service->getBoardListByName($boardId, 'Departments');
        $cards = $this->trello_service->getCardsNameAndId($departmentList['id']);

        $structuredData = [];

        foreach ($cards as $card) {
            $cardDetails = $this->trello_service->getCardData($card['id']);
            $checklists = $this->trello_service->getChecklistsByCardId($card['id']);

            $cardData = [
                'card_id' => $card['id'],
                'card_name' => $card['name'],
                'card_due_date' => $cardDetails['due'] ? date('Y-m-d', strtotime($cardDetails['due'])) : null,
                'card_description' => $cardDetails['desc'] ?? '',
                'checklists' => []
            ];

            foreach ($checklists as $checklist) {
                $checklistItems = $this->trello_service->getChecklistItems($checklist['id']);

                $checklistData = [
                    'checklist_id' => $checklist['id'],
                    'checklist_name' => $checklist['name'],
                    'check_items' => []
                ];

                foreach ($checklistItems as $item) {

                    $checklistData['check_items'][] = [
                        'check_item_id' => $item['id'],
                        'check_item_name' => $item['name'],
                        'due_date' => $item['due'] ? date('Y-m-d', strtotime($item['due'])) : null,
                        'status' => $item['state'] ?? 'incomplete',
                        'user_id' => null
                    ];
                }

                $cardData['checklists'][] = $checklistData;
            }

            $structuredData[] = $cardData;
        }

        // Save to ChecklistUser model
        ChecklistUser::updateOrCreate(
            ['project_id' => $project->id],
            ['user_checklist' => $structuredData]
        );

        Log::info('Checklist data synced', ['project_id' => $project->id, 'data' => $structuredData]);
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

            // Get ChecklistUser record
            $checklistUser = $project->checklist;
            if (!$checklistUser) {
                Log::error('No checklist found for project: ' . $project->name);
                return;
            }

            $userChecklist = $checklistUser->user_checklist ?? [];

            foreach ($cards as $card) {
                $departmentId   = $card['id'];
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

                            // Update Trello checklist item due date
                            $this->trello_service->setChecklistItemDueDate($departmentId, $item['id'], $dueDate);

                            // Update ChecklistUser model - only update existing entries
                            if (isset($userChecklist[$checklist['id']])) {
                                foreach ($userChecklist[$checklist['id']] as &$entry) {
                                    if ($entry['check_item_id'] === $item['id']) {
                                        $entry['due_date'] = $dueDate;
                                        break;
                                    }
                                }
                            }

                            // // Update UserTask model
                            // UserTask::where('check_item_id', $item['id'])
                            //     ->update(['due_date' => $dueDate]);

                            // $this->syncChecklist($project);
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

            // Save updated checklist data
            $checklistUser->user_checklist = $userChecklist;
            $checklistUser->save();
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

    public function allocateUser(Project $project)
    {
        $checklistUser = ChecklistUser::where('project_id', $project->id)->first();

        if (!$checklistUser) {
            Log::error('No checklist found for project', ['project_id' => $project->id]);
            throw new \Exception('No checklist found for project: ' . $project->id);
        }

        $dataArray = $checklistUser->user_checklist ?? [];
        $usersArray = [];
        $teams = $project->teams()
            ->with(['users.skills', 'departments'])
            ->get();

        Log::info('Teams', ['teams' => $teams]);

        if ($teams->isEmpty()) {
            throw new \Exception('No teams found for project: ' . $project->id);
        }

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

        try {
            Log::info('Starting user allocation for project', [
                'project_id' => $project->id,
                'project_name' => $project->name
            ]);

            // Transform data to match Python endpoint's expected format
            $formattedData = [
                'project_id' => $project->id,
                'data_array' => array_map(function ($card) {
                    return [
                        'card_id' => $card['card_id'],
                        'card_name' => $card['card_name'],
                        'card_due_date' => $card['card_due_date'] ? date('Y-m-d', strtotime($card['card_due_date'])) : date('Y-m-d'),
                        'card_description' => $card['card_description'] ?? '',
                        'checklists' => array_map(function ($checklist) {
                            return [
                                'checklist_id' => $checklist['checklist_id'],
                                'checklist_name' => $checklist['checklist_name'],
                                'check_items' => array_map(function ($item) {
                                    return [
                                        'check_item_id' => $item['check_item_id'],
                                        'check_item_name' => $item['check_item_name'],
                                        'due_date' => $item['due_date'] ? date('Y-m-d', strtotime($item['due_date'])) : date('Y-m-d'),
                                        'status' => $item['status'] ?? 'incomplete'
                                    ];
                                }, $checklist['check_items'])
                            ];
                        }, $card['checklists'])
                    ];
                }, $dataArray)
            ];

            Log::info('Formatted data for Python service', [
                'formatted_data' => $formattedData
            ]);

            $response = $this->python_service->allocateUserToTask(
                $project->id,
                $formattedData,
                $usersArray
            );

            if (isset($response['error'])) {
                throw new \Exception('User allocation failed: ' . $response['error']);
            }

            // Update user_id values in the original data structure
            if (isset($response['allocation'])) {
                foreach ($response['allocation'] as $checklistId => $checklistData) {
                    foreach ($checklistData['check_items'] as $allocatedItem) {
                        foreach ($dataArray as &$card) {
                            foreach ($card['checklists'] as &$checklist) {
                                if ($checklist['checklist_id'] === $checklistId) {
                                    foreach ($checklist['check_items'] as &$checkItem) {
                                        if ($checkItem['check_item_id'] === $allocatedItem['check_item_id']) {
                                            $checkItem['user_id'] = $allocatedItem['user_id'];

                                            // Send notification to the allocated user
                                            if ($allocatedItem['user_id']) {
                                                $user = User::find($allocatedItem['user_id']);
                                                if ($user) {
                                                    Notification::make()
                                                        ->success()
                                                        ->title('New Task Assignment')
                                                        ->body('You have been assigned to task: ' . $checkItem['check_item_name'])
                                                        ->sendToDatabase($user);
                                                }
                                            }
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                // Save the updated data back to the database
                $checklistUser->user_checklist = $dataArray;
                $checklistUser->save();

                Log::info('Updated ChecklistUser with allocated users', [
                    'project_id' => $project->id
                ]);
            }

            Log::info('User allocation completed successfully', [
                'project_id' => $project->id,
                'response' => $response
            ]);

            return $response;
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

    public function updateTrelloBoard(Project $project)
    {
        $this->trello_service->updateBoard($project->trello_board_id, [
            'name' => $project->name,
        ]);

        $createOrUpdateCard = function ($listId, $cardName, $cardData) {
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

        $detailsList = $this->trello_service->getBoardListByName(
            $project->trello_board_id,
            'Project details'
        );
        $coordinatorList = $this->trello_service->getBoardListByName(
            $project->trello_board_id,
            'Coordinators'
        );

        if ($coordinatorList) {
            Log::info('Updating Coordinator list.');

            $coordinatorMappings = [
                'groom_coordinator' => ['card_name' => 'groom coordinator', 'relation' => 'groomCoordinator'],
                'bride_coordinator' => ['card_name' => 'bride coordinator', 'relation' => 'brideCoordinator'],
                'head_coordinator' => ['card_name' => 'head coordinator', 'relation' => 'headCoordinator'],
                'head_coor_assistant' => ['card_name' => 'head coordinator assistant', 'relation' => 'headAssistant'],
                'bride_coor_assistant' => ['card_name' => 'bride coordinator assistant', 'relation' => 'brideAssistant'],
                'groom_coor_assistant' => ['card_name' => 'groom coordinator assistant', 'relation' => 'groomAssistant'],
            ];

            foreach ($coordinatorMappings as $field => $config) {
                if ($project->$field) {
                    try {
                        $relation = $config['relation'];
                        $name = $project->$relation->name;
                        $createOrUpdateCard($coordinatorList['id'], $config['card_name'], ['desc' => $name]);
                        Log::info("Updated {$config['card_name']} card", ['name' => $name]);
                    } catch (\Exception $e) {
                        Log::error("Failed to update {$config['card_name']} card", [
                            'error' => $e->getMessage(),
                            'field' => $field
                        ]);
                    }
                }
            }
        }

        if ($detailsList) {
            Log::info('Updating Project details list.');

            $coupleCardId = $createOrUpdateCard(
                $detailsList['id'],
                'name of couple',
                ['desc' => "{$project->groom_name} & {$project->bride_name}", 'due' => $project->end]
            );
            $createOrUpdateCard($detailsList['id'], 'package', ['desc' => $project->package->name]);
            $createOrUpdateCard($detailsList['id'], 'description', ['desc' => $project->description]);
            $createOrUpdateCard($detailsList['id'], 'venue of wedding', ['desc' => $project->venue]);
            $createOrUpdateCard($detailsList['id'], 'wedding theme color', ['desc' => $project->theme_color]);
            $createOrUpdateCard($detailsList['id'], 'special request', ['desc' => $project->special_request]);

            if ($project->thumbnail_path && $coupleCardId) {
                Log::info('Updating thumbnail on the couple name card.');
                $this->trello_service->addAttachmentToCard($coupleCardId, $project->thumbnail_path);
            }
        }
    }
}
