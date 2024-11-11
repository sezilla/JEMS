<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReactionResource\Pages;
use App\Filament\Resources\ReactionResource\RelationManagers;
use App\Models\Reaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReactionResource extends Resource
{
    protected static ?string $model = Reaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('post_id')
                    ->nullable()
                    ->relationship('post', 'title'),
                Forms\Components\Select::make('comment_id')
                    ->nullable()
                    ->relationship('comment', 'content'),
                Forms\Components\Select::make('type')
                    ->options([
                        'like' => 'Like',
                        'love' => 'Love',
                        'dislike' => 'Dislike',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('user.name'),
                Tables\Columns\TextColumn::make('post.id'),
                Tables\Columns\TextColumn::make('comment.id'),
                Tables\Columns\TextColumn::make('type'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListReactions::route('/'),
            'create' => Pages\CreateReaction::route('/create'),
            'edit' => Pages\EditReaction::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Feed';
    }
}
