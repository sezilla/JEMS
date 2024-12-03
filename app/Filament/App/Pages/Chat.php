<?php

namespace App\Filament\App\Pages;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Forms;
use App\Models\Message;
use App\Models\Conversation;
use Filament\Pages\Page;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class Chat extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;
    // use HasPageShield;

    public $conversations;
    public $selectedConversationId;
    public $messages = [];
    public $messageBody;
    public $selectedConversationName;

    public function mount()
    {
        $this->conversations = Conversation::all();
        $this->selectedConversationName = $this->conversations->first()->name ?? '';
        $this->selectedConversationId = $this->conversations->first()->id ?? null;

        if ($this->selectedConversationId) {
            $this->loadMessages($this->selectedConversationId);
        }
    }

    public function loadMessages($conversationId)
    {
        $this->messages = Message::where('conversation_id', $conversationId)
            ->with('user')
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'body' => $message->body,
                    'created_at' => $message->created_at,
                    'user_id' => $message->user_id,
                    'user' => [
                        'id' => $message->user->id,
                        'name' => $message->user->name,
                        'avatar' => $message->user->getFilamentAvatarUrl() ?? asset('images/default-avatar.png'),
                    ],
                ];
            })
            ->toArray();
    
        // // Emit a Livewire event to scroll the chat to the bottom
        // $this->dispatchBrowserEvent('scroll-to-bottom');
    }
    

    public function updatedSelectedConversationId($conversationId)
    {
        $conversation = Conversation::find($conversationId);
        $this->selectedConversationName = $conversation ? $conversation->name : '';
        $this->loadMessages($conversationId);
    }

    public function sendMessage()
    {
        $message = Message::create([
            'conversation_id' => $this->selectedConversationId,
            'user_id' => auth()->id(),
            'body' => $this->messageBody,
        ]);

        $this->messageBody = '';
        $this->messages[] = $message->load('user')->toArray();
        
    }

    public function getListeners()
    {
        return [
            'messageReceived' => 'addMessage',
        ];
    }

    public function addMessage($message)
    {
        $this->messages[] = [
            'id' => $message['id'],
            'body' => $message['body'],
            'created_at' => $message['created_at'],
            'user_id' => $message['user_id'],
            'user' => [
                'id' => $message['user']['id'],
                'name' => $message['user']['name'],
                'avatar' => $message['user']['getFilamentAvatarUrl'] ?? asset('images/default-avatar.png'),
            ],
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return '';
    }

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.app.pages.chat';

    /**
     * Get the form schema definition for the page.
     *
     * @return array
     */
    protected function getFormSchema(): array
    {
        return [
            // Forms\Components\Select::make('selectedConversationId')
            //     ->label('Select Conversation')
            //     ->options($this->conversations->pluck('name', 'id'))
            //     ->reactive()
            //     ->afterStateUpdated(fn ($state) => $this->loadMessages($state)),

            Forms\Components\TextInput::make('messageBody')
                ->label('')
                ->placeholder('Type your message here...'),
        ];
    }

    /**
     * Submit the form to send a message.
     */
    public function submit()
    {
        $this->sendMessage();
    }
}
