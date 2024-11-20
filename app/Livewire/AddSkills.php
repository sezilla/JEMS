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

class AddSkills extends Component implements HasForms
{
    use InteractsWithForms;
    use HasSort;

    public ?array $data = [];

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
                ->description('Select the skills you have')
                ->schema([
                    Forms\Components\Select::make('skills')
                        ->multiple()
                        ->options(Skill::pluck('name', 'id')->toArray())
                        ->label('Skills')
                        ->preload()
                        ->required(),
                
                ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        // Fetch selected skills from the form state
        $data = $this->form->getState();
        
        // Get the currently authenticated user
        $user = Auth::user();

        // Sync the selected skills with the user's skills
        $user->skills()->sync($data['skills'] ?? []);
        
        // Optionally, add a success message
        session()->flash('message', 'Skills updated successfully.');
    }

    public function render(): View
    {
        return view('livewire.add-skills');
    }
}
