<?php

namespace App\Livewire\Chat;

use Livewire\Component;
use App\Models\Conversation;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatMain extends Component
{
    public $selectedConversationId;

    protected $listeners = [
        'conversationSelected' => 'updateConversation',
    ];

    public function updateConversation($conversationId)
    {
        $this->selectedConversationId = $conversationId;
        $this->emit('loadMessages', $conversationId);
    }

    public function render()
    {
        return view('livewire.chat.chat-main');
    }
}