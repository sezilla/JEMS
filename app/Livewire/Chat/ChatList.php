<?php

namespace App\Livewire\Chat;

use Livewire\Component;
use App\Models\Conversation;

class ChatList extends Component
{
    public $conversations;
    public $selectedConversationId;

    public function mount()
    {
        $this->conversations = Conversation::all();
        $this->selectedConversationId = optional($this->conversations->first())->id;
        if ($this->selectedConversationId) {
            $this->dispatch('conversationSelected', $this->selectedConversationId);
        }
    }

    public function selectConversation($conversationId)
    {
        $this->selectedConversationId = $conversationId;
        $this->dispatch('conversationSelected', $this->selectedConversationId);
    }

    public function render()
    {
        return view('livewire.chat.chat-list');
    }
}

