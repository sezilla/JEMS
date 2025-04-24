<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\ProjectResource;
use App\Models\Project;

class ViewProject extends ViewRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('markAsDone')
                ->label('Mark as Done')
                ->action(function ($record) {
                    $this->authorize('update_project', $record);

                    $record->update([
                        'status' => config('project.project_status.completed'),
                    ]);

                    Notification::make()
                        ->title('Project marked as done')
                        ->success()
                        ->send();
                })
                ->requiresConfirmation()
                ->color('info')
                ->icon('heroicon-o-check')
                ->visible(fn($record) => $record->trashed() === false),

            Actions\EditAction::make()
                ->visible(fn($record) => $record->trashed() === false),

            Actions\RestoreAction::make()


        ];
    }
    protected function resolveRecord($key): \Illuminate\Database\Eloquent\Model
    {
        return Project::withTrashed()->findOrFail($key);
    }
}
