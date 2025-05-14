<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use App\Models\UserTask;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Livewire\WithPagination;
use Illuminate\Support\HtmlString;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Blade;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Support\Contracts\TranslatableContentDriver;

class ProjectTaskTable extends Component implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    public $project;
    public $cardNames = [];
    public $activeCard = null;

    protected $queryString = ['activeCard'];

    public function makeFilamentTranslatableContentDriver(): ?TranslatableContentDriver
    {
        return null;
    }

    public function mount($project)
    {
        $user = Auth::user();
        $this->project = $project;

        // Get all unique card names for this project
        $this->cardNames = UserTask::forUser(Auth::user()->id)
            ->where('project_id', $this->project->id)
            ->whereNotNull('card_name')
            ->distinct()
            ->pluck('card_name')
            ->toArray();

        // Get tasks with empty card_name
        $hasNoCardNameTasks = UserTask::forUser(Auth::user()->id)
            ->where('project_id', $this->project->id)
            ->whereNull('card_name')
            ->exists();

        if ($hasNoCardNameTasks) {
            $this->cardNames[] = 'Uncategorized';
        }

        if (!$this->activeCard && !empty($this->cardNames)) {
            $this->activeCard = $this->cardNames[0];
        }
    }

    public function setActiveCard($cardName)
    {
        $this->activeCard = $cardName;
    }

    public function render()
    {
        return view('livewire.project-task-table');
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
            Select::make('status')
                ->label('Status')
                ->default('incomplete')
                ->options([
                    'incomplete' => 'Incomplete',
                    'pending' => 'Pending',
                    'completed' => 'Completed',
                ])
                ->required(),
            Repeater::make('attachment')
                ->label('Attachment')
                ->schema([
                    FileUpload::make('attachment')
                        ->label('Attachment')
                        ->required(),
                ]),
        ]);
    }

    public function table(Table $table): Table
    {
        $query = UserTask::forUser(Auth::user()->id)
            ->where('project_id', $this->project->id);

        return $table
            ->query($query)
            ->columns([
                TextColumn::make('card_name')
                    ->label('Department')
                    ->toggleable()
                    ->visible(function () {
                        if (!Auth::check()) return false;
                        return Auth::user()->roles->where('name', 'Coordinator')->count() > 0;
                    })
                    ->sortable(),
                TextColumn::make('task_name')
                    ->label('Task')
                    ->searchable(),
                TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
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
                SelectFilter::make('status')
                    ->label('Status')
                    ->default('incomplete')
                    ->options([
                        'incomplete' => 'Incomplete',
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                    ]),
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
            ])
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
                                FileUpload::make('attachment')
                                    ->label('Attachment')
                                    ->required(),
                            ]),
                    ]),
            ]);
    }
}
