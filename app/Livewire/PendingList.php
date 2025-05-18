<?php

namespace App\Livewire;

use App\Models\User;
use Filament\Tables;
use Livewire\Component;
use App\Models\UserTask;
use Filament\Tables\Table;
use App\Services\TrelloTask;
use App\Models\ChecklistUser;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Contracts\HasTable;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;
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
                Tables\Columns\TextColumn::make('card_name')
                    ->label('Department'),
                Tables\Columns\TextColumn::make('task_name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('users.name')
                    ->label('Assigned To'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Submitted At')
                    ->dateTime('F d Y | h:i A'),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->requiresConfirmation()
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
                                Notification::make()
                                    ->title('Your Task has been Approved')
                                    ->body('The task: ' . $record->task_name . ' has been approved.')
                                    ->success()
                                    ->sendToDatabase(User::find($record->user_id));
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
