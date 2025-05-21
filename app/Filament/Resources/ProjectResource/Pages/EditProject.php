<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use Filament\Actions;
use Predis\Response\Status;
use App\Enums\ProjectStatus;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\ProjectResource;

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
                ->visible(fn($record) => $record->status === ProjectStatus::ACTIVE)
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
            Actions\Action::make('activate')
                ->label('Activate')
                ->visible(fn($record) => $record->status !== ProjectStatus::ACTIVE)
                ->action(function ($record) {
                    $this->authorize('update_project', $record);

                    $record->update([
                        'status' => config('project.project_status.active'),
                    ]);

                    Notification::make()
                        ->title('Project marked as on active')
                        ->success()
                        ->send();
                })
                ->requiresConfirmation()
                ->color('info'),
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
