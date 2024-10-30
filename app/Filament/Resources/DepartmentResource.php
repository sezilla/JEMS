<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DepartmentResource\Pages;
use App\Filament\Resources\DepartmentResource\RelationManagers;
use App\Models\Department;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Components\Section;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ImageColumn;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;


class DepartmentResource extends Resource
{
    protected static ?string $model = Department::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan('1'),
                        Forms\Components\Select::make('teams')
                            ->multiple()
                            ->columnSpan('1')
                            ->relationship('teams', 'name')
                            ->label('Teams')
                            ->preload()
                            ->searchable(),
                        Forms\Components\MarkdownEditor::make('description')
                            ->required()
                            ->columnSpan('1'),
                        FileUpload::make('image')
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->columnSpan('1')
                            ->directory('departments'),
                    ])
                    ->columns(2)
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    ImageColumn::make('image')
                        ->width(200)
                        ->height(200)
                        ->rounded('lg')
                        ->alignment(Alignment::Center),
                    Tables\Columns\TextColumn::make('name')
                        ->weight(FontWeight::Bold)
                        ->description(fn($record): ?string => $record->description),
                    ImageColumn::make('teams.image')
                        ->circular()
                        ->stacked(),
                ])
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->paginated(false)
            ->filters([
                //
            ])
            ->actions([
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDepartments::route('/'),
            'create' => Pages\CreateDepartment::route('/create'),
            'edit' => Pages\EditDepartment::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'User Management';
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
