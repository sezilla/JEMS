<?php

namespace App\Filament\Resources\TaskCategoryResource\Pages;

use App\Filament\Resources\TaskCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTaskCategory extends CreateRecord
{
    protected static string $resource = TaskCategoryResource::class;
}
