<flux:sidebar sticky stashable class="bg-[#0f172a]/80 border-r border-white/5 backdrop-blur-2xl">
    {{-- Increased internal padding (px-4) and vertical spacing (space-y-3) --}}
    <flux:navlist variant="standalone" class="mt-8 px-4 space-y-3">
        
        {{-- Primary Action: Back to Chat --}}
        <flux:navlist.item 
            icon="chat-bubble-left-right" 
            href="{{ route('chat') }}" 
            {{-- Increased margin-bottom (mb-10) to separate the main action --}}
            class="mb-10 !rounded-2xl bg-sky-500/10 text-sky-400 border border-sky-500/20 hover:bg-sky-500/20 transition-all duration-300 shadow-[0_0_20px_rgba(14,165,233,0.2)] font-bold py-3.5"
        >
            Back to Chat
        </flux:navlist.item>

        {{-- Group Label with more bottom margin --}}
        <div class="px-3 mb-5">
            <span class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-600">Account Settings</span>
        </div>
            
        {{-- Profile Info --}}
        <flux:navlist.item 
            icon="user" 
            href="{{ route('profile.edit') }}" 
            :current="request()->routeIs('profile.edit')"
            class="!rounded-2xl py-3.5 transition-all duration-300 {{ request()->routeIs('profile.edit') 
                ? 'bg-sky-500/15 text-sky-400 border-sky-500/30 shadow-[0_0_25px_rgba(14,165,233,0.25)] ring-1 ring-sky-500/20' 
                : 'text-gray-500 hover:bg-white/5 hover:text-gray-300 border border-transparent' }}"
        >
            Profile Info
        </flux:navlist.item>

        {{-- Security --}}
        <flux:navlist.item 
            icon="key" 
            href="{{ route('user-password.edit') }}" 
            :current="request()->routeIs('user-password.edit')"
            class="!rounded-2xl py-3.5 transition-all duration-300 {{ request()->routeIs('user-password.edit') 
                ? 'bg-sky-500/15 text-sky-400 border-sky-500/30 shadow-[0_0_25px_rgba(14,165,233,0.25)] ring-1 ring-sky-500/20' 
                : 'text-gray-400 hover:bg-white/5 hover:text-gray-300 border border-transparent' }}"
        >
            Security
        </flux:navlist.item>

        {{-- Two-Factor --}}
        <flux:navlist.item 
            icon="shield-check" 
            href="{{ route('two-factor.show') }}" 
            :current="request()->routeIs('two-factor.show')"
            class="!rounded-2xl py-3.5 transition-all duration-300 {{ request()->routeIs('two-factor.show') 
                ? 'bg-sky-500/15 text-sky-400 border-sky-500/30 shadow-[0_0_25px_rgba(14,165,233,0.25)] ring-1 ring-sky-500/20' 
                : 'text-gray-400 hover:bg-white/5 hover:text-gray-300 border border-transparent' }}"
        >
            Two-Factor
        </flux:navlist.item>

    </flux:navlist>

    <flux:spacer />

    {{-- Logout Section with extra padding --}}
    <div class="px-4 pb-8">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" 
                class="w-full flex items-center gap-3 px-4 py-4 !rounded-2xl text-gray-500 hover:text-red-400 hover:bg-red-500/10 transition-all duration-300 group border border-transparent hover:border-red-500/20"
            >
                <flux:icon.arrow-right-start-on-rectangle class="w-5 h-5 transition-transform group-hover:translate-x-1" />
                <span class="text-xs font-black uppercase tracking-widest">Logout</span>
            </button>
        </form>
    </div>
</flux:sidebar>