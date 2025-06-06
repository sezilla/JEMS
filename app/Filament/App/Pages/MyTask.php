<?php

namespace App\Filament\App\Pages;

use Filament\Tables;
use App\Models\UserTask;
use Filament\Pages\Page;
use Filament\Tables\Table;
use App\Enums\PriorityLevel;
use Illuminate\Support\Facades\Auth;
use Filament\Infolists\Components\Grid;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Infolists\Components\Split;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use App\Filament\App\Resources\ProjectResource;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Infolists\Components\RepeatableEntry;

class MyTask extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.app.pages.my-task';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                UserTask::query()->where('user_id', Auth::id())
            )

            ->columns([
                TextColumn::make('project.name')
                    ->label('Event')
                    ->url(fn($record): string => ProjectResource::getUrl('task', ['record' => $record->project_id]))
                    ->limit(30)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->searchable()
                    ->color(fn(string $state): string => match ($state) {
                        'incomplete' => 'warning',
                        'complete' => 'success',
                        'pending' => 'info',
                        default => 'gray',
                    })
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('priority_level')
                    ->label('Priority')
                    ->getStateUsing(function (UserTask $record): string {
                        return $record->priority_level?->value ?? '';
                    })
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color(fn($state): string => match ($state) {
                        PriorityLevel::P0->value => 'danger',
                        PriorityLevel::P1->value => 'warning',
                        PriorityLevel::P2->value => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('due_date')
                    ->label('Due Date')
                    ->searchable()
                    ->date()
                    ->sortable(),
                ImageColumn::make('attachment')
                    ->label('Attachment')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->stacked()
                    ->circular()
                    ->limit(3)
                    ->limitedRemainingText()
                    ->state(function (UserTask $record): array {
                        if (!$record->attachment) {
                            return [];
                        }

                        $images = [];
                        foreach ((array) $record->attachment as $attachment) {
                            if (isset($attachment['attachment'])) {
                                $images[] = $attachment['attachment'];
                            }
                        }

                        return $images;
                    }),
                TextColumn::make('approvedBy.name')
                    ->searchable()
                    ->label('Approved By')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->default('incomplete')
                    ->options([
                        'incomplete' => 'Incomplete',
                        'pending' => 'Pending',
                        'complete' => 'Completed',
                    ]),
                SelectFilter::make('priority_level')
                    ->label('Priority')
                    ->options(PriorityLevel::class),
            ])
            ->actions([
                ViewAction::make()
                    ->label('View')
                    ->color('primary')
                    ->icon('heroicon-m-eye')
                    ->infolist(fn(UserTask $record) => [
                        Grid::make(2)
                            ->schema([
                                Fieldset::make('Task Information')
                                    ->schema([
                                        Split::make([
                                            TextEntry::make('task_name')
                                                ->label('Task')
                                                ->size(TextEntry\TextEntrySize::Large),
                                            TextEntry::make('card_name')
                                                ->label('Department')
                                                ->badge(),
                                        ]),
                                        TextEntry::make('due_date')
                                            ->label('Due Date')
                                            ->date(),
                                    ]),
                                Fieldset::make('Status Information')
                                    ->schema([
                                        TextEntry::make('priority_level')
                                            ->label('Priority')
                                            ->badge()
                                            ->color(function ($state): string {
                                                return match ($state?->value) {
                                                    PriorityLevel::P0->value => 'danger',
                                                    PriorityLevel::P1->value => 'warning',
                                                    PriorityLevel::P2->value => 'info',
                                                    default => 'gray',
                                                };
                                            }),
                                        TextEntry::make('status')
                                            ->label('Status')
                                            ->badge()
                                            ->color(function (string $state): string {
                                                return match ($state) {
                                                    'incomplete' => 'warning',
                                                    'complete' => 'success',
                                                    'pending' => 'info',
                                                    default => 'gray',
                                                };
                                            }),
                                    ]),
                                Fieldset::make('Assignment Information')
                                    ->schema([
                                        Split::make([
                                            ImageEntry::make('users.avatar_url')
                                                ->label('Avatar')
                                                ->circular(),
                                            TextEntry::make('users.name')
                                                ->label('Assigned To'),
                                        ]),
                                        TextEntry::make('approvedBy.name')
                                            ->label('Approved By'),
                                    ]),
                                Fieldset::make('Attachments')
                                    ->schema([
                                        RepeatableEntry::make('attachment')
                                            ->label('')
                                            ->schema([
                                                Split::make([
                                                    ImageEntry::make('attachment')
                                                        ->label('')
                                                        ->width('full')
                                                        ->height(150)
                                                        ->width(150)
                                                        ->extraImgAttributes(['class' => 'rounded-md w-full']),
                                                    TextEntry::make('description')
                                                        ->label('Description'),
                                                ]),
                                            ])->grid(2)->columnSpan('full'),
                                    ]),
                                Fieldset::make('Additional Information')
                                    ->schema([
                                        TextEntry::make('created_at')
                                            ->label('Created At')
                                            ->dateTime(),
                                        TextEntry::make('updated_at')
                                            ->label('Updated At')
                                            ->dateTime(),
                                    ]),
                            ]),
                    ]),
            ])
            ->emptyStateHeading('No Pending Tasks')
            ->emptyStateDescription('There are no tasks pending for approval at this time.');;
    }
}
