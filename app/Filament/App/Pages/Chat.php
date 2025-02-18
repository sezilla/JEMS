<?php

namespace App\Filament\App\Pages;

use Illuminate\Contracts\Support\Htmlable;
use Filament\Forms;
use App\Models\Message;
use App\Models\Conversation;
use Filament\Pages\Page;
use App\Events\UserTyping;
use App\Events\MessageSent;
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
    public $typingUser = null;

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
        
        broadcast(new MessageSent($message));
    }

    public function refreshMessages()
    {
        $this->loadMessages($this->selectedConversationId);
    }  
    
    protected $listeners = [
        "echo-private:conversation.{selectedConversationId},MessageSent" => "addMessage",
        "echo-private:conversation.{selectedConversationId},UserTyping" => "setTypingUser",
    ];            

    public function addMessage($message)
    {
        \Log::info('Livewire received MessageSent event', ['message' => $message]);
    
        if (!collect($this->messages)->contains('id', $message['id'])) {
            $this->messages[] = $message;
            $this->dispatchBrowserEvent('scroll-to-bottom'); // Scroll to bottom on new message
        }
    }    

    public function setTypingUser($data)
    {
        $this->typingUser = $data['user']['name'];
        $this->emit('userTyping', $this->typingUser);
    }

    public function userTyping()
    {
        broadcast(new UserTyping(auth()->user(), $this->selectedConversationId));
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
