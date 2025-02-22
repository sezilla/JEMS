<?php

namespace App\Livewire\Chat;

use Livewire\Component;
use App\Models\Message;
use App\Events\UserTyping;
use App\Events\MessageSent;

class ChatBox extends Component
{
    public $messages = [];
    public $selectedConversationId;
    public $typingUser;

    protected function getListeners()
    {
        if (!$this->selectedConversationId) {
            return [];
        }
    
        return [
            "echo-private:conversation.{$this->selectedConversationId},MessageSent" => "addMessage",
            "echo-private:conversation.{$this->selectedConversationId},UserTyping" => "setTypingUser",
        ];
    }      

    public function loadMessages($conversationId)
    {
        $this->selectedConversationId = $conversationId;
        $this->messages = Message::where('conversation_id', $conversationId)
            ->with('user')
            ->latest()
            ->get()
            ->toArray();
    }

    public function addMessage($message)
    {
        if (!collect($this->messages)->contains('id', $message['id'])) {
            $this->messages[] = $message;
            $this->dispatchBrowserEvent('scroll-to-bottom');
        }
    }

    public function setTypingUser($data)
    {
        $this->typingUser = $data['user']['name'];
    }

    public function render()
    {
        return view('livewire.chat.chat-box');
    }
}