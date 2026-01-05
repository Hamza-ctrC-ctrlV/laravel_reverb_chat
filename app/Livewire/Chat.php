<?php

namespace App\Livewire;

use App\Models\ChatMessage;
use App\Models\User;
use App\Events\MessageSent;
use App\Events\MessageRead;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Computed;

class Chat extends Component
{
    public $selectedUser;
    public $newMessage = '';
    public $searchEmail = '';
    public $currentUserId;
    public $messages = [];
    public $onlineUsers = [];
    public $showUserMenu = false;

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
            ->where(function($q) {
                if($this->searchEmail) $q->where('email', 'like', "%{$this->searchEmail}%");
            })
            ->whereExists(function ($query) use ($authId) {
                $query->select(DB::raw(1))
                    ->from('chat_messages')
                    ->where(function ($q) use ($authId) {
                        $q->whereColumn('sender_id', 'users.id')->where('receiver_id', $authId);
                    })
                    ->orWhere(function ($q) use ($authId) {
                        $q->whereColumn('receiver_id', 'users.id')->where('sender_id', $authId);
                    });
            })
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
        if ($this->selectedUser) {
            $this->loadMessages();
        }
    }

    public function loadMessages()
    {
        if (!$this->selectedUser) return;

        $unread = ChatMessage::where('sender_id', $this->selectedUser->id)
            ->where('receiver_id', auth()->id())
            ->where('is_read', false);

        if ($unread->exists()) {
            $unread->update(['is_read' => true]);
            // Notify the sender that we read their messages
            broadcast(new MessageRead(auth()->id(), $this->selectedUser->id))->toOthers();
        }

        $this->messages = ChatMessage::where(function($q) {
                $q->where("sender_id", auth()->id())->where("receiver_id", $this->selectedUser->id);
            })->orWhere(function($q) {
                $q->where("sender_id", $this->selectedUser->id)->where("receiver_id", auth()->id());
            })
            ->oldest()
            ->get()
            ->toArray();
    }

    public function submit()
    {
        if (empty(trim($this->newMessage))) return;

        $message = ChatMessage::create([
            "sender_id" => Auth::id(),
            "receiver_id" => $this->selectedUser->id,
            "message" => trim($this->newMessage),
            "is_read" => false,
        ]);

        $this->newMessage = ""; 
        $this->messages[] = $message->toArray();
        broadcast(new MessageSent($message))->toOthers();
    }

    public function handleIncomingMessage($event)
    {
        if ($this->selectedUser && $event['message']['sender_id'] == $this->selectedUser->id) {
            $this->loadMessages();
        } else {
            unset($this->users); 
        }
    }

    public function getListeners()
    {
        return [
            "echo-private:chat.{$this->currentUserId},MessageSent" => 'handleIncomingMessage',
            // We handle MessageRead visually via JS, so we only need this 
            // to keep the PHP array in sync if needed.
            "echo-private:chat.{$this->currentUserId},MessageRead" => 'syncReadStatus',
            "echo-presence:chat-presence,here" => 'updateOnlineUsers',
            "echo-presence:chat-presence,joining" => 'userJoined',
            "echo-presence:chat-presence,leaving" => 'userLeft',
        ];
    }

    public function syncReadStatus()
    {
        foreach($this->messages as &$m) { $m['is_read'] = true; }
    }

    public function updateOnlineUsers($users) { $this->onlineUsers = collect($users)->pluck('id')->toArray(); }
    public function userJoined($user) { if (!in_array($user['id'], $this->onlineUsers)) $this->onlineUsers[] = $user['id']; }
    public function userLeft($user) { $this->onlineUsers = array_diff($this->onlineUsers, [$user['id']]); }

    public function render()
    {
        return view('livewire.chat')->layout('components.layouts.empty');
    }
}