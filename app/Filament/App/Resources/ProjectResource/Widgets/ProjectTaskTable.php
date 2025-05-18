<?php

namespace App\Filament\App\Resources\ProjectResource\Widgets;

use App\Models\User;
use App\Models\UserTask;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Enums\PriorityLevel;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Filters\SelectFilter;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\SelectColumn;

class ProjectTaskTable extends BaseWidget
{
    public $project;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Event Tasks';

    public function table(Table $table): Table
    {
        $query = UserTask::forUser(Auth::user()->id)
            ->where('project_id', $this->project->id);

        return $table
            ->headerActions([
                Action::make('createTask')
                    ->label('Create Task')
                    ->icon('heroicon-o-plus')
                    ->requiresConfirmation()
                    ->visible(function () {
                        if (!Auth::check()) return false;
                        return Auth::user()->roles->where('name', 'Coordinator')->count() > 0;
                    })
                    ->form(fn(Form $form): Form => $this->form($form))
                    ->slideOver()
            ])
            ->query($query)
            ->columns([
                TextColumn::make('card_name')
                    ->label('Department')
                    ->toggleable()
                    ->visible(fn() => optional(Auth::user())->hasRole('Coordinator'))
                    ->sortable(),
                TextColumn::make('task_name')
                    ->label('Task')
                    ->limit(30)
                    ->searchable(),
                TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date()
                    ->sortable(),
                SelectColumn::make('priority_level')
                    ->label('Priority')
                    ->options([
                        PriorityLevel::P0->value => 'P0',
                        PriorityLevel::P1->value => 'P1',
                        PriorityLevel::P2->value => 'P2',
                    ])
                    ->sortable()
                    ->visible(fn() => optional(Auth::user())->hasRole('Coordinator'))
                    ->sortable(),
                TextColumn::make('priority')
                    ->label('Priority')
                    ->getStateUsing(function (UserTask $record): string {
                        return $record->priority_level?->value ?? '';
                    })
                    ->visible(fn() => optional(Auth::user())->hasAnyRole(['Team Leader', 'Member']))
                    ->sortable()
                    ->badge()
                    ->color(fn($state): string => match ($state) {
                        PriorityLevel::P0->value => 'danger',
                        PriorityLevel::P1->value => 'warning',
                        PriorityLevel::P2->value => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'incomplete' => 'warning',
                        'complete' => 'success',
                        'pending' => 'info',
                        default => 'gray',
                    })
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                ImageColumn::make('attachment')
                    ->label('Attachment')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->stacked()
                    ->circular()
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
                TextColumn::make('approved_by')
                    ->label('Approved By')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('card_name')
                    ->label('Department')
                    ->options(UserTask::forUser(Auth::user()->id)
                        ->where('project_id', $this->project->id)
                        ->whereNotNull('card_name')
                        ->distinct()
                        ->pluck('card_name', 'card_name'))
                    ->visible(function () {
                        if (!Auth::check()) return false;
                        return Auth::user()->roles->where('name', 'Coordinator')->count() > 0;
                    }),
                SelectFilter::make('status')
                    ->label('Status')
                    ->default('incomplete')
                    ->options([
                        'incomplete' => 'Incomplete',
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                    ]),
                SelectFilter::make('priority_level')
                    ->label('Priority')
                    ->options(PriorityLevel::class),
            ], layout: FiltersLayout::AboveContentCollapsible)->filtersFormColumns(3)
            ->actions([
                EditAction::make()
                    ->form(fn(Form $form): Form => $this->form($form))
                    ->fillForm(function (UserTask $record): array {
                        return [
                            'task_name' => $record->task_name,
                            'due_date' => $record->due_date,
                            'user_id' => $record->user_id,
                            'status' => $record->status,
                            'attachment' => $record->attachment,
                        ];
                    })
                    ->using(function (UserTask $record, array $data): UserTask {
                        $record->update($data);
                        return $record;
                    }),

                Action::make('submitAsComplete')
                    ->requiresConfirmation()
                    ->label('Submit')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->action(function (UserTask $record, array $data) {
                        $record->update([
                            'status' => 'pending',
                            'attachment' => $data['attachment'] ?? $record->attachment,
                        ]);
                    })
                    ->form([
                        Repeater::make('attachment')
                            ->label('Attachment')
                            ->schema([
                                TextInput::make('description')
                                    ->label('Description'),
                                FileUpload::make('attachment')
                                    ->label('Attachment')
                                    ->required(),
                            ]),
                    ]),
            ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('task_name')
                ->label('Task')
                ->required(),
            DatePicker::make('due_date')
                ->label('Due Date')
                ->required(),
            Select::make('user_id')
                ->label('Assign To')
                ->options(User::all()->pluck('name', 'id'))
                ->required(),
            Select::make('priority_level')
                ->label('Priority')
                ->options(PriorityLevel::class)
                ->required(),
            Select::make('status')
                ->label('Status')
                ->default('incomplete')
                ->options([
                    'incomplete' => 'Incomplete',
                    'pending' => 'Pending',
                    'complete' => 'Completed',
                ])
                ->required(),
            Repeater::make('attachment')
                ->label('Attachment')
                ->schema([
                    TextInput::make('description')
                        ->label('Description'),
                    FileUpload::make('attachment')
                        ->label('File'),
                ]),
        ]);
    }
}
