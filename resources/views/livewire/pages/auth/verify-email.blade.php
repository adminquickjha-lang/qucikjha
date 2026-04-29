<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.safety')] class extends Component {
    /**
     * Send an email verification notification to the user.
     */
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('user-dashboard', absolute: false), navigate: true);

            return;
        }

        Auth::user()->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div class="min-h-[80vh] flex flex-col items-center justify-center px-4 pt-4 pb-12 relative overflow-hidden">
    <!-- Background decorations -->
    <div
        class="absolute top-1/3 left-1/4 w-[300px] h-[300px] bg-primary/5 rounded-full blur-[100px] pointer-events-none">
    </div>
    <div
        class="absolute bottom-1/4 right-1/4 w-[200px] h-[200px] bg-accent/5 rounded-full blur-[80px] pointer-events-none">
    </div>

    <div class="w-full max-w-lg relative z-10">
        <div class="card-surface-xl p-10 bg-card ring-1 ring-border shadow-2xl">
            <div class="text-center pb-8 mb-8 border-b border-border/50">
                <a href="/" wire:navigate class="mx-auto block mb-6 transition-transform hover:scale-105">
                    <img src="/logo.svg" alt="QuickJHA Logo" class="h-16 w-auto mx-auto object-contain" />
                </a>
                <h2 class="text-2xl font-black tracking-tighter mb-2">Verify Email</h2>
                <p class="text-sm text-muted-foreground font-medium uppercase tracking-widest">Action Required</p>
            </div>

            <div class="mb-8 text-sm text-muted-foreground leading-relaxed text-center font-medium">
                {{ __('Thanks for signing up! Before getting started, please verify your email address by clicking on the link we just emailed to you. If you didn\'t receive the email, we will gladly send you another.') }}
            </div>

            @if (session('status') == 'verification-link-sent')
                <div
                    class="mb-8 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 text-sm font-bold text-center animate-bounce">
                    {{ __('A new verification link has been sent to your email address.') }}
                </div>
            @endif

            <div class="space-y-4">
                <button wire:click="sendVerification"
                    wire:loading.attr="disabled"
                    class="w-full h-12 bg-primary text-primary-foreground rounded-lg font-bold text-sm uppercase tracking-widest shadow-2xl shadow-primary/20 hover:brightness-110 active:scale-[0.98] transition-all flex items-center justify-center gap-3 disabled:opacity-80 disabled:cursor-not-allowed disabled:scale-100">
                    <span wire:loading.remove wire:target="sendVerification">
                        {{ __('Resend Verification Email') }}
                    </span>
                    <span wire:loading wire:target="sendVerification" style="display:none;">
                        Sending...
                    </span>
                </button>

                <button wire:click="logout"
                    class="w-full h-12 bg-secondary text-secondary-foreground rounded-lg font-bold text-sm uppercase tracking-widest hover:bg-secondary/80 active:scale-[0.98] transition-all">
                    {{ __('Log Out') }}
                </button>
            </div>

            <div class="mt-8 pt-8 border-t border-border/50 text-center">
                <div
                    class="flex items-center justify-center gap-2 text-[10px] text-muted-foreground uppercase tracking-widest">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="20" height="16" x="2" y="4" rx="2" />
                        <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                    </svg>
                    Check your spam folder if not found
                </div>
            </div>
        </div>
    </div>
</div>