<?php

namespace App\Livewire\Chat;

use Livewire\Component;
use App\Events\MessageSent;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class SendMessage extends Component
{
    public $messageBody;
    public $selectedConversationId;
    public $typingUser;

    protected $listeners = [
        'conversationSelected' => 'setConversationId',
    ];

    public function setConversationId($conversationId)
    {
        $this->selectedConversationId = $conversationId;
    }

    public function sendMessage()
    {
        if (!$this->messageBody || !$this->selectedConversationId) return;

        $message = Message::create([
            'conversation_id' => $this->selectedConversationId,
            'user_id' => Auth::id(),
            'body' => $this->messageBody,
        ]);

        $this->messageBody = '';
        broadcast(new MessageSent($message));
    }

    public function render()
    {
        return view('livewire.chat.send-message');
    }
}