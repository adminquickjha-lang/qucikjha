<?php

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;

new #[Layout('layouts.safety')] class extends Component {
    #[Locked]
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Mount the component.
     */
    public function mount(string $token): void
    {
        $this->token = $token;

        $this->email = request()->string('email');
    }

    /**
     * Reset the password for the given user.
     */
    public function resetPassword(): void
    {
        $this->validate([
            'token' => ['required'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $this->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) {
                $user->forceFill([
                    'password' => Hash::make($this->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status != Password::PASSWORD_RESET) {
            $this->addError('email', __($status));

            return;
        }

        Session::flash('status', __($status));

        $this->redirectRoute('login', navigate: true);
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

    <div class="w-full max-w-md relative z-10">
        <div class="card-surface-xl p-10 bg-card ring-1 ring-border shadow-2xl">
            <div class="text-center pb-8 mb-8 border-b border-border/50">
                <a href="/" wire:navigate class="mx-auto block mb-6 transition-transform hover:scale-105">
                    <img src="/logo.svg" alt="QuickJHA Logo" class="h-16 w-auto mx-auto object-contain" />
                </a>
                <h2 class="text-2xl font-black tracking-tighter mb-2">Create New Password</h2>
                <p class="text-sm text-muted-foreground font-medium uppercase tracking-[0.05em] leading-relaxed">
                    Choose a strong, secure password to regain access to your account.
                </p>
            </div>

            <form wire:submit="resetPassword" class="space-y-6">
                <!-- Email Address -->
                <div class="space-y-3">
                    <label for="email"
                        class="text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground ml-1">Email
                        Address</label>
                    <div class="relative group">
                        <div
                            class="absolute left-4 top-1/2 -translate-y-1/2 text-muted-foreground group-focus-within:text-primary transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round">
                                <rect width="20" height="16" x="2" y="4" rx="2" />
                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                            </svg>
                        </div>
                        <input wire:model="email" id="email"
                            class="w-full h-12 pl-12 pr-4 bg-secondary ring-1 ring-border rounded-lg text-sm font-bold focus:ring-2 focus:ring-primary focus:bg-card outline-none transition-all placeholder:text-muted-foreground/40"
                            type="email" placeholder="name@company.com" required readonly />
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-[10px] font-bold" />
                </div>

                <!-- Password -->
                <div class="space-y-3">
                    <label for="password"
                        class="text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground ml-1">New
                        Password</label>
                    <div class="relative group">
                        <div
                            class="absolute left-4 top-1/2 -translate-y-1/2 text-muted-foreground group-focus-within:text-primary transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round">
                                <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                        </div>
                        <input wire:model="password" id="password"
                            class="w-full h-12 pl-12 pr-4 bg-secondary ring-1 ring-border rounded-lg text-sm font-bold focus:ring-2 focus:ring-primary focus:bg-card outline-none transition-all placeholder:text-muted-foreground/40"
                            type="password" placeholder="••••••••" required autocomplete="new-password" />
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-[10px] font-bold" />
                </div>

                <!-- Confirm Password -->
                <div class="space-y-3">
                    <label for="password_confirmation"
                        class="text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground ml-1">Confirm New
                        Password</label>
                    <div class="relative group">
                        <div
                            class="absolute left-4 top-1/2 -translate-y-1/2 text-muted-foreground group-focus-within:text-primary transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round">
                                <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                        </div>
                        <input wire:model="password_confirmation" id="password_confirmation"
                            class="w-full h-12 pl-12 pr-4 bg-secondary ring-1 ring-border rounded-lg text-sm font-bold focus:ring-2 focus:ring-primary focus:bg-card outline-none transition-all placeholder:text-muted-foreground/40"
                            type="password" placeholder="••••••••" required autocomplete="new-password" />
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')"
                        class="mt-2 text-[10px] font-bold" />
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="w-full h-12 bg-primary text-primary-foreground rounded-lg font-bold text-sm uppercase tracking-widest shadow-2xl shadow-primary/20 hover:brightness-110 active:scale-[0.98] transition-all flex items-center justify-center gap-3">
                        Reset Password
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                            <path d="m9 12 2 2 4-4" />
                        </svg>
                    </button>

                    <div
                        class="mt-6 flex items-center justify-center gap-2 text-[10px] text-muted-foreground uppercase tracking-widest">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                            <path d="m9 12 2 2 4-4" />
                        </svg>
                        Secure Password Update
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>