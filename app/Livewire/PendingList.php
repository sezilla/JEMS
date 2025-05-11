<?php

namespace App\Livewire;

use Filament\Tables;
use Livewire\Component;
use App\Models\UserTask;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Contracts\HasTable;
use Filament\Support\Contracts\TranslatableContentDriver;
use Filament\Forms\Contracts\HasForms;

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
        if (!$user->hasRole(config('filament-shield.coordinator_user.name'))) {
            abort(403, 'Unauthorized action.');
        }

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
                Tables\Columns\TextColumn::make('task_name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('users.name')
                    ->label('Assigned To'),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->action(function ($record) {
                        $record->update(['status' => 'approved']);
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->action(function ($record) {
                        $record->update(['status' => 'rejected']);
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
