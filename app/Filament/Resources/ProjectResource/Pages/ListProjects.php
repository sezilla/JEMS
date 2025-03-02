<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use Filament\Actions;
use App\Models\Project;
use Filament\Tables\Table;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProjectResource;

class ListProjects extends ListRecords
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'All' => Tab::make()
                ->badge(Project::count()),
    
            'This Week' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->whereBetween('end', [
                        now()->startOfWeek(),
                        now()->endOfWeek()
                    ])
                )
                ->badge(fn () => Project::whereBetween('end', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])->count()),

    
            'This Month' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->whereBetween('end', [
                        now()->startOfMonth(), 
                        now()->endOfMonth()
                    ])
                )
                ->badge(fn () => Project::whereBetween('end', [
                    now()->startOfMonth(), 
                    now()->endOfMonth()
                ])->count()),
    
            'This Year' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->whereBetween('end', [
                        now()->startOfYear(), 
                        now()->endOfYear()
                    ])
                )
                ->badge(fn () => Project::whereBetween('end', [
                    now()->startOfYear(), 
                    now()->endOfYear()
                ])->count()),
        ];
    }
    
}
