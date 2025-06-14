<?php

namespace App\Filament\App\Resources\ProjectResource\Widgets;

use App\Models\User;
use App\Models\UserTask;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Enums\PriorityLevel;
use App\Livewire\ProjectTask;
use Filament\Infolists\Infolist;
use App\Events\TaskStatusUpdated;
use Filament\Tables\Actions\Action;
use App\Services\ProjectTaskService;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Infolists\Components\Grid;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\Stack;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use App\Filament\App\Resources\ProjectResource;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry\TextEntrySize;
use Filament\Notifications\Actions\Action as NotificationAction;

class ProjectTaskTable extends BaseWidget
{
    public $project;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Event Tasks';

    protected static string $id = 'tasks-table';

    protected static ?string $recordTitleAttribute = 'task_name';

    protected static bool $shouldCheckAccess = true;

    public static function getGlobalSearchEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getGlobalSearchEloquentQuery()->where('project_id', request()->route('record'));
    }

    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        return [
            'Task' => $record->task_name,
            'Department' => $record->card_name,
            'Due Date' => $record->due_date?->format('M d, Y'),
            'Status' => ucfirst($record->status),
            'Priority' => $record->priority_level?->value,
            'Assigned To' => $record->users?->name,
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['task_name', 'card_name', 'status', 'users.name'];
    }

    public function table(Table $table): Table
    {
        $query = UserTask::forUser(Auth::user()->id)
            ->where('project_id', $this->project->id);

        return $table
            ->headerActions([
                Action::make('createTask')
                    ->label('Create Task')
                    ->icon('heroicon-o-plus')
                    ->requiresConfirmation()
                    ->visible(fn() => optional(Auth::user())->hasRole('Coordinator'))
                    ->form(fn(Form $form): Form => $this->form($form))
                    ->slideOver()
                    ->action(function (array $data) {
                        $service = app(ProjectTaskService::class);

                        UserTask::create(array_merge($data, ['project_id' => $this->project->id]));

                        $service->createTask($this->project->id, $data['card_name'], $data['task_name'], $data['due_date']);

                        Notification::make()
                            ->title('New task has been assigned to you')
                            ->body('The task: "' . $data['task_name'] . '" has been created, and is assigned to you.')
                            ->success()
                            ->actions([
                                NotificationAction::make('view')
                                    ->label('View Task')
                                    ->icon('heroicon-o-eye')
                                    ->url(ProjectResource::getUrl('task', ['record' => $this->project->id]))
                            ])
                            ->sendToDatabase(User::find($data['user_id']));

                        Notification::make()
                            ->title('Task Created')
                            ->body('The task has been Created.')
                            ->success()
                            ->send();
                    }),
            ])
            ->query($query)
            ->columns([
                TextColumn::make('card_name')
                    ->label('Department')
                    ->searchable()
                    ->toggleable()
                    ->visible(fn() => optional(Auth::user())->hasRole('Coordinator'))
                    ->sortable(),
                TextColumn::make('task_name')
                    ->label('Task')
                    ->limit(25)
                    ->searchable(),
                TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('users.name')
                    ->label('Assigned To')
                    ->toggleable()
                    ->default('no assigned user')
                    ->badge()
                    ->color(fn ($state) => $state === 'no assigned user' ? 'danger' : 'gray')
                    ->searchable()
                    ->sortable(),
                // SelectColumn::make('priority_level')
                //     ->label('Priority')
                //     ->options([
                //         PriorityLevel::P0->value => 'P0',
                //         PriorityLevel::P1->value => 'P1',
                //         PriorityLevel::P2->value => 'P2',
                //     ])
                //     ->sortable()
                //     ->searchable()
                //     ->visible(fn() => optional(Auth::user())->hasRole('Coordinator'))
                //     ->sortable(),
                TextColumn::make('priority_level')
                    ->label('Priority')
                    ->getStateUsing(function (UserTask $record): string {
                        return $record->priority_level?->value ?? '';
                    })
                    // ->visible(fn() => optional(Auth::user())->hasAnyRole(['Team Leader', 'Member']))
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color(fn($state): string => match ($state) {
                        PriorityLevel::P0->value => 'danger',
                        PriorityLevel::P1->value => 'warning',
                        PriorityLevel::P2->value => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->searchable()
                    ->color(fn(string $state): string => match ($state) {
                        'incomplete' => 'warning',
                        'complete' => 'success',
                        'pending' => 'info',
                        default => 'gray',
                    })
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                ImageColumn::make('attachment')
                    ->label('Attachment')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->stacked()
                    ->circular()
                    ->limit(3)
                    ->limitedRemainingText()
                    ->state(function (UserTask $record): array {
                        if (!$record->attachment) {
                            return [];
                        }

                        $images = [];
                        foreach ((array) $record->attachment as $attachment) {
                            if (isset($attachment['attachment'])) {
                                $images[] = $attachment['attachment'];
                            }
                        }

                        return $images;
                    }),
                TextColumn::make('approvedBy.name')
                    ->label('Approved By')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('card_name')
                    ->label('Department')
                    ->options(UserTask::forUser(Auth::user()->id)
                        ->where('project_id', $this->project->id)
                        ->whereNotNull('card_name')
                        ->distinct()
                        ->pluck('card_name', 'card_name'))
                    ->visible(function () {
                        if (!Auth::check()) return false;
                        return Auth::user()->roles->where('name', 'Coordinator')->count() > 0;
                    }),
                SelectFilter::make('status')
                    ->label('Status')
                    ->default('incomplete')
                    ->options([
                        'incomplete' => 'Incomplete',
                        'pending' => 'Pending',
                        'complete' => 'Completed',
                    ]),
                SelectFilter::make('priority_level')
                    ->label('Priority')
                    ->options(PriorityLevel::class),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns([
                'default' => 3,
                'md' => 3,
                'lg' => 3,
                'xl' => 3,
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('submitAsComplete')
                        ->requiresConfirmation()
                        ->label('Submit')
                        ->color('success')
                        ->icon('heroicon-o-check-circle')
                        ->tooltip('Submit your completed task with attachments for approval')
                        ->slideOver()
                        ->form([
                            Repeater::make('attachment')
                                ->label('Attachment')
                                ->addActionLabel('Add more attachment')
                                ->schema([
                                    TextInput::make('description')
                                        ->label('Remarks')
                                        ->helperText('Add remarks to explain the purpose of this attachment'),
                                    FileUpload::make('attachment')
                                        ->label('Attachment')
                                        ->required()
                                        ->helperText('Upload a file related to your task completion'),
                                ]),
                        ])
                        ->action(function (UserTask $record, array $data) {
                            $record->update([
                                'status' => 'pending',
                                'attachment' => $data['attachment'] ?? $record->attachment,
                            ]);

                            $coordinatorUsers = collect();
                            if ($this->project->head_coordinator) {
                                $coordinatorUsers->push(User::find($this->project->head_coordinator));
                            }
                            if ($this->project->head_coor_assistant) {
                                $coordinatorUsers->push(User::find($this->project->head_coor_assistant));
                            }
                            if ($this->project->groom_coordinator) {
                                $coordinatorUsers->push(User::find($this->project->groom_coordinator));
                            }
                            if ($this->project->bride_coordinator) {
                                $coordinatorUsers->push(User::find($this->project->bride_coordinator));
                            }
                            if ($this->project->groom_coor_assistant) {
                                $coordinatorUsers->push(User::find($this->project->groom_coor_assistant));
                            }
                            if ($this->project->bride_coor_assistant) {
                                $coordinatorUsers->push(User::find($this->project->bride_coor_assistant));
                            }
                            $coordinatorTeams = $this->project->coordinationTeam()->with('users')->get();
                            foreach ($coordinatorTeams as $team) {
                                $coordinatorUsers = $coordinatorUsers->merge($team->users);
                            }

                            $coordinatorUsers = $coordinatorUsers->filter()->unique('id')->values();

                            foreach ($coordinatorUsers as $coordinator) {
                                if ($coordinator) {
                                    Notification::make()
                                        ->title('New Pending Task for Approval')
                                        ->body('A Task from "' . $this->project->name . '" has been submitted as Completed and is pending for approval.')
                                        ->info()
                                        ->actions([
                                            NotificationAction::make('view')
                                                ->label('View Task')
                                                ->icon('heroicon-o-eye')
                                                ->url(ProjectResource::getUrl('task', ['record' => $this->project->id]))
                                        ])
                                        ->sendToDatabase($coordinator);
                                }
                            }
                            event(new TaskStatusUpdated($record->project_id));
                            Notification::make()
                                ->title('Task Submitted')
                                ->body('The task has been submitted.')
                                ->success()
                                ->send();
                        }),
                    EditAction::make()
                        ->label('Edit')
                        ->color('warning')
                        ->icon('heroicon-m-pencil-square')
                        ->tooltip('Edit task details like name, due date, and priority')
                        ->modalHeading('Edit Task')
                        ->modalWidth('lg')
                        ->modalHeading('Edit the task details')
                        ->modalSubmitActionLabel('Update Task')
                        ->visible(fn() => optional(Auth::user())->hasAnyRole(['Coordinator', 'Team Leader']))
                        ->requiresConfirmation()
                        ->form(fn(Form $form): Form => $this->form($form))
                        ->slideOver()
                        ->fillForm(function (UserTask $record): array {
                            return [
                                'task_name' => $record->task_name,
                                'due_date' => $record->due_date,
                                'user_id' => $record->user_id,
                                'status' => $record->status,
                                'attachment' => $record->attachment,
                                'priority_level' => $record->priority_level,
                                'card_name' => $record->card_name,
                                'approved_by' => $record->approved_by,
                            ];
                        })
                        ->action(function (UserTask $record, array $data): UserTask {
                            if ($data['status'] === 'incomplete') {
                                if ($record->status === 'complete') {
                                    $service = app(ProjectTaskService::class);
                                    $service->changeStatus($record->check_item_id, $record->card_id, 'incomplete');
                                }
                            } elseif ($data['status'] === 'complete') {
                                $service = app(ProjectTaskService::class);
                                $service->changeStatus($record->check_item_id, $record->card_id, 'complete');
                            } elseif ($data['status'] === 'pending') {
                                if ($record->status === 'complete') {
                                    $service = app(ProjectTaskService::class);
                                    $service->changeStatus($record->check_item_id, $record->card_id, 'incomplete');
                                }
                            }

                            if ($data['user_id'] !== $record->user_id) {
                                Notification::make()
                                    ->title('A task has been assigned to you')
                                    ->body('The task has been updated, and is assigned to you.')
                                    ->success()
                                    ->actions([
                                        NotificationAction::make('view')
                                            ->label('View Task')
                                            ->icon('heroicon-o-eye')
                                            ->url(ProjectResource::getUrl('task', ['record' => $this->project->id]))
                                    ])
                                    ->sendToDatabase(User::find($data['user_id']));
                            }

                            $record->update($data);
                            Notification::make()
                                ->title('Task Updated')
                                ->body('The task has been updated.')
                                ->success()
                                ->send();
                            return $record;
                        }),
                    Action::make('updateDueDate')
                        ->label('Update Due Date')
                        ->color('warning')
                        ->icon('heroicon-m-calendar')
                        ->tooltip('Change the task due date and add remarks for the change')
                        ->modalHeading('Edit Task')
                        ->modalWidth('lg')
                        ->form([
                            DatePicker::make('due_date')
                                ->label('Due Date')
                                ->required(),
                            TextInput::make('remarks')
                                ->label('Remarks or Reason'),
                        ])
                        ->action(function (UserTask $record, array $data) {
                            $record->updateDueHistories()->create([
                                'old_due_date' => $record->due_date,
                                'new_due_date' => $data['due_date'],
                                'user_id' => Auth::user()->id,
                                'remarks' => $data['remarks'],
                            ]);
                            $record->update([
                                'due_date' => $data['due_date'],
                            ]);
                            Notification::make()
                                ->title('Due Date Updated')
                                ->body('The due date has been updated.')
                                ->success()
                                ->send();
                        }),
                    ViewAction::make()
                        ->label('View')
                        ->color('primary')
                        ->icon('heroicon-m-eye')
                        ->tooltip('View complete task details including attachments and history')
                        ->infolist(fn(UserTask $record) => [
                            Grid::make(2)
                                ->schema([
                                    Fieldset::make('Task Information')
                                        ->schema([
                                            Split::make([
                                                TextEntry::make('task_name')
                                                    ->label('Task')
                                                    ->size(TextEntry\TextEntrySize::Large),
                                                TextEntry::make('card_name')
                                                    ->label('Department')
                                                    ->badge(),
                                            ]),
                                            TextEntry::make('due_date')
                                                ->label('Due Date')
                                                ->date(),
                                        ]),
                                    Fieldset::make('Status Information')
                                        ->schema([
                                            TextEntry::make('priority_level')
                                                ->label('Priority')
                                                ->badge()
                                                ->color(function ($state): string {
                                                    return match ($state?->value) {
                                                        PriorityLevel::P0->value => 'danger',
                                                        PriorityLevel::P1->value => 'warning',
                                                        PriorityLevel::P2->value => 'info',
                                                        default => 'gray',
                                                    };
                                                }),
                                            TextEntry::make('status')
                                                ->label('Status')
                                                ->badge()
                                                ->color(function (string $state): string {
                                                    return match ($state) {
                                                        'incomplete' => 'warning',
                                                        'complete' => 'success',
                                                        'pending' => 'info',
                                                        default => 'gray',
                                                    };
                                                }),
                                        ]),
                                    Fieldset::make('Assignment Information')
                                        ->schema([
                                            Split::make([
                                                ImageEntry::make('users.avatar_url')
                                                    ->label('Avatar')
                                                    ->circular(),
                                                TextEntry::make('users.name')
                                                    ->label('Assigned To'),
                                            ]),
                                            TextEntry::make('approvedBy.name')
                                                ->label('Approved By'),
                                        ]),
                                    Fieldset::make('Attachments')
                                        ->schema([
                                            RepeatableEntry::make('attachment')
                                                ->label('')
                                                ->schema([
                                                    Split::make([
                                                        ImageEntry::make('attachment')
                                                            ->label('')
                                                            ->width('full')
                                                            ->height(150)
                                                            ->width(150)
                                                            ->extraImgAttributes(['class' => 'rounded-md w-full']),
                                                        TextEntry::make('description')
                                                            ->label('Description'),
                                                    ]),
                                                ])->grid(2)->columnSpan('full'),
                                        ]),
                                    Fieldset::make('Additional Information')
                                        ->schema([
                                            Grid::make(2)
                                                ->schema([
                                                    TextEntry::make('created_at')
                                                        ->label('Created At')
                                                        ->dateTime(),
                                                    TextEntry::make('updated_at')
                                                        ->label('Updated At')
                                                        ->dateTime(),
                                                ]),
                                            
                                        ]),
                                    Fieldset::make('Due Date Update History')
                                            ->schema([
                                                RepeatableEntry::make('updateDueHistories')
                                                    ->label('')
                                                    ->schema([
                                                        Grid::make(2)
                                                            ->schema([
                                                                TextEntry::make('old_due_date')
                                                                    ->label('Previous Due Date')
                                                                    ->date()
                                                                    ->badge()
                                                                    ->color('warning'),
                                                                TextEntry::make('new_due_date')
                                                                    ->label('New Due Date')
                                                                    ->date()
                                                                    ->badge()
                                                                    ->color('success'),
                                                            ]),
                                                        Grid::make(1)
                                                            ->schema([
                                                                TextEntry::make('remarks')
                                                                    ->label('Remarks')
                                                                    ->markdown(),
                                                                Grid::make(2)
                                                                    ->schema([
                                                                        TextEntry::make('user.name')
                                                                            ->label('Updated By')
                                                                            ->badge(),
                                                                        TextEntry::make('created_at')
                                                                            ->label('Updated At')
                                                                            ->dateTime()
                                                                            ->badge()
                                                                            ->color('gray'),
                                                                    ]),
                                                            ]),
                                                    ])
                                                    ->columns(1)
                                                    ->columnSpan('full'),
                                            ]),
                                ]),
                        ]),
                    DeleteAction::make('delete')
                        ->label('Delete')
                        ->color('danger')
                        ->icon('heroicon-m-trash')
                        ->tooltip('Remove this task from the project')
                        ->requiresConfirmation()
                        ->visible(fn() => optional(Auth::user())->hasRole('Coordinator'))
                        ->action(function (UserTask $record) {
                            $service = app(ProjectTaskService::class);

                            try {
                                $service->deleteTask($record->card_id, $record->check_item_id);
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Error Deleting Task to trello')
                                    ->body('An error occurred while deleting the task to trello.')
                                    ->danger()
                                    ->send();
                            }

                            $record->delete();
                            Notification::make()
                                ->title('Task Deleted')
                                ->body('The task has been deleted.')
                                ->success()
                                ->send();
                        }),
                ])
                ->tooltip('Access task action: Submit, Edit, Update Due Date, View, and Delete')
            ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('task_name')
                ->label('Task')
                ->required()
                ->disabled(fn() => optional(Auth::user())->hasAnyRole(['Team Leader', 'Member'])),
            Select::make('card_name')
                ->label('Department')
                ->required()
                ->options(UserTask::forUser(Auth::user()->id)
                    ->where('project_id', $this->project->id)
                    ->whereNotNull('card_name')
                    ->distinct()
                    ->pluck('card_name', 'card_name'))
                ->visible(fn() => optional(Auth::user())->hasRole('Coordinator')),
            DatePicker::make('due_date')
                ->label('Due Date')
                ->required()
                ->disabled(fn() => optional(Auth::user())->hasAnyRole(['Team Leader', 'Member'])),
            Select::make('user_id')
                ->searchable()
                ->preload()
                // ->relationship('users', 'name')
                ->label('Assign To')
                ->options(function ($get) {
                    $selectedDepartment = $get('card_name');
                    if (!$selectedDepartment) {
                        return [];
                    }

                    // $users = collect();

                    // // if ($selectedDepartment === 'Coordination') {
                    // //     $coordinatorIds = collect([
                    // //         $this->project->head_coordinator,
                    // //         $this->project->groom_coordinator,
                    // //         $this->project->bride_coordinator,
                    // //         $this->project->head_coor_assistant,
                    // //         $this->project->groom_coor_assistant,
                    // //         $this->project->bride_coor_assistant
                    // //     ])->filter()->unique();

                    // //     if ($coordinatorIds->isNotEmpty()) {
                    // //         $users = $users->merge(
                    // //             \App\Models\User::whereIn('id', $coordinatorIds)
                    // //                 ->pluck('name', 'id')
                    // //         );
                    // //     }
                    // // }
                    $teamUsers = $this->project->teams()
                        ->whereHas('departments', function ($query) use ($selectedDepartment) {
                            $query->where('name', $selectedDepartment);
                        })
                        ->with('users')
                        ->get()
                        ->pluck('users')
                        ->flatten()
                        ->unique('id')
                        ->pluck('name', 'id')
                        ->toArray();

                    return $teamUsers;
                })
                ->required()
                ->disabled(fn() => optional(Auth::user())->hasRole('Member')),
            Select::make('priority_level')
                ->label('Priority')
                ->options(PriorityLevel::class)
                ->required()
                ->disabled(fn() => optional(Auth::user())->hasAnyRole(['Team Leader', 'Member'])),
            Select::make('status')
                ->label('Status')
                ->default('incomplete')
                ->options([
                    'incomplete' => 'Incomplete',
                    'pending' => 'Pending',
                    'complete' => 'Completed',
                ])
                ->disabled(fn() => optional(Auth::user())->hasAnyRole(['Team Leader', 'Member']))
                ->required(),
            Repeater::make('attachment')
                ->label('Attachment')
                ->disabled(fn() => optional(Auth::user())->hasAnyRole(['Team Leader', 'Member']))
                ->schema([
                    TextInput::make('description')
                        ->label('Remarks'),
                    FileUpload::make('attachment')
                        ->label('File'),
                ]),
        ]);
    }
}
