<?php

namespace App\Filament\App\Resources;

use Carbon\Carbon;
use Filament\Tables;
use App\Models\Package;
use App\Models\Project;
use Filament\Tables\Table;
use App\Enums\ProjectStatus;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\App\Resources\ProjectResource\Pages;

$user = Auth::user();

class ProjectResource extends Resource
{

    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return Project::forUser(Auth::user());
            })
            ->recordUrl(
                fn(Project $record): string => static::getUrl('task', ['record' => $record])
            )
            ->columns([
                Stack::make([
                    Split::make([
                        ImageColumn::make('thumbnail_path')
                            ->disk('public')
                            ->label('Thumbnail')
                            ->width(150)
                            ->height(200)
                            ->extraImgAttributes(['class' => 'rounded-md'])
                            ->defaultImageUrl(url('https://placehold.co/150x200')),
                        Stack::make([
                            TextColumn::make('name')
                                ->label('Names')
                                ->searchable()
                                ->limit(14)
                                ->size(TextColumn\TextColumnSize::Large)
                                ->getStateUsing(function ($record) {
                                    return $record->groom_name . ' & ' . $record->bride_name;
                                }),
                            TextColumn::make('description')
                                ->limit(40)
                                ->searchable()
                                ->placeholder('No description'),
                            Split::make([

                                TextColumn::make('package.name')
                                    ->label('Package')
                                    ->searchable()
                                    ->limit(15)
                                    ->badge()
                                    ->color(
                                        fn(string $state): string => match ($state) {
                                            'Ruby' => 'ruby',
                                            'Garnet' => 'garnet',
                                            'Emerald' => 'emerald',
                                            'Infinity' => 'infinity',
                                            'Sapphire' => 'sapphire',
                                            default => 'gray',
                                        }
                                    ),

                                ColorColumn::make('theme_color')
                                    ->label('Theme Color')
                                    ->copyable()
                                    ->copyMessage('Color code copied')
                                    ->copyMessageDuration(1500),

                                IconColumn::make('status')
                                    ->label('Status')
                                    ->options([
                                        'heroicon-o-clock' => ProjectStatus::ACTIVE,
                                        'heroicon-o-check-circle' => ProjectStatus::COMPLETED,
                                        'heroicon-o-trash-circle' => ProjectStatus::ARCHIVED,
                                        'heroicon-o-x-circle' => ProjectStatus::CANCELLED,
                                        'heroicon-o-pause-circle' => ProjectStatus::ON_HOLD,
                                    ])
                                    ->colors([
                                        'success' => ProjectStatus::COMPLETED,
                                        'warning' => ProjectStatus::ARCHIVED,
                                        'danger' => ProjectStatus::CANCELLED,
                                        'secondary' => ProjectStatus::ON_HOLD,
                                        'primary' => ProjectStatus::ACTIVE,
                                    ])
                                    ->size('sm'),

                            ]),

                            TextColumn::make('venue')
                                ->placeholder('No location'),
                            Split::make([
                                ImageColumn::make('user.avatar_url')
                                    ->tooltip('Event Creator')
                                    ->label('Coordinator')
                                    ->width(20)
                                    ->height(20)
                                    ->circular()
                                    ->grow(false),
                                TextColumn::make('user.name')
                                    ->label('Coordinator')
                                    ->searchable()
                                    ->limit(15),
                            ]),
                            Stack::make([
                                TextColumn::make('start')
                                    ->date()
                                    // ->sortable()
                                    ->formatStateUsing(function ($column, $state) {
                                        return '<span style="font-size: 70%; opacity: 0.7;">' . Carbon::parse($state)->format('m-d-Y') . '</span>';
                                    })
                                    ->html(),
                                TextColumn::make('end')
                                    ->label('Event Date')
                                    ->date()
                                    ->fontFamily(FontFamily::Mono)
                                    ->size(TextColumn\TextColumnSize::Large)
                                    ->alignment(Alignment::Left),
                            ]),

                        ])->space(3),
                    ]),
                    Split::make([
                        Stack::make([
                            TextColumn::make('headCoordinator.name')
                                ->getStateUsing(function ($record) {
                                    return 'Head coor';
                                })
                                ->size(TextColumn\TextColumnSize::ExtraSmall)
                                ->weight(FontWeight::Thin)
                                ->formatStateUsing(function ($column, $state) {
                                    return '<span style="font-size: 70%; opacity: 0.7;">' . $state . '</span>';
                                })
                                ->html(),
                            TextColumn::make('headCoordinator.name')
                                ->label('Head Coordinator')
                                ->searchable()
                                ->badge()
                                ->limit(8),
                        ]),

                        Stack::make([
                            TextColumn::make('brideCoordinator.name')
                                ->getStateUsing(function ($record) {
                                    return 'Bride coor';
                                })
                                ->size(TextColumn\TextColumnSize::ExtraSmall)
                                ->weight(FontWeight::Thin)
                                ->formatStateUsing(function ($column, $state) {
                                    return '<span style="font-size: 70%; opacity: 0.7;">' . $state . '</span>';
                                })
                                ->html(),
                            TextColumn::make('brideCoordinator.name')
                                ->label('Bride Coordinator')
                                ->searchable()
                                ->badge()
                                ->limit(8),
                        ]),

                        Stack::make([
                            TextColumn::make('groomCoordinator.name')
                                ->getStateUsing(function ($record) {
                                    return 'Groom coor';
                                })
                                ->size(TextColumn\TextColumnSize::ExtraSmall)
                                ->weight(FontWeight::Thin)
                                ->formatStateUsing(function ($column, $state) {
                                    return '<span style="font-size: 70%; opacity: 0.7;">' . $state . '</span>';
                                })
                                ->html(),
                            TextColumn::make('groomCoordinator.name')
                                ->label('Groom Coordinator')
                                ->searchable()
                                ->badge()
                                ->limit(8),
                        ]),

                    ]),
                    TextColumn::make('teams.name')
                        ->getStateUsing(function ($record) {
                            return 'Teams';
                        })
                        ->size(TextColumn\TextColumnSize::ExtraSmall)
                        ->weight(FontWeight::Thin)
                        ->formatStateUsing(function ($column, $state) {
                            return '<span style="font-size: 70%; opacity: 0.7;">' . $state . '</span>';
                        })
                        ->html(),
                    ImageColumn::make('teams.image')
                        ->label('Bride Coordinator')
                        ->searchable()
                        ->stacked()
                        ->limit(6)
                        ->circular()
                        ->limitedRemainingText(),
                    // TextColumn::make('start')
                    //     ->label('Date Added')
                    //     ->date()
                    //     ->sortable(),

                ])->space(3),
            ])->defaultSort('end', 'asc')
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
                'sm' => 1,
            ])
            ->paginated([12, 24, 48, 96, 'all'])
            ->filters([
                Tables\Filters\Filter::make('completed')
                    ->label('Completed')
                    ->query(fn(Builder $query): Builder => $query->where('status', config('project.project_status.completed'))),
                Tables\Filters\Filter::make('canceled')
                    ->label('Canceled')
                    ->query(fn(Builder $query): Builder => $query->where('status', config('project.project_status.canceled'))),
                Tables\Filters\Filter::make('on_hold')
                    ->label('On Hold')
                    ->query(fn(Builder $query): Builder => $query->where('status', config('project.project_status.on_hold'))),
                Tables\Filters\TrashedFilter::make()
                    ->label('Deleted')
            ])
            ->actions([
                // ForceDeleteAction::make()
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\ViewAction::make()
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }



    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    public static function canCreate(): bool
    {
        return false; // Disable create functionality
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            // 'view' => Pages\ViewProject::route('/{record}'),
            // 'create' => Pages\CreateProject::route('/create'),
            'task' => Pages\task::route('/{record}'),
            // 'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
