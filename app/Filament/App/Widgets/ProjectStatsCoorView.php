<?php

namespace App\Filament\App\Widgets;

use Dom\Text;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Project;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Forms;

class ProjectStatsCoorView extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 2;
    protected static ?string $heading = 'Projects assigned to you';
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
                    ->label('Wedding Date')
                    ->date('F j, Y'),

            ])
            ->paginated([5])
            ->filters([
                Filter::make('wedding_date_range')
                    ->form([
                        DatePicker::make('wedding_from')
                            ->label('From Date')
                            ->displayFormat('M d, Y')
                            ->native(false)
                            ->closeOnDateSelection()
                            ->firstDayOfWeek(1)
                            ->placeholder('Select start date'),
                        DatePicker::make('wedding_until')
                            ->label('Until Date')
                            ->displayFormat('M d, Y')
                            ->native(false)
                            ->closeOnDateSelection()
                            ->firstDayOfWeek(1)
                            ->placeholder('Select end date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['wedding_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('end', '>=', $date),
                            )
                            ->when(
                                $data['wedding_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('end', '<=', $date),
                            );
                    }),
            ]);
    }
}
