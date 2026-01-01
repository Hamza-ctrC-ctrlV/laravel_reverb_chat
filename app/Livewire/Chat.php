<?php

namespace App\Livewire;

use App\Models\ChatMessage;
use App\Models\User;
use App\Events\MessageSent;
use App\Events\MessageRead;
use App\Events\UserTyping;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Computed;

use function Laravel\Prompts\select;

class Chat extends Component
{
    public $selectedUser;
    public $newMessage = '';
    public $searchEmail = '';
    public $currentUserId;
    public $messages = [];
    public $onlineUsers = [];
    public $typingUserId = null;
    public $loginID;

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

    public function startNewChat()
    {
        $this->validate(['searchEmail' => 'required|email|exists:users,email'], ['exists' => 'User not found.']);

        $user = User::where('email', $this->searchEmail)->first();

        if ($user->id === auth()->id()) {
            $this->addError('searchEmail', 'You cannot chat with yourself.');
            return;
        }

        $this->searchEmail = '';
        $this->selectUser($user->id);
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
        $this->dispatch('$refresh');
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
        
        $this->dispatch('scroll-to-bottom');
        broadcast(new MessageSent($message))->toOthers();
    }

    // Typing Event using Reverb
    public function userTyping()
    {
        $this->dispatch("userTyping", userID : $this->loginID, UserName: Auth::user()->name, selectedUserID: $this->selectedUser->id);
    }


    public function getListeners()
    {
        return [
            "echo-private:chat.{$this->currentUserId},MessageSent" => 'handleIncomingMessage',
            "echo-private:chat.{$this->currentUserId},MessageRead" => 'handleMessageRead',
            "echo-private:chat.{$this->currentUserId},UserTyping" => 'showTypingIndicator',
            "echo-presence:chat-presence,here" => 'updateOnlineUsers',
            "echo-presence:chat-presence,joining" => 'userJoined',
            "echo-presence:chat-presence,leaving" => 'userLeft',
        ];
    }

    public function showTypingIndicator($event)
    {
        $this->typingUserId = $event['senderId'];
        $this->dispatchBrowserEvent('showTyping');
    }

    public function handleIncomingMessage($event)
    {
        $incomingMessage = $event['message'];

        // If chat with this user is open
        if ($this->selectedUser && $incomingMessage['sender_id'] == $this->selectedUser->id) {

            ChatMessage::where('id', $incomingMessage['id'])
                ->update(['is_read' => true]);

            broadcast(new MessageRead(auth()->id(), $this->selectedUser->id))->toOthers();

            $incomingMessage['is_read'] = true;
            $this->messages[] = $incomingMessage;

            $this->dispatch('scroll-to-bottom');
        }
        // Message from another user → update sidebar badge
        else {
            $this->dispatch('$refresh'); // ✅ THIS WAS THE BUG
        }
    }


    public function handleMessageRead($event)
    {
        if ($this->selectedUser && $event['readerId'] == $this->selectedUser->id) {
            foreach ($this->messages as $key => $message) {
                if ($message['sender_id'] == auth()->id() && !$message['is_read']) {
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
        return view('livewire.chat')
            ->layout('components.layouts.empty');
    }
    public bool $showUserMenu = false;

    public function toggleUserMenu()
    {
        $this->showUserMenu = !$this->showUserMenu;
    }
}
