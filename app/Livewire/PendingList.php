<?php

namespace App\Livewire;

use App\Models\User;
use Filament\Tables;
use Livewire\Component;
use App\Models\UserTask;
use Filament\Tables\Table;
use App\Enums\PriorityLevel;
use App\Services\TrelloTask;
use App\Models\ChecklistUser;
use Illuminate\Support\Facades\Auth;
use App\Events\ProjectProgressUpdated;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\Grid;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Infolists\Components\Split;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Support\Contracts\TranslatableContentDriver;

class PendingList extends Component implements HasTable, HasForms
{
    use Tables\Concerns\InteractsWithTable;
    use InteractsWithForms;

    public $project;

    public function makeFilamentTranslatableContentDriver(): ?TranslatableContentDriver
    {
        return null;
    }

    protected $listeners = [
        'echo:project.{project.id},TaskStatusUpdated' => 'refreshTable',
    ];

    public function refreshTable()
    {
        $this->dispatch('refresh');
    }

    public function mount($project)
    {
        $user = Auth::user();
        // if (!$user->hasRole(config('filament-shield.coordinator_user.name'))) {
        //     abort(403, 'Unauthorized action.');
        // }

        $this->project = $project;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                UserTask::query()
                    ->where('status', 'pending')
                    ->where('project_id', $this->project->id)
            )
            ->columns([
                TextColumn::make('card_name')
                    ->label('Department'),
                TextColumn::make('task_name')
                    ->sortable(),
                TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('users.name')
                    ->label('Assigned To'),
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
                ImageColumn::make('attachment')
                    ->label('Attachment')
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
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Submitted At')
                    ->dateTime('F d Y | h:i A'),
            ])
            ->actions([
                ViewAction::make()
                    ->label('View')
                    ->color('primary')
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
                                    ]),
                                Fieldset::make('Attachments')
                                    ->schema([
                                        RepeatableEntry::make('attachment')
                                            ->label('')
                                            ->columnSpan('full')
                                            ->schema([
                                                ImageEntry::make('attachment')
                                                    ->label('')
                                                    ->height('auto')
                                                    ->width('100%')
                                                    ->extraImgAttributes(['class' => 'rounded-md']),
                                                TextEntry::make('description')
                                                    ->label('Remarks'),
                                            ]),
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
                            ])


                    ])
                    ->modalFooterActions([
                        Tables\Actions\Action::make('approve')
                            ->label('Approve')
                            ->requiresConfirmation()
                            ->color('primary')
                            ->icon('heroicon-o-check-circle')
                            ->action(function (UserTask $record) {
                                $data = ChecklistUser::where('project_id', $record->project_id)->first();

                                if ($data) {
                                    try {
                                        $record->update(['status' => 'complete', 'approved_by' => Auth::user()->id]);

                                        $data->user_checklist = array_map(function ($card) use ($record) {
                                            $card['checklists'] = array_map(function ($checklist) use ($record) {
                                                $checklist['check_items'] = array_map(function ($item) use ($record) {
                                                    if ($item['check_item_name'] === $record->task_name) {
                                                        $item['status'] = 'complete';
                                                    }
                                                    return $item;
                                                }, $checklist['check_items'] ?? []);
                                                return $checklist;
                                            }, $card['checklists'] ?? []);
                                            return $card;
                                        }, $data->user_checklist);

                                        $data->update(['user_checklist' => $data->user_checklist]);

                                        app(TrelloTask::class)->completeCheckItemStatus($record->card_id, $record->check_item_id);

                                        Notification::make()
                                            ->title('Task Approved')
                                            ->body('The task has been approved.')
                                            ->success()
                                            ->send();

                                        if ($record->user_id) {
                                            Notification::make()
                                                ->title('Your Task has been Approved')
                                                ->body('The task: ' . $record->task_name . ' has been approved.')
                                                ->success()
                                                ->sendToDatabase(User::find($record->user_id));
                                        }
                                        cache()->forget("project_{$record->project_id}_progress");
                                        event(new ProjectProgressUpdated($record->project_id));
                                    } catch (\Exception $e) {
                                        Notification::make()
                                            ->title('Error')
                                            ->body('Something went wrong during approval. Please contact admin.')
                                            ->danger()
                                            ->send();
                                    }
                                } else {
                                    Notification::make()
                                        ->title('Task Not Found')
                                        ->body('Checklist data not found. Please contact the administrator.')
                                        ->danger()
                                        ->send();
                                }
                            }),
                        Tables\Actions\Action::make('reject')
                            ->label('Reject')
                            ->color('danger')
                            ->icon('heroicon-o-x-circle')
                            ->requiresConfirmation()
                            ->action(function ($record) {
                                $data = ChecklistUser::where('project_id', $this->project->id)
                                    ->first();

                                if ($data) {
                                    $record->update(['status' => 'rejected']);

                                    $data->user_checklist = array_map(function ($card) use ($record) {
                                        $card['checklists'] = array_map(function ($checklist) use ($record) {
                                            $checklist['check_items'] = array_map(function ($item) use ($record) {
                                                if ($item['check_item_name'] === $record->task_name) {
                                                    $item['status'] = 'incomplete';
                                                }
                                                return $item;
                                            }, $checklist['check_items'] ?? []);
                                            return $checklist;
                                        }, $card['checklists'] ?? []);
                                        return $card;
                                    }, $data->user_checklist);

                                    $data->update(['user_checklist' => $data->user_checklist]);

                                    Notification::make()
                                        ->title('Task Rejected')
                                        ->body('The task has been rejected.')
                                        ->danger()
                                        ->send();

                                    if ($record->user_id) {
                                        Notification::make()
                                            ->title('Your Task has been Rejected')
                                            ->body('The task: ' . $record->task_name . ' has been rejected.')
                                            ->danger()
                                            ->sendToDatabase(User::find($record->user_id));
                                    }
                                } else {
                                    Notification::make()
                                        ->title('Task Not Found')
                                        ->body('There seems to be an issue with the task. Please contact the administrator.')
                                        ->danger()
                                        ->send();
                                }
                            }),
                    ]),
                ActionGroup::make([
                    Tables\Actions\Action::make('approve')
                        ->label('Approve')
                        ->requiresConfirmation()
                        ->color('success')
                        ->icon('heroicon-o-check-circle')
                        ->action(function ($record) {

                            // Update ChecklistUser check_item status
                            $data = ChecklistUser::where('project_id', $this->project->id)
                                ->first();

                            if ($data) {

                                try {
                                    $record->update(['status' => 'complete', 'approved_by' => Auth::user()->id]);

                                    $data->user_checklist = array_map(function ($card) use ($record) {
                                        $card['checklists'] = array_map(function ($checklist) use ($record) {
                                            $checklist['check_items'] = array_map(function ($item) use ($record) {
                                                if ($item['check_item_name'] === $record->task_name) {
                                                    $item['status'] = 'complete';
                                                }
                                                return $item;
                                            }, $checklist['check_items'] ?? []);
                                            return $checklist;
                                        }, $card['checklists'] ?? []);
                                        return $card;
                                    }, $data->user_checklist);

                                    $data->update(['user_checklist' => $data->user_checklist]);

                                    $trelloTask = app(TrelloTask::class);

                                    $trelloTask->completeCheckItemStatus($record->card_id, $record->check_item_id);

                                    Notification::make()
                                        ->title('Task Approved')
                                        ->body('The task has been approved.')
                                        ->success()
                                        ->send();
                                    if ($record->user_id) {
                                        Notification::make()
                                            ->title('Your Task has been Approved')
                                            ->body('The task: ' . $record->task_name . ' has been approved.')
                                            ->success()
                                            ->sendToDatabase(User::find($record->user_id));
                                    }
                                    cache()->forget("project_{$record->project_id}_progress");
                                    event(new ProjectProgressUpdated($record->project_id));
                                } catch (\Exception $e) {
                                    Notification::make()
                                        ->title('Task Not Found')
                                        ->body('There seems to be an issue with the task. Please contact the administrator.')
                                        ->danger()
                                        ->send();
                                }
                            } else {
                                Notification::make()
                                    ->title('Task Not Found')
                                    ->body('There seems to be an issue with the task. Please contact the administrator.')
                                    ->danger()
                                    ->send();
                            }
                        }),
                    Tables\Actions\Action::make('reject')
                        ->label('Reject')
                        ->color('danger')
                        ->icon('heroicon-o-x-circle')
                        ->requiresConfirmation()
                        ->action(function ($record) {
                            $data = ChecklistUser::where('project_id', $this->project->id)
                                ->first();

                            if ($data) {
                                $record->update(['status' => 'incomplete']);

                                $data->user_checklist = array_map(function ($card) use ($record) {
                                    $card['checklists'] = array_map(function ($checklist) use ($record) {
                                        $checklist['check_items'] = array_map(function ($item) use ($record) {
                                            if ($item['check_item_name'] === $record->task_name) {
                                                $item['status'] = 'incomplete';
                                            }
                                            return $item;
                                        }, $checklist['check_items'] ?? []);
                                        return $checklist;
                                    }, $card['checklists'] ?? []);
                                    return $card;
                                }, $data->user_checklist);

                                $data->update(['user_checklist' => $data->user_checklist]);

                                Notification::make()
                                    ->title('Task Rejected')
                                    ->body('The task has been rejected.')
                                    ->danger()
                                    ->send();

                                if ($record->user_id) {
                                    Notification::make()
                                        ->title('Your Task has been Rejected')
                                        ->body('The task: ' . $record->task_name . ' has been rejected.')
                                        ->danger()
                                        ->sendToDatabase(User::find($record->user_id));
                                }
                            } else {
                                Notification::make()
                                    ->title('Task Not Found')
                                    ->body('There seems to be an issue with the task. Please contact the administrator.')
                                    ->danger()
                                    ->send();
                            }
                        }),
                ]),
            ])
            ->defaultSort('due_date', 'asc')
            ->paginated([10, 25, 50, 100])
            ->emptyStateHeading('No Pending Tasks')
            ->emptyStateDescription('There are no tasks pending for approval at this time.');
    }

    public function render()
    {
        return view('livewire.pending-list');
    }
}
