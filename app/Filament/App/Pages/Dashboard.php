<?php
 
namespace App\Filament\App\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Illuminate\Notifications\Notification;
 
class Dashboard extends \Filament\Pages\Dashboard
{
    protected function getHeaderActions(): array
    {
        return [
            Action::make('action')
                ->label('try')
                ->form([
                    TextInput::make('name')
                        ->required()
                        ->autofocus(),
                ])
                ->action(function (array $data){
                    $recipient = auth()->user();
 
                    $recipient->notify(
                        Notification::make()
                            ->title('Saved successfully')
                            ->toDatabase(),
                    );
                }),
            ];
    }
}