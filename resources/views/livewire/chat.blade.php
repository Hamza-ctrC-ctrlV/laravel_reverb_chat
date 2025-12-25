<div class="flex h-screen bg-gray-100" wire:loading.class="opacity-50" wire:loading.class.remove="opacity-100" wire:target="selectUser">
    <div class="w-1/4 bg-white border-r border-gray-200 overflow-y-auto">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Messages</h2>
        </div>

        <div class="overflow-y-auto">
            {{-- Use $this->users to access the computed property --}}
            @forelse($this->users as $user)
                <div wire:key="user-box-{{ $user->id }}" 
                     wire:click="selectUser({{ $user->id }})" 
                     class="flex items-center p-4 border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition {{ $selectedUser && $selectedUser->id == $user->id ? 'bg-blue-50' : '' }}">
                    
                    <div class="relative">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-lg">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        @if(in_array($user->id, $onlineUsers))
                            <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></div>
                        @endif
                    </div>

                    <div class="ml-3 flex-1 overflow-hidden">
                        <div class="flex justify-between items-center">
                            <h3 class="font-semibold text-gray-800 truncate">{{ $user->name }}</h3>
                            @if($user->last_message_at)
                                <span class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($user->last_message_at)->diffForHumans(null, true, true) }}
                                </span>
                            @endif
                        </div>
                        <div class="flex justify-between items-center mt-1">
                            <p class="text-sm text-gray-600 truncate">
                                {{ $user->last_message_preview ?? 'No messages yet' }}
                            </p>
                            @if($user->unread_count > 0)
                                <span class="ml-2 px-2 py-0.5 bg-blue-500 text-white text-xs rounded-full">
                                    {{ $user->unread_count }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-4 text-center text-gray-500">No users available</div>
            @endforelse
        </div>
    </div>

    <div class="flex-1 flex flex-col">
        @if($selectedUser)
            <div class="bg-white border-b border-gray-200 p-4 flex items-center">
                <div class="relative">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold">
                        {{ strtoupper(substr($selectedUser->name, 0, 1)) }}
                    </div>
                    @if(in_array($selectedUser->id, $onlineUsers))
                        <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></div>
                    @endif
                </div>
                <div class="ml-3">
                    <h3 class="font-semibold text-gray-800">{{ $selectedUser->name }}</h3>
                    <p class="text-xs text-gray-500">
                        @if(in_array($selectedUser->id, $onlineUsers))
                            <span class="text-green-600">● Online</span>
                        @else
                            Offline
                        @endif
                    </p>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-4 space-y-2 bg-gray-50" id="chat-messages">
                @forelse($messages as $message)
                    <div wire:key="msg-{{ $message['id'] }}" class="flex {{ $message['sender_id'] == $currentUserId ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-xs lg:max-w-md">
                            <div class="rounded-2xl px-4 py-2 {{ $message['sender_id'] == $currentUserId ? 'bg-blue-500 text-white' : 'bg-white text-gray-800 border shadow-sm' }}">
                                <p class="break-words text-[15px]">{{ $message['message'] }}</p>
                            </div>
                            <div class="flex items-center {{ $message['sender_id'] == $currentUserId ? 'justify-end' : 'justify-start' }} mt-0.5 px-3 space-x-1.5">
                                @if($message['sender_id'] == $currentUserId)
                                    <span class="text-[11px] {{ $message['is_read'] ? 'text-blue-600 font-bold' : 'text-gray-500' }}">
                                        {{ $message['is_read'] ? 'Read' : 'Delivered' }}
                                    </span>
                                @endif
                                <span class="text-[11px] text-gray-500">
                                    {{ \Carbon\Carbon::parse($message['created_at'])->format('g:i A') }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex items-center justify-center h-full">
                        <p class="text-gray-500">No messages yet. Start the conversation!</p>
                    </div>
                @endforelse
            </div>

            <div class="bg-white border-t border-gray-200 p-4">
                <form wire:submit.prevent="submit" class="flex items-center space-x-3">
                    <input type="text" wire:model="newMessage" placeholder="type your message..." class="flex-1 bg-gray-100 border-0 rounded-full px-5 py-2.5 focus:ring-2 focus:ring-blue-500 text-gray-800" autocomplete="off">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white rounded-full w-9 h-9 flex items-center justify-center transition disabled:opacity-50">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M3.478 2.405a.75.75 0 00-.926.94l2.432 7.905H13.5a.75.75 0 010 1.5H4.984l-2.432 7.905a.75.75 0 00.926.94 60.519 60.519 0 0018.445-8.986.75.75 0 000-1.218A60.517 60.517 0 003.478 2.405z"/>
                        </svg>
                    </button>
                </form>
            </div>
        @else
            <div class="flex-1 flex items-center justify-center bg-gray-50">
                <div class="text-center">
                    <h3 class="mt-2 text-lg font-medium text-gray-900">Select a conversation</h3>
                    <p class="mt-1 text-sm text-gray-500">Choose a user to start chatting</p>
                </div>
            </div>
        @endif
    </div>
</div>

@script
<script>
    $wire.on('scroll-to-bottom', () => {
        setTimeout(() => {
            const chatMessages = document.getElementById('chat-messages');
            if (chatMessages) {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
        }, 50);
    });

    // Initial scroll on page load
    const chatMessages = document.getElementById('chat-messages');
    if (chatMessages) {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
</script>
@endscript