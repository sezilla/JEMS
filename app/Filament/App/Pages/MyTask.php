<?php

namespace App\Filament\App\Pages;

use App\Models\User;
use Filament\Tables;
use App\Models\UserTask;
use Filament\Pages\Page;
use Filament\Tables\Table;
use App\Enums\PriorityLevel;
use App\Events\TaskStatusUpdated;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Repeater;
use Filament\Infolists\Components\Grid;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Split;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Filters\SelectFilter;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use App\Filament\App\Resources\ProjectResource;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Notifications\Actions\Action as NotificationAction;

class MyTask extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.app.pages.my-task';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                UserTask::query()->where('user_id', Auth::id())
            )

            ->columns([
                TextColumn::make('project.name')
                    ->label('Event')
                    ->url(fn($record): string => ProjectResource::getUrl('task', ['record' => $record->project_id]))
                    ->limit(30)
                    ->searchable()
                    ->sortable(),
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
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('priority_level')
                    ->label('Priority')
                    ->getStateUsing(function (UserTask $record): string {
                        return $record->priority_level?->value ?? '';
                    })
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color(fn($state): string => match ($state) {
                        PriorityLevel::P0->value => 'danger',
                        PriorityLevel::P1->value => 'warning',
                        PriorityLevel::P2->value => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('due_date')
                    ->label('Due Date')
                    ->searchable()
                    ->date()
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
                    ->searchable()
                    ->label('Approved By')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
            ])
            ->filters([
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
            ])
            ->actions([
                ViewAction::make()
                    ->label('View')
                    ->color('primary')
                    ->tooltip('View the task details')
                    ->icon('heroicon-m-eye')
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
                                        TextEntry::make('created_at')
                                            ->label('Created At')
                                            ->dateTime(),
                                        TextEntry::make('updated_at')
                                            ->label('Updated At')
                                            ->dateTime(),
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
                    Action::make('submitAsComplete')
                        ->requiresConfirmation()
                        ->label('Submit')
                        ->color('success')
                        ->tooltip('Submit the task as complete')
                        ->icon('heroicon-o-check-circle')
                        ->slideOver()
                        ->form([
                            Repeater::make('attachment')
                                ->label('Attachment')
                                ->schema([
                                    TextInput::make('description')
                                        ->label('Remarks'),
                                    FileUpload::make('attachment')
                                        ->label('Attachment')
                                        ->required(),
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
                ])
            
            ->emptyStateHeading('No Pending Tasks')
            ->emptyStateDescription('There are no tasks pending for approval at this time.');;
    }
}
