<div class="flex h-screen bg-gray-100" wire:loading.class="opacity-50" wire:target="selectUser">

    {{-- Styles Section --}}
    <style>
        #chat-messages::-webkit-scrollbar { width: 12px; }
        #chat-messages::-webkit-scrollbar-track { background: rgba(0,0,0,0.02); border-radius: 10px; }
        #chat-messages::-webkit-scrollbar-thumb {
            background-color: rgba(50,50,50,0.3);
            border-radius: 10px;
            border: 3px solid transparent;
            background-clip: content-box;
            transition: background-color 0.2s ease;
        }
        #chat-messages:hover::-webkit-scrollbar-thumb { background-color: rgba(30,30,30,0.6); }
        #chat-messages::-webkit-scrollbar-thumb:active { background-color: rgba(0,0,0,0.8); }
        .apple-scroll { scroll-behavior: smooth; -webkit-overflow-scrolling: touch; }
        .bubble-sent { border-bottom-right-radius: 4px !important; }
        .bubble-received { border-bottom-left-radius: 4px !important; }
        #chat-input:focus { outline: none !important; box-shadow: none !important; border: none !important; }
    </style>

    {{-- Users List Section --}}
    <div class="w-1/4 bg-white border-r border-gray-200 flex flex-col">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-800 mb-4 tracking-tight">Messages</h2>
            <form wire:submit.prevent="startNewChat" class="relative">
                <input type="email" wire:model="searchEmail" placeholder="Search email..." 
                    class="w-full text-sm bg-gray-100 border-none rounded-xl px-4 py-2.5 focus:ring-0 text-gray-800">
            </form>
        </div>
        <div class="flex-1 overflow-y-auto">
            @foreach($this->users as $user)
                <div wire:key="user-row-{{ $user->id }}" wire:click="selectUser({{ $user->id }})" 
                     class="flex items-center p-4 border-b border-gray-50 hover:bg-gray-50 cursor-pointer transition {{ $selectedUser && $selectedUser->id == $user->id ? 'bg-blue-50' : '' }}">
                    <div class="relative">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-medium shadow-sm">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        @if(in_array($user->id, $onlineUsers))
                            <div class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-green-500 rounded-full border-2 border-white"></div>
                        @endif
                    </div>
                    <div class="ml-3 flex-1 overflow-hidden">
                        <div class="flex justify-between items-center">
                            <h3 class="font-semibold text-gray-800 truncate">{{ $user->name }}</h3>
                        </div>
                        <p class="text-sm text-gray-500 truncate">{{ $user->last_message_preview ?? 'No messages yet' }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Chat Window Section --}}
    <div class="flex-1 flex flex-col min-h-0 bg-white">
        @if($selectedUser)
            {{-- Selected User Header --}}
            <div class="bg-white/90 backdrop-blur-md border-b border-gray-200 p-4 flex items-center sticky top-0 z-10">
                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                    {{ strtoupper(substr($selectedUser->name, 0, 1)) }}
                </div>
                <div class="ml-3">
                    <h3 class="font-bold text-gray-900 leading-tight">{{ $selectedUser->name }}</h3>
                    <p class="text-[11px] font-bold tracking-wider {{ in_array($selectedUser->id, $onlineUsers) ? 'text-green-500' : 'text-gray-400' }}">
                        {{ in_array($selectedUser->id, $onlineUsers) ? 'ONLINE' : 'OFFLINE' }}
                    </p>
                </div>
            </div>

            {{-- Messages Section --}}
            <div 
                id="chat-messages"
                x-data="{ 
                    userScrolledUp: false,
                    typingUsers: [],
                    scrollToBottom(behavior = 'smooth') { 
                        $el.scrollTo({ top: $el.scrollHeight, behavior: behavior }); 
                        this.userScrolledUp = false;
                    },
                    handleScroll() {
                        const distanceToBottom = $el.scrollHeight - $el.scrollTop - $el.clientHeight;
                        this.userScrolledUp = distanceToBottom > 200;
                    }
                }"
                x-init="setTimeout(() => scrollToBottom('auto'), 50)"
                @scroll.debounce.50ms="handleScroll"
                @scroll-to-bottom.window="if(!userScrolledUp) setTimeout(() => scrollToBottom('smooth'), 50)"
                class="flex-1 overflow-y-auto p-4 pr-5 space-y-4 apple-scroll relative"
            >
                @foreach($messages as $message)
                    <div wire:key="msg-{{ $message['id'] ?? $loop->index }}" 
                         class="flex {{ $message['sender_id'] == $currentUserId ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[80%] lg:max-w-md">
                            <div class="rounded-[22px] px-4 py-2.5 text-[15px] shadow-sm {{ $message['sender_id'] == $currentUserId ? 'bg-blue-500 text-white bubble-sent' : 'bg-gray-100 text-gray-800 bubble-received' }}">
                                <p class="leading-snug">{{ $message['message'] }}</p>
                            </div>
                            <div class="flex items-center mt-1.5 px-1 space-x-2 text-[10px] text-gray-400 font-bold {{ $message['sender_id'] == $currentUserId ? 'justify-end' : 'justify-start' }}">
                                <span>{{ \Carbon\Carbon::parse($message['created_at'])->format('g:i A') }}</span>
                                @if($message['sender_id'] == $currentUserId)
                                    <span class="{{ $message['is_read'] ? 'text-blue-500' : '' }}">
                                        {{ $message['is_read'] ? 'READ' : 'DELIVERED' }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Typing Indicator --}}
                <template x-for="user in typingUsers" :key="user">
                    <div class="px-4 pb-1 text-xs text-gray-400 italic" x-text="user + ' is typing...'"></div>
                </template>

                {{-- Scroll to Bottom Button --}}
                <div x-show="userScrolledUp" x-transition class="sticky bottom-2 left-0 right-0 flex justify-center pointer-events-none">
                    <button @click="scrollToBottom('smooth')" class="pointer-events-auto bg-white border border-gray-200 text-blue-600 px-5 py-2 rounded-full shadow-xl text-xs font-black hover:scale-105 transition transform">
                        ↓ NEW MESSAGES
                    </button>
                </div>
            </div>

            {{-- Message Input Section --}}
            <div class="p-4 bg-white">
                <form wire:submit.prevent="submit" class="flex items-center bg-gray-100 rounded-[24px] px-4 py-1.5 border border-transparent focus-within:ring-2 focus-within:ring-blue-100 focus-within:bg-white transition-all">
                    <input 
                        type="text" 
                        id="chat-input" 
                        wire:model.live="newMessage" 
                        @input="$wire.userTyping()"
                        placeholder="Type a message..." 
                        autocomplete="off" 
                        spellcheck="false"
                        class="flex-1 bg-transparent border-none focus:ring-0 text-gray-800 py-2.5"
                    >
                    <button type="submit" class="ml-2 bg-blue-500 text-white rounded-full p-2 hover:bg-blue-600 transition shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                    </button>
                </form>
            </div>
        @else
            {{-- Empty State --}}
            <div class="flex-1 flex items-center justify-center text-gray-400 font-bold uppercase tracking-widest text-sm">Select a contact to chat</div>
        @endif
    </div>
</div>

{{-- Scripts Section --}}
@script
<script>
    document.addEventListener('livewire:initialized', () => {
        const typingUsers = [];
        const chatMessages = document.getElementById('chat-messages');

        // DEBUG: Confirm script initialization
        console.log('Typing script initialized. Listening for Livewire events...');

        Livewire.on('userTyping', (event) => {
            // DEBUG: Outbound signal
            console.log(`%c SENDING WHISPER: You are typing to User ID: ${event.selectedUserID}`, 'color: #3b82f6; font-weight: bold;');

            // Send whisper to other user
            window.Echo.private(`chat.${event.selectedUserID}`)
                .whisper('typing', {
                    userID: event.userID,
                    UserName: event.UserName
                });
        });

        // Listen for typing from other users
        window.Echo.private(`chat.${@js(Auth::id())}`)
            .listenForWhisper('typing', (payload) => {
                // DEBUG: Inbound signal
                console.log(`%c RECEIVED WHISPER: ${payload.UserName} is typing...`, 'color: #10b981; font-weight: bold;');
                console.dir(payload);

                if (!typingUsers.includes(payload.UserName)) {
                    typingUsers.push(payload.UserName);
                }
                updateTyping();

                setTimeout(() => {
                    const index = typingUsers.indexOf(payload.UserName);
                    if (index > -1) {
                        typingUsers.splice(index, 1);
                        // DEBUG: Clean up
                        console.log(`Typing timeout reached for ${payload.UserName}`);
                    }
                    updateTyping();
                }, 3000);
            });

        function updateTyping() {
            const container = chatMessages;
            if (!container) return;

            // Clear existing indicators
            container.querySelectorAll('.typing-indicator').forEach(el => el.remove());

            typingUsers.forEach(user => {
                const div = document.createElement('div');
                div.classList.add('px-4', 'pb-1', 'text-xs', 'text-gray-400', 'italic', 'typing-indicator');
                div.innerText = `${user} is typing...`;
                container.appendChild(div);
            });

            // Scroll to bottom when indicator appears
            container.scrollTop = container.scrollHeight;
        }
    });

    Livewire.hook('morph.updated', ({ el, component }) => {
        const container = document.getElementById('chat-messages');
        if (container) {
            const isNearBottom = (container.scrollHeight - container.scrollTop - container.clientHeight) < 250;
            if (isNearBottom) container.scrollTop = container.scrollHeight;
        }
    });
</script>
@endscript