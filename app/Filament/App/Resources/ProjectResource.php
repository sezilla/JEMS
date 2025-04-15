<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\ProjectResource\Pages;
use App\Filament\App\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Package;
use App\Models\User;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Auth;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\ColorPicker;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\FontFamily;
use Illuminate\Support\Facades\Date;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Tables\Columns\ColorColumn;

$user = Auth::user();

class ProjectResource extends Resource
{
    
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function table(Table $table): Table
    {
        return $table
        ->query(Project::forUser(Auth::user()))
        ->columns([
            Stack::make([
                Split::make([
                    ImageColumn::make('thumbnail_path')
                        ->disk('public')
                        ->label('Thumbnail')
                        ->width(150)
                        ->height(200)
                        ->extraImgAttributes(['class' => 'rounded-md']),
                    Stack::make([
                        TextColumn::make('groom_name')
                            ->label('Names')
                            ->searchable()
                            ->size(TextColumn\TextColumnSize::Large)
                            ->getStateUsing(function ($record) {
                                return $record->groom_name . ' & ' . $record->bride_name;
                            }),
                        TextColumn::make('name')
                            ->searchable(),
                        Split::make([
                            TextColumn::make('package.name')
                                ->label('Package')
                                ->searchable()
                                ->limit(15)
                                ->badge()
                                ->color(
                                    fn (string $state): string => match ($state) {
                                        'Ruby' => 'ruby',
                                        'Garnet' => 'garnet',
                                        'Emerald' => 'emerald',
                                        'Infinity' => 'infinity',
                                        'sapphire' => 'sapphire',
                                        default => 'gray',
                                    }
                                ),
                            ColorColumn::make('theme_color')
                                ->label('Theme Color')
                                ->copyable()
                                ->copyMessage('Color code copied')
                                ->copyMessageDuration(1500)
                        ]),
                        
                        TextColumn::make('venue'),
                        Stack::make([
                            TextColumn::make('start')
                                ->date()
                                ->sortable()
                                ->formatStateUsing(function ($column, $state) {
                                    return '<span style="font-size: 70%; opacity: 0.7;">' . $state . '</span>';
                                })
                                ->html(),
                            TextColumn::make('end')
                                ->label('Event Date')
                                ->date()
                                ->sortable()
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
                ]),
                // TextColumn::make('start')
                //     ->label('Date Added')
                //     ->date()
                //     ->sortable(),
                
           ])->space(3),
        ])
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            // ->columns([
            //     ImageColumn::make('thumbnail_path')
            //         ->disk('public')
            //         ->label('Thumbnail'),
            //     TextColumn::make('name')
            //         ->searchable(),
            //     TextColumn::make('package.name')
            //         ->label('Package')
            //         ->searchable()
            //         ->limit(15),
            //     TextColumn::make('event_date')
            //         ->date()
            //         ->sortable(),
            //     TextColumn::make('user.name')
            //         ->label('Creator')
            //         ->toggleable(isToggledHiddenByDefault: true),
            //     TextColumn::make('coordinators.name')
            //         ->label('Coordinators')
            //         ->searchable()
            //         ->getStateUsing(function ($record) {
            //             if ($record->coordinators) {
            //                 return implode('<br/>', $record->coordinators->pluck('name')->toArray());
            //             }
            //             return 'N/A';
            //         })
            //         ->html()
            //         ->verticallyAlignStart(),
            //     TextColumn::make('teams.name')
            //         ->label('Teams')
            //         ->searchable()
            //         ->getStateUsing(function ($record) {
            //             if ($record->teams) {
            //                 return implode('<br/>', $record->teams->pluck('name')->toArray());
            //             }
            //             return 'N/A';
            //         })
            //         ->html()
            //         ->verticallyAlignStart(),
            //     TextColumn::make('venue')
            //         ->searchable()
            //         ->limit(15),
            //     TextColumn::make('groom_name')
            //         ->searchable()
            //         ->limit(15),
            //     TextColumn::make('bride_name')
            //         ->searchable()
            //         ->limit(15),
            //     TextColumn::make('groomCoordinator.name')
            //         ->label('Groom Coordinator')
            //         ->searchable()
            //         ->limit(15),
            //     TextColumn::make('brideCoordinator.name')
            //         ->label('Bride Coordinator')
            //         ->searchable()
            //         ->limit(15),
            //     TextColumn::make('headCoordinator.name')
            //         ->label('Head Coordinator')
            //         ->searchable()
            //         ->limit(15),
            // ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
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
            // 'create' => Pages\CreateProject::route('/create'),
            // 'edit' => Pages\EditProject::route('/{record}/edit'),
            'task' => Pages\task::route('/{record}/task'),
            'view' => Pages\ViewProject::route('/{record}'), 
        ];
    }
}
