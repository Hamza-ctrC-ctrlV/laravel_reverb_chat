<div class="flex h-screen bg-[#0f172a] text-white overflow-hidden selection:bg-sky-500/30" 
     wire:loading.class="opacity-50" 
     wire:target="selectUser"
     x-data="{ showUserMenu: @entangle('showUserMenu') }">
    
    <style>
        [x-cloak] { display: none !important; }
        
        /* --- CUSTOM SCROLLBAR CONFIGURATION --- */
        /* Apply this class to any scrollable element */
        .custom-scrollbar::-webkit-scrollbar { 
            width: 5px; 
        }
        .custom-scrollbar::-webkit-scrollbar-track { 
            background: transparent; /* Makes the track invisible */
        }
        .custom-scrollbar::-webkit-scrollbar-thumb { 
            background: rgba(255, 255, 255, 0.1); 
            border-radius: 20px; 
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { 
            background: rgba(14, 165, 233, 0.5); /* Sky-500 on hover */
        }

        /* UI Accents */
        .glass-sidebar { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(15px); border-right: 1px solid rgba(255, 255, 255, 0.08); }
        .glass-header { background: rgba(15, 23, 42, 0.8); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(255, 255, 255, 0.08); }
        .bubble-sent { border-bottom-right-radius: 4px !important; background: #0ea5e9; }
        .bubble-received { border-bottom-left-radius: 4px !important; background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.05); }
    </style>

    {{-- Navigation Rail --}}
    <aside class="w-20 flex flex-col items-center py-8 glass-sidebar z-50">
        <button @click="showUserMenu = !showUserMenu" 
                class="w-12 h-12 mb-10 rounded-2xl flex items-center justify-center transition-all duration-300 active:scale-90 group relative focus:outline-none"
                :class="showUserMenu ? 'bg-sky-500 shadow-[0_0_20px_rgba(14,165,233,0.6)]' : 'bg-white/5 hover:bg-white/10'">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
        <nav class="flex flex-col gap-8 flex-grow">
            {{-- Icons here --}}
        </nav>
        <button @click="showUserMenu = !showUserMenu" 
                class="w-10 h-10 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-xs font-bold text-gray-400 hover:border-sky-500 hover:text-white transition-all focus:outline-none">
            {{ auth()->user()->initials() }}
        </button>
    </aside>

    {{-- Slide-out Menu --}}
    <div class="fixed inset-y-0 left-20 z-40 w-64 bg-[#0f172a]/95 backdrop-blur-2xl border-r border-white/10 transform transition-all duration-300"
        x-show="showUserMenu" x-cloak @click.away="showUserMenu = false">
        <div class="flex flex-col h-full p-6">
            <h2 class="text-[10px] font-black uppercase tracking-widest text-gray-500 mb-8">Account</h2>
            <nav class="space-y-3">
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-4 px-4 py-3 rounded-xl text-gray-400 hover:bg-white/5 hover:text-white transition-all group">
                    <i class="fa-solid fa-user-gear group-hover:text-sky-500"></i>
                    <span class="text-sm font-medium">Edit Profile</span>
                </a>
                <form method="POST" action="{{ route('logout') }}">@csrf
                    <button type="submit" class="w-full flex items-center gap-4 px-4 py-3 rounded-xl text-gray-400 hover:bg-red-500/10 hover:text-red-400 transition-all text-left focus:outline-none">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span class="text-sm font-medium ml-4">Logout</span>
                    </button>
                </form>
            </nav>
        </div>
    </div>

    {{-- Sidebar Contact List --}}
    <div class="w-1/4 flex flex-col glass-sidebar relative z-10">
        <div class="p-6">
            <h2 class="text-2xl font-bold text-white mb-6 tracking-tight">Messages</h2>
            <div class="relative group">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 text-xs"></i>
                <input type="text" wire:model.live="searchEmail" placeholder="Search email..." 
                    class="w-full text-sm bg-white/5 border border-white/10 rounded-xl px-10 py-3 text-gray-200 focus:shadow-[0_0_20px_rgba(14,165,233,0.3)] focus:bg-white/[0.08] outline-none transition-all">
            </div>
        </div>
        
        {{-- ADDED: custom-scrollbar class here --}}
        <div class="flex-1 overflow-y-auto px-3 space-y-1 custom-scrollbar">
            @foreach($this->users as $user)
                <div wire:key="user-row-{{ $user->id }}" wire:click="selectUser({{ $user->id }})" 
                     class="flex items-center p-4 rounded-2xl cursor-pointer transition-all duration-200 group {{ $selectedUser && $selectedUser->id == $user->id ? 'bg-sky-500/10 border border-sky-500/20' : 'hover:bg-white/5 border border-transparent' }}">
                    <div class="relative">
                        <div class="w-12 h-12 bg-slate-800 rounded-full flex items-center justify-center text-white font-bold border border-white/10">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        @if(in_array($user->id, $onlineUsers))
                            <div class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-green-500 rounded-full border-2 border-[#0f172a]"></div>
                        @endif
                    </div>
                    <div class="ml-3 flex-1 overflow-hidden">
                        <div class="flex justify-between items-center text-sm">
                            <h3 class="font-semibold {{ $selectedUser && $selectedUser->id == $user->id ? 'text-sky-400' : 'text-gray-200' }} truncate">{{ $user->name }}</h3>
                            @if($user->unread_count > 0)
                                <span class="bg-sky-500 text-white text-[10px] px-1.5 py-0.5 rounded-full font-bold">{{ $user->unread_count }}</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 truncate mt-0.5">{{ $user->last_message_preview ?? 'No messages yet' }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Chat Window --}}
    <main class="flex-1 flex flex-col min-h-0 relative bg-slate-900/40"
          x-data="{
            userScrolledUp: false,
            typingUsers: [],
            scrollToBottom(behavior = 'smooth') { 
                const container = document.getElementById('chat-messages');
                if (container) { 
                    container.scrollTo({ top: container.scrollHeight, behavior: behavior }); 
                    this.userScrolledUp = false; 
                }
            },
            handleScroll() {
                const container = document.getElementById('chat-messages');
                if (container) { this.userScrolledUp = (container.scrollHeight - container.scrollTop - container.clientHeight) > 200; }
            }
          }"
          x-init="setTimeout(() => scrollToBottom('auto'), 100);">
        
        @if($selectedUser)
            <header class="glass-header p-4 px-8 flex justify-between items-center sticky top-0 z-10">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-sky-500 rounded-full flex items-center justify-center text-white font-bold text-xs">
                        {{ strtoupper(substr($selectedUser->name, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="font-bold text-sm tracking-wide">{{ $selectedUser->name }}</h3>
                        <div class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-widest">
                            <span class="w-1.5 h-1.5 {{ in_array($selectedUser->id, $onlineUsers) ? 'bg-green-500 animate-pulse' : 'bg-gray-600' }} rounded-full"></span>
                            <span class="text-gray-400">{{ in_array($selectedUser->id, $onlineUsers) ? 'Online' : 'Offline' }}</span>
                        </div>
                    </div>
                </div>
            </header>

            {{-- ADDED: custom-scrollbar class here --}}
            <div id="chat-messages" @scroll.debounce.50ms="handleScroll" class="flex-1 overflow-y-auto p-8 space-y-6 scroll-smooth custom-scrollbar">
                @foreach($messages as $message)
                    <div wire:key="msg-{{ $message['id'] ?? $loop->index }}" class="flex {{ $message['sender_id'] == $currentUserId ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[75%] lg:max-w-md">
                            <div class="rounded-[20px] px-5 py-3 text-sm shadow-xl {{ $message['sender_id'] == $currentUserId ? 'bubble-sent text-white' : 'bubble-received text-gray-200' }}">
                                <p class="leading-relaxed">{{ $message['message'] }}</p>
                            </div>
                            <div class="flex items-center mt-2 px-1 text-[10px] text-gray-500 font-bold {{ $message['sender_id'] == $currentUserId ? 'justify-end' : 'justify-start' }}">
                                <span>{{ \Carbon\Carbon::parse($message['created_at'])->format('g:i A') }}</span>
                                @if($message['sender_id'] == $currentUserId)
                                    <span class="ml-2 {{ $message['is_read'] ? 'text-sky-400' : '' }} uppercase tracking-widest">
                                        {{ $message['is_read'] ? 'Read' : 'Delivered' }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Floating Scroll to Bottom Button --}}
            <div x-show="userScrolledUp" 
                 x-cloak
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-10"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-10"
                 class="absolute bottom-24 right-1/2 translate-x-1/2 z-20">
                <button @click="scrollToBottom()" 
                        class="bg-slate-700/80 hover:bg-sky-500 text-white backdrop-blur-sm shadow-xl rounded-full px-4 py-2 transition-all flex items-center gap-2 text-xs font-bold border border-white/10 group">
                    <span class="group-hover:animate-bounce"><i class="fa-solid fa-arrow-down"></i></span>
                    <span>New Messages</span>
                </button>
            </div>

            <div class="p-6 pt-0 relative z-30">
                <div class="h-6 px-4 mb-2">
                    <template x-for="user in typingUsers" :key="user">
                        <div class="text-[10px] text-sky-400 font-bold italic" x-text="user + ' is typing...'"></div>
                    </template>
                </div>

                <form wire:submit.prevent="submit" 
                    x-on:submit="scrollToBottom('smooth')" 
                    class="flex items-center bg-white/5 border border-white/10 rounded-2xl p-1.5 focus-within:border-sky-500/50">
                    
                    <input type="text" 
                        id="chat-input" 
                        wire:model="newMessage" 
                        @input="$dispatch('send-whisper')" 
                        placeholder="Type a message..." 
                        autocomplete="off" 
                        class="flex-1 bg-transparent border-none outline-none focus:ring-0 text-gray-200 py-3 px-4 text-sm placeholder-gray-500">
                    
                    <button type="submit" 
                            class="w-11 h-11 bg-sky-500 text-white rounded-xl flex items-center justify-center hover:bg-sky-400 transition-all shadow-lg group">
                        <svg class="w-5 h-5 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                        </svg>
                    </button>
                </form>
            </div>
        @else
            <div class="flex-1 flex flex-col items-center justify-center text-center p-8 opacity-40">
                <i class="fa-solid fa-comments text-4xl mb-4"></i>
                <h2 class="text-lg font-bold">Select a contact</h2>
            </div>
        @endif
    </main>
</div>

@script
<script>
    document.addEventListener('livewire:initialized', () => {
        const alpine = Alpine.$data(document.querySelector('main'));

        window.Echo.private(`chat.${@js(Auth::id())}`)
            .listenForWhisper('typing', (payload) => {
                if (alpine && !alpine.typingUsers.includes(payload.UserName)) {
                    alpine.typingUsers.push(payload.UserName);
                    setTimeout(() => {
                        const index = alpine.typingUsers.indexOf(payload.UserName);
                        if (index > -1) alpine.typingUsers.splice(index, 1);
                    }, 2500);
                }
            });

        window.addEventListener('send-whisper', () => {
            const recipientId = @js($selectedUser?->id);
            if (recipientId) {
                window.Echo.private(`chat.${recipientId}`)
                    .whisper('typing', { UserName: @js(Auth::user()->name) });
            }
        });

        window.Echo.join('chat-presence')
            .here((u) => @this.updateOnlineUsers(u))
            .joining((u) => @this.userJoined(u))
            .leaving((u) => @this.userLeft(u));

        Livewire.hook('morph.updated', () => {
            const container = document.getElementById('chat-messages');
            if (container) {
                const isNearBottom = (container.scrollHeight - container.scrollTop - container.clientHeight) < 300;
                if (isNearBottom) container.scrollTop = container.scrollHeight;
            }
        });
    });
</script>
@endscript