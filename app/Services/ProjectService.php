<?php 

namespace App\Services;

use App\Events\TrelloBoardCreatedEvent;
use Exception;
use App\Models\Project;
use App\Services\PythonService;
use App\Services\TrelloService;
use Illuminate\Support\Facades\Log;


class ProjectService
{
    private TrelloService $trello_service;
    private PythonService $python_service;  
    protected $project;  

    public function __construct(
        TrelloService $trello_service,
        PythonService $python_service,
        Project $project
        )
    {
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
            TrelloBoardCreatedEvent::dispatch($project);
        }
    }

    public function createSpecialRequest(Project $project)
    {
        if (!empty($project->special_request)) {
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
        } else {
            Log::error('there is no special request');
        }
    }

}