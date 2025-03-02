<?php

namespace App\Livewire;

use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Joaopaulolndev\FilamentEditProfile\Concerns\HasSort;
use App\Models\User;
use App\Models\Skill;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class AddSkills extends Component implements HasForms
{
    use InteractsWithForms;
    use HasSort;

    public ?array $data = [];
    public bool $isSaving = false; // Loading state

    protected static int $sort = 0;

    public function mount(): void
    {
        $this->form->fill([
            'skills' => Auth::user()->skills->pluck('id')->toArray(),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('My Skills')
                    ->aside()
                    ->description('Select the skills you have. "3" maximum.')
                    ->schema([
                        Forms\Components\Select::make('skills')
                            ->multiple()
                            ->options(Skill::pluck('name', 'id')->toArray())
                            ->label('Skills')
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set) => 
                                count($state) > 3 ? $set('skills', array_slice($state, 0, 3)) : null
                            )
                            ->rule('max:3') // Enforce max 3 selections
                            ->required(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->isSaving = true; // Show loading indicator

        // Fetch selected skills from the form state
        $data = $this->form->getState();
        
        // Get the currently authenticated user
        $user = Auth::user();

        // Sync the selected skills with the user's skills
        $user->skills()->sync($data['skills'] ?? []);

        // Add success notification
        Notification::make()
            ->title('Success')
            ->body('Your skills have been updated.')
            ->success()
            ->send();

        // Hide loading indicator
        $this->isSaving = false;
    }

    public function render(): View
    {
        return view('livewire.add-skills');
    }
}
