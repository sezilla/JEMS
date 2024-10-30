<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PackageResource\Pages;
use App\Filament\Resources\PackageResource\RelationManagers;
use App\Models\Package;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Model;

use Filament\Forms\Components\Section;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ImageColumn;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;

class PackageResource extends Resource
{
    protected static ?string $model = Package::class;

    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->columnSpan('full'),
                        MarkdownEditor::make('description')
                            ->required(),
                        FileUpload::make('image')
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('packages'),
                    ])
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
                        ->description(fn($record): ?string => $record->description)
                        ->alignment(Alignment::Start),
                ]),
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
            PackageResource\RelationManagers\TaskRelationManager::class,  
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPackages::route('/'),
            'create' => Pages\CreatePackage::route('/create'),
            'edit' => Pages\EditPackage::route('/{record}/edit'),
            // 'task' => Pages\PackageTask::route('/{record}/task'),
            // 'createTask' => Pages\CreateTask::route('/{record}/task/create'),
            // 'editTask' => Pages\EditTask::route('/{record}/task/{task}/edit'),
        ];
    }
    
    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Project Management';
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
