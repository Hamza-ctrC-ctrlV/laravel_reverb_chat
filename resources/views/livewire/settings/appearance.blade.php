<?php

use Livewire\Volt\Component;

new class extends Component {
    // Logic remains handled by the global $flux or your system theme script
}; ?>

<section class="w-full">
    <header class="mb-10">
        <h2 class="text-3xl font-bold text-white">Appearance</h2>
        <p class="text-gray-500 mt-2">Customize how the application looks on your device.</p>
    </header>

    <div class="glass-panel rounded-3xl p-8">
        <div class="space-y-4">
            <label class="block text-xs font-bold uppercase text-gray-500 ml-1">Theme Preference</label>
            
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4" x-data>
                <button 
                    @click="$flux.appearance = 'light'"
                    :class="$flux.appearance === 'light' ? 'bg-sky-500/10 border-sky-500/50 text-sky-400 shadow-[0_0_20px_rgba(14,165,233,0.2)]' : 'bg-white/5 border-white/10 text-gray-400 hover:bg-white/10'"
                    class="flex items-center justify-center gap-3 px-6 py-4 rounded-2xl border transition-all duration-300 font-bold outline-none focus:outline-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z"/></svg>
                    Light
                </button>

                <button 
                    @click="$flux.appearance = 'dark'"
                    :class="$flux.appearance === 'dark' ? 'bg-sky-500/10 border-sky-500/50 text-sky-400 shadow-[0_0_20px_rgba(14,165,233,0.2)]' : 'bg-white/5 border-white/10 text-gray-400 hover:bg-white/10'"
                    class="flex items-center justify-center gap-3 px-6 py-4 rounded-2xl border transition-all duration-300 font-bold outline-none focus:outline-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    Dark
                </button>

                <button 
                    @click="$flux.appearance = 'system'"
                    :class="$flux.appearance === 'system' ? 'bg-sky-500/10 border-sky-500/50 text-sky-400 shadow-[0_0_20px_rgba(14,165,233,0.2)]' : 'bg-white/5 border-white/10 text-gray-400 hover:bg-white/10'"
                    class="flex items-center justify-center gap-3 px-6 py-4 rounded-2xl border transition-all duration-300 font-bold outline-none focus:outline-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    System
                </button>
            </div>
        </div>

        <div class="mt-8 p-4 bg-sky-500/5 border border-sky-500/10 rounded-2xl">
            <p class="text-xs text-gray-400 leading-relaxed text-center">
                Choosing <span class="text-sky-400 font-bold">System</span> will automatically sync your interface with your operating system's light or dark mode settings.
            </p>
        </div>
    </div>
</section>