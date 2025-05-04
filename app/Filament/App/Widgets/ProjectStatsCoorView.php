<?php

namespace App\Filament\App\Widgets;

use Dom\Text;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Project;
use Filament\Tables\Columns\TextColumn;

class ProjectStatsCoorView extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    public function table(Table $table): Table
    {
        return $table
             ->query(
                Project::query()->forUser(Auth::user())
             )
            ->columns([
                TextColumn::make('name')
                    ->label('Project Name')
                    ->url(fn ($record): string => \App\Filament\App\Resources\ProjectResource::getUrl('edit', ['record' => $record]))
                    ->searchable(),
                TextColumn::make('end')
                    ->label('Event Date')
                    ->date('F j, Y'),

            ]);
    }
}
