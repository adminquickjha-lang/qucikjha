<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.safety')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('user-dashboard', absolute: false), navigate: true);
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
        <div class="card-surface-xl p-6 sm:p-8 bg-card ring-1 ring-border shadow-2xl">
            <div class="text-center pb-6 mb-4 border-b border-border/50">
                <a href="/" wire:navigate class="mx-auto block mb-4 transition-transform hover:scale-105">
                    <img src="/logo.svg" alt="QuickJHA Logo" class="h-14 w-auto mx-auto object-contain" />
                </a>
                <h2 class="text-2xl font-black tracking-tighter mb-1">Sign up</h2>
                <p class="text-sm text-muted-foreground font-medium">Sign up to create safety documents instantly.</p>
            </div>

            <form wire:submit="register" class="space-y-4">
                <!-- Name -->
                <div class="space-y-3">
                    <label for="name"
                        class="text-[11px] font-black uppercase tracking-[0.15em] text-slate-800 ml-1">Full
                        Name</label>
                    <div class="relative group">
                        <div
                            class="absolute left-4 top-1/2 -translate-y-1/2 text-primary/40 group-focus-within:text-primary transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                        </div>
                        <input wire:model="name" id="name"
                            class="w-full h-12 pl-12 pr-4 bg-slate-50 border-2 border-slate-100 rounded-2xl text-sm font-bold text-slate-900 placeholder:text-slate-400 placeholder:font-medium focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all duration-300"
                            type="text" placeholder="Enter Your Full Name" required autofocus autocomplete="name" />
                    </div>
                    <x-input-error :messages="$errors->get('name')" class="mt-2 text-[10px] font-bold" />
                </div>

                <!-- Email Address -->
                <div class="space-y-3">
                    <label for="email"
                        class="text-[11px] font-black uppercase tracking-[0.15em] text-slate-800 ml-1">Email
                        Address</label>
                    <div class="relative group">
                        <div
                            class="absolute left-4 top-1/2 -translate-y-1/2 text-primary/40 group-focus-within:text-primary transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round">
                                <rect width="20" height="16" x="2" y="4" rx="2" />
                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                            </svg>
                        </div>
                        <input wire:model="email" id="email"
                            class="w-full h-12 pl-12 pr-4 bg-slate-50 border-2 border-slate-100 rounded-2xl text-sm font-bold text-slate-900 placeholder:text-slate-400 placeholder:font-medium focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all duration-300"
                            type="email" placeholder="Enter Your Email" required autocomplete="username" />
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-[10px] font-bold" />
                </div>

                <!-- Password -->
                <div class="space-y-3">
                    <label for="password"
                        class="text-[11px] font-black uppercase tracking-[0.15em] text-slate-800 ml-1">Password</label>
                    <div class="relative group" x-data="{ show: false }">
                        <div
                            class="absolute left-4 top-1/2 -translate-y-1/2 text-primary/40 group-focus-within:text-primary transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round">
                                <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                        </div>
                        <input wire:model="password" id="password"
                            class="w-full h-12 pl-12 pr-12 bg-slate-50 border-2 border-slate-100 rounded-2xl text-sm font-bold text-slate-900 placeholder:text-slate-400 placeholder:font-medium focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all duration-300"
                            :type="show ? 'text' : 'password'" placeholder="••••••••" required autocomplete="new-password" />
                        <button type="button" @click="show = !show"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-primary transition-colors focus:outline-none">
                            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg x-show="show" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.52 13.52 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-[10px] font-bold" />
                </div>

                <!-- Confirm Password -->
                <div class="space-y-3">
                    <label for="password_confirmation"
                        class="text-[11px] font-black uppercase tracking-[0.15em] text-slate-800 ml-1">Confirm
                        Password</label>
                    <div class="relative group" x-data="{ show: false }">
                        <div
                            class="absolute left-4 top-1/2 -translate-y-1/2 text-primary/40 group-focus-within:text-primary transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round">
                                <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                        </div>
                        <input wire:model="password_confirmation" id="password_confirmation"
                            class="w-full h-12 pl-12 pr-12 bg-slate-50 border-2 border-slate-100 rounded-2xl text-sm font-bold text-slate-900 placeholder:text-slate-400 placeholder:font-medium focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all duration-300"
                            :type="show ? 'text' : 'password'" placeholder="••••••••" required autocomplete="new-password" />
                        <button type="button" @click="show = !show"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-primary transition-colors focus:outline-none">
                            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg x-show="show" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.52 13.52 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')"
                        class="mt-2 text-[10px] font-bold" />
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="group relative overflow-hidden w-full h-12 bg-primary text-primary-foreground rounded-lg font-bold text-sm uppercase tracking-widest shadow-2xl shadow-primary/20 hover:brightness-110 active:scale-[0.98] transition-all flex items-center justify-center gap-3">
                        <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-in-out z-0"></div>
                        <span class="relative z-10 flex items-center justify-center gap-3">
                            Sign up
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12h14" />
                                <path d="m12 5 7 7-7 7" />
                            </svg>
                        </span>
                    </button>

                    <div class="mt-3">
                        <a href="{{ route('social.redirect', ['provider' => 'google']) }}"
                            class="group relative overflow-hidden w-full h-12 bg-white text-gray-900 ring-1 ring-border rounded-lg font-bold text-sm uppercase tracking-widest active:scale-[0.98] transition-all flex items-center justify-center gap-3">
                            <div class="absolute inset-0 bg-slate-100/50 translate-y-full group-hover:translate-y-0 transition-transform duration-500 ease-out z-0"></div>
                            <span class="relative z-10 flex items-center justify-center gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 488 512"
                                    fill="currentColor">
                                    <path
                                        d="M488 261.8C488 403.3 391.1 504 248 504 110.8 504 0 393.2 0 256S110.8 8 248 8c66.8 0 123 24.5 166.3 64.9l-67.5 64.9C258.5 52.6 94.3 116.6 94.3 256c0 86.5 69.1 156.6 153.7 156.6 98.2 0 135-70.4 140.8-106.9H248v-85.3h236.1c2.3 12.7 3.9 24.9 3.9 41.4z" />
                                </svg>
                                Continue with Google
                            </span>
                        </a>
                    </div>

                    <div class="mt-6 pt-6 border-t border-border/50 text-center">
                        <p class="text-[10px] font-black uppercase tracking-[0.1em] text-muted-foreground">
                            Already have an account?
                            <a href="{{ route('login') }}" wire:navigate class="text-primary hover:underline ml-1">Sign
                                In</a>
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>