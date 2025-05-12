<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use Filament\Actions;
use App\Models\Project;
use App\Services\ProjectService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\ProjectResource;
use Filament\Actions\Action;
use Illuminate\Support\Facades\URL;

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
                    // if ($record->trello_board_id) {
                    //     app(ProjectService::class)->markAsDone($record->trello_board_id);
                    // }
                    Notification::make()
                        ->title('Project marked as done')
                        ->success()
                        ->send();
                })
                ->requiresConfirmation()
                ->color('info')
                ->icon('heroicon-o-check')
                ->visible(fn($record) => $record->trashed() === false),

            Actions\RestoreAction::make()
                ->label('Restore')
                ->action(function ($record) {
                    $record->restore();

                    $record->update([
                        'status' => config('project.project_status.active'),
                    ]);

                    Notification::make()
                        ->title('Project restored')
                        ->success()
                        ->send();
                })
                ->requiresConfirmation()
                ->color('success')
                ->visible(fn($record) => $record->trashed() === true),

            Action::make('downloadPdf')
                ->label('Download PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(fn($record) => route('projects.exportPdf', $record->id))
                ->openUrlInNewTab()
                ->color('secondary'),

            Actions\EditAction::make()
                ->visible(fn($record) => $record->trashed() === false),
        ];
    }

    protected function resolveRecord($key): \Illuminate\Database\Eloquent\Model
    {
        return Project::withTrashed()->findOrFail($key);
    }
}
