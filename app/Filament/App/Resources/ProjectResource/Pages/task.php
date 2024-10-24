<?php

namespace App\Filament\App\Resources\ProjectResource\Pages;

use App\Filament\App\Resources\ProjectResource;
use Filament\Resources\Pages\Page;

class task extends Page
{
    protected static string $resource = ProjectResource::class;

    protected static string $view = 'filament.app.resources.project-resource.pages.task';
}
