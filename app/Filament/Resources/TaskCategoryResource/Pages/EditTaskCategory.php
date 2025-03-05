<?php

namespace App\Filament\Resources\TaskCategoryResource\Pages;

use App\Filament\Resources\TaskCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTaskCategory extends EditRecord
{
    protected static string $resource = TaskCategoryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
