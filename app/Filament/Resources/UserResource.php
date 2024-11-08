<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Tables\Columns\Layout\Stack;

use Filament\Forms\Components\Select;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Layout\Split;
use Filament\Support\Enums\FontFamily;
use Filament\Tables\Filters\SelectFilter;
use App\Models\Department;
use Filament\Tables\Columns\TextColumn;





class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Section::make('Profile')
                    ->columns(1)
                    ->columnSpan(1)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->columnSpan(1)
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->columnSpan(1)
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('password')
                            ->default('password')
                            ->required()
                            ->columnSpan(1)
                            ->maxLength(255)
                            ->visible(fn ($livewire) => $livewire instanceof Pages\CreateUser),
                        Forms\Components\Select::make('roles')
                            ->multiple()
                            ->relationship('roles', 'name')
                            ->label('Role')
                            ->columnSpan(1)
                            ->preload()
                            ->required()
                            ->searchable(),
                    ]),
                Section::make()
                    ->columns(2)
                    ->columnSpan(2)
                    ->schema([
                        FileUpload::make('avatar_url')
                            ->label('Avatar')
                            ->image()
                            ->visible(fn ($livewire) => $livewire instanceof Pages\EditUser),
                        Select::make('departments')
                            ->relationship('departments', 'name')
                            ->multiple()
                            ->label('Department')
                            ->preload()
                            ->searchable()
                            // Uncomment if you want departments to be reactive and reset teams when changed
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set) => 
                                // Clear the selected teams when the department changes
                                $set('teams', null)
                            ),

                        Select::make('teams')
                            ->relationship('teams', 'name', fn ($query, $get) => 
                                // Filter teams based on the selected departments (handles multiple)
                                $query->whereHas('departments', fn ($query) => 
                                    $query->whereIn('id', $get('departments') ?? [])
                                )
                            )
                            ->label('Team')
                            ->preload()
                            ->multiple()
                            ->searchable(),
                        Select::make('skill_id')
                            ->relationship('skills', 'name')
                            ->label('Skill')
                            ->preload()
                            ->multiple(),
                    ]),
                
                    
                // Section::make()
                //     ->columns(2)
                //     ->schema([
                //         Forms\Components\TextInput::make('name')
                //             ->required()
                //             ->maxLength(255),
                //         Forms\Components\TextInput::make('email')
                //             ->email()
                //             ->required()
                //             ->maxLength(255),
                //         Forms\Components\TextInput::make('password')
                //             ->default('password')
                //             ->required()
                //             ->maxLength(255)
                //             ->visible(fn ($livewire) => $livewire instanceof Pages\CreateUser),
                //         Forms\Components\Select::make('roles')
                //             ->multiple()
                //             ->relationship('roles', 'name')
                //             ->label('Role')
                //             ->preload()
                //             ->required()
                //             ->searchable(),
                            
                //         Select::make('departments')
                //             ->relationship('departments', 'name')
                //             ->multiple()
                //             ->label('Department')
                //             ->preload()
                //             ->searchable()
                //             // Uncomment if you want departments to be reactive and reset teams when changed
                //             ->reactive()
                //             ->afterStateUpdated(fn ($state, callable $set) => 
                //                 // Clear the selected teams when the department changes
                //                 $set('teams', null)
                //             ),

                //         Select::make('teams')
                //             ->relationship('teams', 'name', fn ($query, $get) => 
                //                 // Filter teams based on the selected departments (handles multiple)
                //                 $query->whereHas('departments', fn ($query) => 
                //                     $query->whereIn('id', $get('departments') ?? [])
                //                 )
                //             )
                //             ->label('Team')
                //             ->preload()
                //             ->multiple()
                //             ->searchable(), 

                //         Select::make('skill_id')
                //             ->relationship('skills', 'name')
                //             ->label('Skill')
                //             ->preload()
                //             ->multiple(),


                //         // Select::make('departments')
                //         //     ->relationship('departments', 'name')
                //         //     ->multiple()
                //         //     ->label('Department')
                //         //     ->preload()
                //         //     ->searchable()
                //         //     // ->reactive() // Makes the department field reactive
                //         //     // ->afterStateUpdated(fn ($state, callable $set) => 
                //         //     //     // Clear the selected team when the department changes
                //         //     //     $set('teams', null)
                //         //     // )
                //         //     ,
                        
                //         // Select::make('teams')
                //         //     ->relationship('teams', 'name', fn ($query, $get) => 
                //         //         // Filter teams based on the selected department
                //         //         $query->whereHas('departments', fn ($query) => 
                //         //             $query->where('id', $get('departments'))
                //         //         )
                //         //     )
                //         //     ->multiple()
                //         //     ->label('Team')
                //         //     ->preload()
                //         //     ->searchable()
                //         //     // ->reactive() // Makes the team field reactive
                //         //     // ->afterStateUpdated(function ($state, callable $set) {
                //         //     //     if ($state) {
                //         //     //         // Automatically set the department based on the selected team
                //         //     //         $team = \App\Models\Team::find($state);
                //         //     //         if ($team && $team->departments->isNotEmpty()) {
                //         //     //             $set('departments', $team->departments->first()->id);
                //         //     //         }
                //         //     //     }
                //         //     // })
                //         //     ,
                //     ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Stack::make([
                //     ImageColumn::make('avatar_url')
                //         ->label('Avatar')
                //         ->size(200)
                //         ->circular()
                //         ->alignment(Alignment::Center)
                //         ->extraImgAttributes([
                //             'class' => 'bordered-avatar', // Apply custom CSS class here
                //             'style' => 'border: 2px solid #ccc; padding: 2px;', // Add inline styles for border
                //         ]),
                
                // Tables\Columns\TextColumn::make('name')
                //     ->searchable()
                //     ->sortable()
                //     ->alignment(Alignment::Center)
                //     ->weight('bold'),
                // Tables\Columns\TextColumn::make('email')
                //     ->searchable()
                //     ->alignment(Alignment::Center),
                // Tables\Columns\TextColumn::make('roles')
                //     ->label('Role')
                //     ->alignment(Alignment::Center)
                //     ->verticallyAlignStart()
                //     ->getStateUsing(function ($record) {
                //         if ($record->roles) {
                //             return implode('<br/>', $record->roles->pluck('name')->toArray());
                //         }
                //         return 'No Role';
                //     })
                //     ->html(),

                // Tables\Columns\TextColumn::make('department_name')
                //     ->label('Department')
                //     ->alignment(Alignment::Center)
                //     ->getStateUsing(function ($record) {
                //         return $record->teams->first()?->departments->first()?->name;
                //     })
                //     ->searchable(),

                // Tables\Columns\TextColumn::make('teams.name')
                //     ->label('Team')
                //     ->formatStateUsing(fn ($record) => $record->teams->pluck('name')->join(', '))
                //     ->searchable(),
                // ]),

                Stack::make([
                    Split::make([
                        ImageColumn::make('avatar_url')
                            ->label('Avatar')
                            ->size(150)
                            ->circular()
                            ->alignment(Alignment::Left)
                            ->extraImgAttributes([
                                'class' => 'bordered-avatar', // Apply custom CSS class here
                                'style' => 'border: 2px solid #ccc; padding: 2px;', // Add inline styles for border
                            ]),

                        Stack::make([
                            Tables\Columns\TextColumn::make('name')
                                ->weight(FontWeight::Bold)
                                ->searchable()
                                ->sortable(),
                            Tables\Columns\TextColumn::make('email')
                                ->limit(15)
                                ->weight(FontWeight::Thin)
                                ->searchable()
                                ->alignment(Alignment::Left),
                            Tables\Columns\TextColumn::make('roles')
                                ->label('Role')
                                ->size(TextColumn\TextColumnSize::ExtraSmall)
                                // ->badge()
                                // ->color(fn (string $state): string => match ($state) {
                                //     'Member' => 'gray',
                                //     'Team Leader' => 'warning',
                                //     'Coordinator' => 'success',
                                //     'Admin' => 'danger',
                                //     'Super Admin', 'super_admin' => 'gray',
                                //     default => 'secondary', // Fallback color
                                // })
                                ->alignment(Alignment::Left)
                                ->verticallyAlignStart()
                                ->getStateUsing(function ($record) {
                                    if ($record->roles) {
                                        return implode('<br/>', $record->roles->pluck('name')->toArray());
                                    }
                                    return 'No Role';
                                })
                                ->html(),
                            Tables\Columns\TextColumn::make('skills.name')
                                ->limit(7)
                                ->badge()
                                ->searchable(),
                            
                        ])->space(1)
                        
                    ]),

                    Tables\Columns\TextColumn::make('departments.name')
                        ->label('Department')
                        ->searchable()
                        ->badge()
                        ->color(
                            fn (string $state): string => match ($state) {
                                'Catering' => 'Catering',
                                'Hair and Makeup' => 'Hair',
                                'Photo and Video' => 'Photo',
                                'Designing' => 'Designing',
                                'Entertainment' => 'Entertainment',
                                'Coordination' => 'Coordination',
                            }
                        ),
                    Tables\Columns\TextColumn::make('teams.name')
                        ->fontFamily(FontFamily::Mono)
                ])->space(3),
                

            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->paginated([12, 24, 48, 96, 'all'])
            ->filters([
                SelectFilter::make('departments')
                    ->options(function () {
                        return Department::pluck('name', 'id');
                    })
                    ->label('Department')
                    ->relationship('departments', 'name'),
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }



    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            // 'view' => Pages\ViewUser::route('/{record}/view'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'User Management';
    }
}
