<?php

namespace App\Filament\Resources\ReactionResource\Pages;

use App\Filament\Resources\ReactionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReaction extends EditRecord
{
    protected static string $resource = ReactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
