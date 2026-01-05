<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component {
    public string $name = '';
    public string $email = '';

    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'string', 'lowercase', 'email', 'max:255',
                Rule::unique(User::class)->ignore($user->id)
            ],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();
        $this->dispatch('profile-updated', name: $user->name);
    }

    public function resendVerificationNotification(): void
    {
        $user = Auth::user();
        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));
            return;
        }
        $user->sendEmailVerificationNotification();
        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section class="min-h-screen bg-[#0f172a] text-white p-8 selection:bg-sky-500/30">
    {{-- Match the Header Style --}}
    <div class="max-w-4xl mx-auto">
        <header class="mb-10">
            <h2 class="text-3xl font-bold tracking-tight text-white">Profile Settings</h2>
            <p class="text-gray-500 text-sm mt-2">Update your account information and email address.</p>
        </header>

        {{-- Glassmorphism Container --}}
        <div class="bg-white/5 border border-white/10 rounded-3xl p-8 backdrop-blur-xl shadow-2xl">
            <form wire:submit="updateProfileInformation" class="space-y-8">
                
                {{-- Name Input with Glow --}}
                <div class="space-y-2">
                    <label class="text-xs font-black uppercase tracking-widest text-gray-500 ml-1">Full Name</label>
                    <input wire:model="name" type="text" required autofocus
                        class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-gray-200 outline-none transition-all duration-300 focus:border-sky-500/50 focus:ring-0 focus:shadow-[0_0_20px_rgba(14,165,233,0.3)] focus:bg-white/[0.08]">
                </div>

                {{-- Email Input with Glow --}}
                <div class="space-y-2">
                    <label class="text-xs font-black uppercase tracking-widest text-gray-500 ml-1">Email Address</label>
                    <input wire:model="email" type="email" required
                        class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-gray-200 outline-none transition-all duration-300 focus:border-sky-500/50 focus:ring-0 focus:shadow-[0_0_20px_rgba(14,165,233,0.3)] focus:bg-white/[0.08]">

                    @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                        <div class="mt-4 p-4 bg-amber-500/10 border border-amber-500/20 rounded-xl">
                            <p class="text-sm text-amber-200 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                {{ __('Your email address is unverified.') }}
                            </p>
                            <button type="button" wire:click.prevent="resendVerificationNotification" class="text-sky-400 hover:text-sky-300 text-xs font-bold mt-2 underline decoration-sky-500/30 underline-offset-4">
                                {{ __('Click here to re-send the verification email.') }}
                            </button>

                            @if (session('status') === 'verification-link-sent')
                                <p class="mt-2 text-xs font-bold text-green-400">
                                    {{ __('A new verification link has been sent to your email address.') }}
                                </p>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center gap-6 pt-4">
                    <button type="submit" 
                        class="bg-sky-500 hover:bg-sky-400 text-white font-bold px-8 py-3 rounded-2xl transition-all duration-300 shadow-lg shadow-sky-500/20 active:scale-95 focus:outline-none ring-0">
                        {{ __('Save Changes') }}
                    </button>

                    <x-action-message class="text-sky-400 font-bold text-sm" on="profile-updated">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            {{ __('Saved successfully') }}
                        </span>
                    </x-action-message>
                </div>
            </form>
        </div>

        {{-- Danger Zone Section --}}
        <div class="mt-12 p-8 bg-red-500/5 border border-red-500/10 rounded-3xl">
            <h3 class="text-red-400 font-bold mb-4 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Danger Zone
            </h3>
            <livewire:settings.delete-user-form />
        </div>
    </div>
</section>