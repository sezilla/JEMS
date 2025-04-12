<?php

namespace App\Filament\Resources\TaskCategoryResource\Pages;

use App\Filament\Resources\TaskCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTaskCategories extends ViewRecord
{
    protected static string $resource = TaskCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
