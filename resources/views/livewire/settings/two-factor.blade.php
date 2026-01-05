<?php

use Laravel\Fortify\Actions\ConfirmTwoFactorAuthentication;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use Symfony\Component\HttpFoundation\Response;

new class extends Component {
    #[Locked]
    public bool $twoFactorEnabled;

    #[Locked]
    public bool $requiresConfirmation;

    #[Locked]
    public string $qrCodeSvg = '';

    #[Locked]
    public string $manualSetupKey = '';

    public bool $showModal = false;

    public bool $showVerificationStep = false;

    #[Validate('required|string|size:6', onUpdate: false)]
    public string $code = '';

    public function mount(DisableTwoFactorAuthentication $disableTwoFactorAuthentication): void
    {
        abort_unless(Features::enabled(Features::twoFactorAuthentication()), Response::HTTP_FORBIDDEN);

        if (Fortify::confirmsTwoFactorAuthentication() && is_null(auth()->user()->two_factor_confirmed_at)) {
            $disableTwoFactorAuthentication(auth()->user());
        }

        $this->twoFactorEnabled = auth()->user()->hasEnabledTwoFactorAuthentication();
        $this->requiresConfirmation = Features::optionEnabled(Features::twoFactorAuthentication(), 'confirm');
    }

    public function enable(EnableTwoFactorAuthentication $enableTwoFactorAuthentication): void
    {
        $enableTwoFactorAuthentication(auth()->user());
        if (! $this->requiresConfirmation) {
            $this->twoFactorEnabled = auth()->user()->hasEnabledTwoFactorAuthentication();
        }
        $this->loadSetupData();
        $this->showModal = true;
    }

    private function loadSetupData(): void
    {
        $user = auth()->user();
        try {
            $this->qrCodeSvg = $user?->twoFactorQrCodeSvg();
            $this->manualSetupKey = decrypt($user->two_factor_secret);
        } catch (Exception) {
            $this->addError('setupData', 'Failed to fetch setup data.');
            $this->reset('qrCodeSvg', 'manualSetupKey');
        }
    }

    public function showVerificationIfNecessary(): void
    {
        if ($this->requiresConfirmation) {
            $this->showVerificationStep = true;
            $this->resetErrorBag();
            return;
        }
        $this->closeModal();
    }

    public function confirmTwoFactor(ConfirmTwoFactorAuthentication $confirmTwoFactorAuthentication): void
    {
        $this->validate();
        $confirmTwoFactorAuthentication(auth()->user(), $this->code);
        $this->closeModal();
        $this->twoFactorEnabled = true;
    }

    public function resetVerification(): void
    {
        $this->reset('code', 'showVerificationStep');
        $this->resetErrorBag();
    }

    public function disable(DisableTwoFactorAuthentication $disableTwoFactorAuthentication): void
    {
        $disableTwoFactorAuthentication(auth()->user());
        $this->twoFactorEnabled = false;
    }

    public function closeModal(): void
    {
        $this->reset('code', 'manualSetupKey', 'qrCodeSvg', 'showModal', 'showVerificationStep');
        $this->resetErrorBag();
        if (! $this->requiresConfirmation) {
            $this->twoFactorEnabled = auth()->user()->hasEnabledTwoFactorAuthentication();
        }
    }

    public function getModalConfigProperty(): array
    {
        if ($this->twoFactorEnabled) {
            return [
                'title' => __('Security Active'),
                'description' => __('Two-factor authentication is now protecting your account.'),
                'buttonText' => __('Finish'),
            ];
        }
        if ($this->showVerificationStep) {
            return [
                'title' => __('Verify Device'),
                'description' => __('Enter the 6-digit code from your authenticator app.'),
                'buttonText' => __('Confirm Code'),
            ];
        }
        return [
            'title' => __('Setup 2FA'),
            'description' => __('Scan the QR code below using your preferred authenticator app.'),
            'buttonText' => __('Continue to Verify'),
        ];
    }
} ?>

{{-- Centered wrapper matching Profile/Password sections --}}
<section class="min-h-screen bg-[#0f172a] text-white p-8 flex justify-center selection:bg-sky-500/30">
    <div class="max-w-4xl w-full">
        <header class="mb-10">
            <h2 class="text-3xl font-bold tracking-tight text-white">Two-Factor Security</h2>
            <p class="text-gray-500 text-sm mt-2">Add an extra layer of protection to your account using TOTP.</p>
        </header>

        <div class="bg-white/5 border border-white/10 rounded-3xl p-8 backdrop-blur-xl shadow-2xl">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6" wire:cloak>
                <div class="max-w-xl">
                    @if ($twoFactorEnabled)
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-bold uppercase tracking-wider mb-4">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            {{ __('Active Protection') }}
                        </div>
                    @else
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-500/10 border border-red-500/20 text-red-400 text-xs font-bold uppercase tracking-wider mb-4">
                            <span class="w-2 h-2 rounded-full bg-red-500"></span>
                            {{ __('Not Secured') }}
                        </div>
                    @endif

                    <p class="text-gray-400 leading-relaxed">
                        {{ __('When enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone\'s Google Authenticator or Authy app.') }}
                    </p>
                </div>

                <div class="shrink-0">
                    @if ($twoFactorEnabled)
                        <button wire:click="disable" class="bg-red-500/10 hover:bg-red-500/20 text-red-500 border border-red-500/30 px-8 py-3 rounded-2xl font-bold transition-all active:scale-95 outline-none focus:outline-none ring-0">
                            {{ __('Disable 2FA') }}
                        </button>
                    @else
                        <button wire:click="enable" class="bg-sky-500 hover:bg-sky-400 text-white font-bold px-8 py-3 rounded-2xl transition-all shadow-lg shadow-sky-500/20 active:scale-95 outline-none focus:outline-none ring-0">
                            {{ __('Enable 2FA') }}
                        </button>
                    @endif
                </div>
            </div>

            @if ($twoFactorEnabled)
                <div class="mt-10 pt-10 border-t border-white/5">
                    <livewire:settings.two-factor.recovery-codes :$requiresConfirmation/>
                </div>
            @endif
        </div>
    </div>

    {{-- Setup Modal --}}
    <template x-teleport="body">
        <div x-show="$wire.showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" x-cloak x-transition>
            <div class="bg-[#18181b] border border-white/10 rounded-[32px] p-8 max-w-md w-full shadow-2xl relative">
                
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-white">{{ $this->modalConfig['title'] }}</h2>
                    <p class="text-sm text-gray-500 mt-2">{{ $this->modalConfig['description'] }}</p>
                </div>

                @if ($showVerificationStep)
                    <div class="space-y-6">
                        <input 
                            wire:model="code" 
                            type="text" 
                            maxlength="6"
                            placeholder="000000"
                            class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-center text-3xl tracking-[1rem] font-mono text-sky-400 outline-none transition-all duration-300 focus:border-sky-500/50 focus:ring-0 focus:shadow-[0_0_20px_rgba(14,165,233,0.2)]"
                        >
                        @error('code') <span class="text-red-400 text-xs text-center block">{{ $message }}</span> @enderror

                        <div class="flex gap-3">
                            <button @click="$wire.resetVerification()" class="flex-1 bg-white/5 hover:bg-white/10 text-gray-400 font-bold py-4 rounded-2xl transition-all">Back</button>
                            <button wire:click="confirmTwoFactor" class="flex-1 bg-sky-500 hover:bg-sky-400 text-white font-bold py-4 rounded-2xl transition-all shadow-lg shadow-sky-500/20">Verify</button>
                        </div>
                    </div>
                @else
                    <div class="flex flex-col items-center gap-8">
                        {{-- QR Code Glass Frame --}}
                        <div class="p-4 bg-white rounded-3xl shadow-[0_0_30px_rgba(255,255,255,0.1)]">
                            @empty($qrCodeSvg)
                                <div class="w-48 h-48 flex items-center justify-center bg-gray-100 animate-pulse rounded-xl">
                                    <svg class="w-8 h-8 text-gray-300 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                </div>
                            @else
                                <div class="w-48 h-48">{!! $qrCodeSvg !!}</div>
                            @endempty
                        </div>

                        <div class="w-full space-y-4">
                            <div class="bg-white/5 border border-white/10 rounded-2xl p-4 flex items-center justify-between">
                                <code class="text-sky-400 font-mono text-sm uppercase tracking-wider">{{ $manualSetupKey }}</code>
                                <button x-data="{ copied: false }" @click="navigator.clipboard.writeText('{{ $manualSetupKey }}'); copied = true; setTimeout(() => copied = false, 2000)" class="text-gray-500 hover:text-white transition-colors">
                                    <svg x-show="!copied" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                                    <svg x-show="copied" class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                </button>
                            </div>

                            <button wire:click="showVerificationIfNecessary" class="w-full bg-sky-500 hover:bg-sky-400 text-white font-bold py-4 rounded-2xl transition-all shadow-lg shadow-sky-500/20">
                                {{ $this->modalConfig['buttonText'] }}
                            </button>
                            
                            <button @click="$wire.closeModal()" class="w-full text-gray-500 text-sm font-medium hover:text-white transition-colors">Cancel Setup</button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </template>
</section>