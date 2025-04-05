<?php
 
namespace App\Filament\App\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use App\Filament\Widgets\EmployeeStats;
use App\Filament\Widgets\UsersLineChart;


class Dashboard extends \Filament\Pages\Dashboard
{
    protected function getHeaderActions(): array
    {
        return [
            Action::make('action')
                ->label('Try')
                ->form([
                    TextInput::make('name')
                        ->required()
                        ->autofocus(),
                ])
                ->action(function (array $data) {
                    $recipient = auth()->user();

                    // Send a notification to the current user
                    Notification::make()
                        ->title('Saved successfully')
                        ->success()
                        ->body('Your data has been saved successfully.')
                        ->sendToDatabase($recipient);
                }),
        ];
    }

    
}