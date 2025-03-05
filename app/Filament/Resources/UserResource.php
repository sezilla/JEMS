<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Department;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Select;

use Filament\Support\Enums\Alignment;

use Filament\Forms\Components\Section;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $recordTitleAttribute = 'name';
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            $record->roles->pluck('name')->join(', '),
            $record->teams->first()?->departments->first()?->name,
            $record->teams->pluck('name')->join(', '),
        ];
    }

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
                            ->visible(fn($livewire) => $livewire instanceof Pages\CreateUser),
                        Forms\Components\Select::make('roles')
                            ->relationship('roles', 'name', function ($query) {
                                $query->where('name', '!=', 'super admin');
                            })
                            ->label('Role')
                            ->columnSpan(1)
                            ->preload()
                            ->required()
                            ->searchable()
                            ->reactive(),
                    ]),
                Section::make()
                    ->columns(2)
                    ->columnSpan(2)
                    ->schema([
                        FileUpload::make('avatar_url')
                            ->label('Avatar')
                            ->image()
                            ->visible(fn($livewire) => $livewire instanceof Pages\EditUser),
                        Select::make('departments')
                            ->relationship('departments', 'name', function ($query, $get) {
                                $selectedRole = \Spatie\Permission\Models\Role::where('id', $get('roles'))->value('name');

                                if ($selectedRole === 'Department Admin') {
                                    $query->whereDoesntHave('users', function ($subQuery) {
                                        $subQuery->whereHas('roles', function ($roleQuery) {
                                            $roleQuery->where('name', 'Department Admin');
                                        });
                                    });
                                } elseif ($selectedRole === 'Coordinator') {
                                    $query->where('name', 'Coordination');
                                }
                            })
                            ->label('Department')
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(
                                fn($state, callable $set) =>
                                $set('teams', null)
                            ),


                        Select::make('teams')
                            ->relationship('teams', 'name', function ($query, $get) {
                                $departments = $get('departments');

                                if (!$departments) {
                                    return $query;
                                }

                                if (!is_array($departments)) {
                                    $departments = [$departments];
                                }

                                return $query->whereHas('departments', function ($query) use ($departments) {
                                    $query->whereIn('id', $departments);
                                });
                            })
                            ->label('Team')
                            ->preload()
                            ->reactive(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    Split::make([
                        ImageColumn::make('avatar_url')
                            ->label('Avatar')
                            ->size(150)
                            ->circular()
                            ->alignment(Alignment::Left),
                        Stack::make([
                            Tables\Columns\TextColumn::make('name')
                                ->weight(FontWeight::Bold)
                                ->searchable(),
                            Tables\Columns\TextColumn::make('email')
                                ->limit(15)
                                ->weight(FontWeight::Thin)
                                ->searchable()
                                ->alignment(Alignment::Left),
                            Split::make([
                                Tables\Columns\TextColumn::make('roles')
                                    ->label('Role')
                                    ->size(TextColumn\TextColumnSize::ExtraSmall)
                                    ->alignment(Alignment::Left)
                                    ->verticallyAlignStart()
                                    ->getStateUsing(function ($record) {
                                        if ($record->roles) {
                                            return implode('<br/>', $record->roles->pluck('name')->toArray());
                                        }
                                        return 'No Role';
                                    })
                                    ->html(),

                                IconColumn::make('email_verified_at')
                                    ->size(IconColumn\IconColumnSize::Small)
                                    ->label('Verified')
                                    ->alignment(Alignment::Center)
                                    ->options([
                                        'heroicon-o-check-badge' => fn($state): bool => !is_null($state),
                                        'heroicon-o-clock' => fn($state): bool => is_null($state),
                                    ])
                                    ->colors([
                                        'success' => fn($state): bool => !is_null($state),
                                        'danger' => fn($state): bool => is_null($state),
                                    ]),
                            ]),

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
                            fn(string $state): string => match ($state) {
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


            ])->defaultSort('name', 'asc')
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

    // public static function getNavigationBadge(): ?string
    // {
    //     return static::getModel()::count();
    // }

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
