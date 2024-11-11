<?php

namespace App\Filament\Resources\ReactionResource\Pages;

use App\Filament\Resources\ReactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReactions extends ListRecords
{
    protected static string $resource = ReactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
