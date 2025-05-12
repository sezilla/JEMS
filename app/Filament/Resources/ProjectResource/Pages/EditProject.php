<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use Filament\Actions;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\ProjectResource;
use Predis\Response\Status;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('onHold')
                ->label('On hold')
                ->action(function ($record) {
                    $this->authorize('update_project', $record);

                    $record->update([
                        'status' => config('project.project_status.on_hold'),
                    ]);

                    Notification::make()
                        ->title('Project marked as on hold')
                        ->success()
                        ->send();
                })
                ->requiresConfirmation()
                ->color('secondary'),
            DeleteAction::make('cancelProject')
                ->label('Cancel')
                ->action(function ($record) {

                    $record->update([
                        'status' => config('project.project_status.canceled'),
                    ]);

                    $record->delete();

                    Notification::make()
                        ->title('Project marked as canceled')
                        ->success()
                        ->send();
                })
                ->requiresConfirmation()
                ->color('warning'),

            DeleteAction::make()
                ->action(function ($record) {

                    $record->update([
                        'status' => config('project.project_status.archived'),
                    ]);

                    $record->delete();

                    Notification::make()
                        ->title('Project Archived')
                        ->success()
                        ->send();
                })
                ->icon('heroicon-s-trash')
                ->requiresConfirmation()
        ];
    }
}
