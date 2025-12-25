<?php

namespace App\Livewire;

use App\Models\ChatMessage;
use App\Models\User;
use App\Events\MessageSent;
use App\Events\MessageRead;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;

class Chat extends Component
{
    public $selectedUser;
    public $newMessage = '';
    public $currentUserId;
    public $messages = [];
    public $onlineUsers = [];
    public $isTyping = false;

    public function mount()
    {
        $this->currentUserId = Auth::id();
        
        if ($this->users->count() > 0) {
            $this->selectUser($this->users->first()->id);
        }
    }

    #[Computed]
    public function users()
    {
        $authId = Auth::id();

        return User::where('users.id', '!=', $authId)
            ->leftJoin('chat_messages', function ($join) use ($authId) {
                $join->on('chat_messages.id', '=', DB::raw("(
                    SELECT id FROM chat_messages 
                    WHERE (sender_id = users.id AND receiver_id = $authId)
                       OR (sender_id = $authId AND receiver_id = users.id)
                    ORDER BY created_at DESC LIMIT 1
                )"));
            })
            ->select([
                'users.*',
                'chat_messages.message as last_message_preview',
                'chat_messages.created_at as last_message_at',
                'unread_count' => ChatMessage::selectRaw('count(*)')
                    ->whereColumn('sender_id', 'users.id')
                    ->where('receiver_id', $authId)
                    ->where('is_read', false)
            ])
            ->orderBy('last_message_at', 'desc')
            ->get();
    }

    public function selectUser($id)
    {
        $this->selectedUser = User::find($id);
        $this->isTyping = false; // Reset typing status when switching users
        if ($this->selectedUser) {
            $this->loadMessages();
        }
    }

    public function loadMessages()
    {
        if (!$this->selectedUser) return;

        ChatMessage::where('sender_id', $this->selectedUser->id)
            ->where('receiver_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        broadcast(new MessageRead(auth()->id(), $this->selectedUser->id))->toOthers();

        $this->messages = ChatMessage::where(function($q) {
                $q->where("sender_id", auth()->id())->where("receiver_id", $this->selectedUser->id);
            })->orWhere(function($q) {
                $q->where("sender_id", $this->selectedUser->id)->where("receiver_id", auth()->id());
            })
            ->oldest()
            ->get()
            ->toArray();
            
        $this->dispatch('scroll-to-bottom');
    }

    public function submit()
    {
        $this->validate(['newMessage' => 'required|string|max:5000']);
        if (!$this->selectedUser) return;

        $message = ChatMessage::create([
            "sender_id" => Auth::id(),
            "receiver_id" => $this->selectedUser->id,
            "message" => trim($this->newMessage),
            "is_read" => false,
        ]);

        $this->messages[] = $message->toArray();
        $this->newMessage = "";
        
        $this->dispatch('message-sent');
        $this->dispatch('scroll-to-bottom');
        broadcast(new MessageSent($message))->toOthers();
    }

    public function getListeners()
    {
        return [
            "echo-private:chat.{$this->currentUserId},MessageSent" => 'handleIncomingMessage',
            "echo-private:chat.{$this->currentUserId},MessageRead" => 'handleMessageRead',
            "echo-presence:chat-presence,.client-typing" => 'handleTyping', // Whisper Listener
            "echo-presence:chat-presence,here" => 'updateOnlineUsers',
            "echo-presence:chat-presence,joining" => 'userJoined',
            "echo-presence:chat-presence,leaving" => 'userLeft',
        ];
    }

    public function handleTyping($event)
    {
        if ($this->selectedUser && $event['typingId'] == $this->selectedUser->id && $event['receiverId'] == auth()->id()) {
            $this->isTyping = true;
            $this->dispatch('reset-typing');
        }
    }

    public function handleIncomingMessage($event)
    {
        $incomingMessage = $event['message'];

        // If I have this user's chat open right now...
        if ($this->selectedUser && $incomingMessage['sender_id'] == $this->selectedUser->id) {
            // 1. Update database
            ChatMessage::where('id', $incomingMessage['id'])->update(['is_read' => true]);
            
            // 2. Tell the sender I read it
            broadcast(new MessageRead(auth()->id(), $this->selectedUser->id))->toOthers();
            
            // 3. Add to my UI as "read"
            $incomingMessage['is_read'] = true;
            $this->messages[] = $incomingMessage;
            
            $this->dispatch('scroll-to-bottom');
        }
    }

    public function handleMessageRead($event)
    {
        // If the person I am currently talking to just read my messages...
        if ($this->selectedUser && $event['readerId'] == $this->selectedUser->id) {
            // Update all messages in the CURRENT array to is_read = true
            foreach ($this->messages as $key => $message) {
                if ($message['sender_id'] == auth()->id() && $message['receiver_id'] == $this->selectedUser->id) {
                    $this->messages[$key]['is_read'] = true;
                }
            }
        }
    }

    public function updateOnlineUsers($users) { $this->onlineUsers = collect($users)->pluck('id')->toArray(); }
    public function userJoined($user) { if (!in_array($user['id'], $this->onlineUsers)) $this->onlineUsers[] = $user['id']; }
    public function userLeft($user) { $this->onlineUsers = array_diff($this->onlineUsers, [$user['id']]); }

    public function render()
    {
        return view('livewire.chat');
    }
}