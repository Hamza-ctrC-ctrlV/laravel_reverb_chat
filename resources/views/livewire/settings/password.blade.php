<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component {
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');
            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');
        $this->dispatch('password-updated');
    }
}; ?>

{{-- Section with flex and justify-center is what actually centers the content --}}
<section class="min-h-screen bg-[#0f172a] text-white p-8 flex justify-center selection:bg-sky-500/30">
    
    {{-- This div limits the width and holds the alignment --}}
    <div class="max-w-4xl w-full">
        
        <header class="mb-10">
            <h2 class="text-3xl font-bold tracking-tight text-white">Security Settings</h2>
            <p class="text-gray-500 text-sm mt-2">Ensure your account is using a long, random password to stay secure.</p>
        </header>

        {{-- The Glassmorphism Card --}}
        <div class="bg-white/5 border border-white/10 rounded-3xl p-8 backdrop-blur-xl shadow-2xl">
            <form wire:submit="updatePassword" class="space-y-8">
                
                {{-- Current Password --}}
                <div class="space-y-2">
                    <label class="text-xs font-black uppercase tracking-widest text-gray-500 ml-1">Current Password</label>
                    <input wire:model="current_password" type="password" required autocomplete="current-password"
                        class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-gray-200 outline-none transition-all duration-300 focus:border-sky-500/50 focus:ring-0 focus:shadow-[0_0_20px_rgba(14,165,233,0.3)] focus:bg-white/[0.08]">
                    @error('current_password') <span class="text-red-400 text-xs mt-1 block ml-1">{{ $message }}</span> @enderror
                </div>

                {{-- New Password --}}
                <div class="space-y-2">
                    <label class="text-xs font-black uppercase tracking-widest text-gray-500 ml-1">New Password</label>
                    <input wire:model="password" type="password" required autocomplete="new-password"
                        class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-gray-200 outline-none transition-all duration-300 focus:border-sky-500/50 focus:ring-0 focus:shadow-[0_0_20px_rgba(14,165,233,0.3)] focus:bg-white/[0.08]">
                    @error('password') <span class="text-red-400 text-xs mt-1 block ml-1">{{ $message }}</span> @enderror
                </div>

                {{-- Confirm Password --}}
                <div class="space-y-2">
                    <label class="text-xs font-black uppercase tracking-widest text-gray-500 ml-1">Confirm New Password</label>
                    <input wire:model="password_confirmation" type="password" required autocomplete="new-password"
                        class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-gray-200 outline-none transition-all duration-300 focus:border-sky-500/50 focus:ring-0 focus:shadow-[0_0_20px_rgba(14,165,233,0.3)] focus:bg-white/[0.08]">
                </div>

                <div class="flex items-center gap-6 pt-4">
                    <button type="submit" 
                        class="bg-sky-500 hover:bg-sky-400 text-white font-bold px-8 py-3 rounded-2xl transition-all duration-300 shadow-lg shadow-sky-500/20 active:scale-95">
                        {{ __('Update Password') }}
                    </button>

                    <x-action-message class="text-sky-400 font-bold text-sm" on="password-updated">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            {{ __('Changed successfully') }}
                        </span>
                    </x-action-message>
                </div>
            </form>
        </div>
    </div>
</section>