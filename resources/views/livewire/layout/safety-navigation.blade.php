<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component {
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

@php
    $adminLinks = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard'],
        ['label' => 'Reviews', 'route' => 'admin.reviews'],
        ['label' => 'Pricing', 'route' => 'admin.pricing'],
        ['label' => 'Prompts', 'route' => 'admin.prompts'],
        ['label' => 'Users', 'route' => 'admin.users'],
        ['label' => 'Template', 'route' => 'admin.template'],
        ['label' => 'SEO', 'route' => 'admin.seo'],
    ];
@endphp

<nav x-data="{ 
    mobileOpen: false, 
    dropdownOpen: false
}" class="relative w-full z-[200] bg-white border-b border-gray-100 py-3 shadow-sm print:hidden">
    <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between">
        <!-- Logo -->
        <a href="/" wire:navigate class="flex items-center gap-2.5 group">
            <img src="/logo.svg" alt="QuickJHA Logo" class="h-16 w-auto object-contain transition-transform" />
        </a>

        <!-- Desktop Navigation -->
        <div class="hidden md:flex items-center gap-1">
            @auth
                @if(auth()->user()->role === 'admin')
                    @php
                        $adminLinks = [
                            ['label' => 'Dashboard', 'route' => 'admin.dashboard'],
                            ['label' => 'Professionals Reviews', 'route' => 'admin.reviews'],
                            ['label' => 'Pricing', 'route' => 'admin.pricing'],
                            ['label' => 'Users', 'route' => 'admin.users'],
                            ['label' => 'Template', 'route' => 'admin.template'],
                            ['label' => 'SEO', 'route' => 'admin.seo'],
                        ];
                    @endphp
                    @foreach($adminLinks as $link)
                        <a href="{{ route($link['route']) }}" wire:navigate
                            class="px-4 py-2 rounded-xl text-sm font-bold text-slate-600 hover:text-primary hover:bg-slate-50 transition-all">

                            {{ $link['label'] }}
                        </a>
                    @endforeach
                @else
                    <a href="{{ route('user-dashboard') }}" wire:navigate
                        class="px-4 py-2 rounded-xl text-sm font-bold {{ request()->routeIs('user-dashboard') ? 'text-primary bg-primary/5' : 'text-slate-600 hover:text-primary hover:bg-slate-50' }} transition-all">
                        Dashboard
                    </a>
                    <a href="{{ route('user.reviews') }}" wire:navigate
                        class="px-4 py-2 rounded-xl text-sm font-bold {{ request()->routeIs('user.reviews') ? 'text-primary bg-primary/5' : 'text-slate-600 hover:text-primary hover:bg-slate-50' }} transition-all">
                        Professionals Reviews
                    </a>
                    <a href="/" wire:navigate
                        class="px-4 py-2 rounded-xl text-sm font-bold text-slate-600 hover:text-primary hover:bg-slate-50 transition-all">
                        Home
                    </a>
                    <div class="relative group" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                        <button
                            class="px-4 py-2 rounded-xl text-sm font-bold text-slate-600 hover:text-primary hover:bg-slate-50 transition-all flex items-center gap-1">
                            Services
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" class="transition-transform group-hover:rotate-180">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="absolute left-0 mt-0 w-72 bg-white rounded-2xl shadow-2xl border border-slate-100 p-2 z-50 overflow-hidden">
                            <a href="{{ route('services.jha') }}" wire:navigate
                                class="flex items-center gap-3 p-3 rounded-xl hover:bg-primary/5 group/link transition-all">
                                <div
                                    class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary group-hover/link:bg-primary group-hover/link:text-white transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                        <line x1="12" x2="12" y1="8" y2="12" />
                                        <line x1="12" x2="12.01" y1="16" y2="16" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-slate-800">Job Hazard Analysis (JHA)</div>
                                    <div class="text-[10px] text-slate-500 font-medium">OSHA Compliant Format</div>
                                </div>
                            </a>
                            <a href="{{ route('services.aha') }}" wire:navigate
                                class="flex items-center gap-3 p-3 rounded-xl hover:bg-primary/5 group/link transition-all">
                                <div
                                    class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary group-hover/link:bg-primary group-hover/link:text-white transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                        <path d="m9 12 2 2 4-4" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-slate-800">Activity Hazard Analysis (AHA)</div>
                                    <div class="text-[10px] text-slate-500 font-medium">EM 385 Planning Format</div>
                                </div>
                            </a>
                            <a href="{{ route('services.jsa') }}" wire:navigate
                                class="flex items-center gap-3 p-3 rounded-xl hover:bg-primary/5 group/link transition-all">
                                <div
                                    class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary group-hover/link:bg-primary group-hover/link:text-white transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                        <path d="M8 12h8" />
                                        <path d="M8 16h5" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-slate-800">Job Safety Analysis (JSA)</div>
                                    <div class="text-[10px] text-slate-500 font-medium">Daily Safe Work Format</div>
                                </div>
                            </a>
                        </div>
                    </div>
                    <a href="{{ request()->is('/') ? '#about' : '/#about' }}" wire:navigate
                        class="px-4 py-2 rounded-xl text-sm font-bold text-slate-600 hover:text-primary hover:bg-slate-50 transition-all">
                        About
                    </a>
                    <a href="{{ request()->is('/') ? '#faq' : '/#faq' }}" wire:navigate
                        class="px-4 py-2 rounded-xl text-sm font-bold text-slate-600 hover:text-primary hover:bg-slate-50 transition-all">
                        FAQ
                    </a>
                    <a href="{{ request()->is('/') ? '#contact' : '/#contact' }}" wire:navigate
                        class="px-4 py-2 rounded-xl text-sm font-bold text-slate-600 hover:text-primary hover:bg-slate-50 transition-all">
                        Contact
                    </a>
                @endif
            @else
                <a href="/" wire:navigate
                    class="px-4 py-2 rounded-xl text-sm font-bold text-slate-600 hover:text-primary hover:bg-slate-50 transition-all">
                    Home
                </a>
                <div class="relative group" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <button
                        class="px-4 py-2 rounded-xl text-sm font-bold text-slate-600 hover:text-primary hover:bg-slate-50 transition-all flex items-center gap-1">
                        Services
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" class="transition-transform group-hover:rotate-180">
                            <path d="m6 9 6 6 6-6" />
                        </svg>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="absolute left-0 mt-0 w-72 bg-white rounded-2xl shadow-2xl border border-slate-100 p-2 z-50 overflow-hidden">
                        <a href="{{ route('services.jha') }}" wire:navigate
                            class="flex items-center gap-3 p-3 rounded-xl hover:bg-primary/5 group/link transition-all">
                            <div
                                class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary group-hover/link:bg-primary group-hover/link:text-white transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                    <line x1="12" x2="12" y1="8" y2="12" />
                                    <line x1="12" x2="12.01" y1="16" y2="16" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-sm font-bold text-slate-800">Job Hazard Analysis (JHA)</div>
                                <div class="text-[10px] text-slate-500 font-medium">OSHA Compliant Format</div>
                            </div>
                        </a>
                        <a href="{{ route('services.aha') }}" wire:navigate
                            class="flex items-center gap-3 p-3 rounded-xl hover:bg-primary/5 group/link transition-all">
                            <div
                                class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary group-hover/link:bg-primary group-hover/link:text-white transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                    <path d="m9 12 2 2 4-4" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-sm font-bold text-slate-800">Activity Hazard Analysis (AHA)</div>
                                <div class="text-[10px] text-slate-500 font-medium">EM 385 Planning Format</div>
                            </div>
                        </a>
                        <a href="{{ route('services.jsa') }}" wire:navigate
                            class="flex items-center gap-3 p-3 rounded-xl hover:bg-primary/5 group/link transition-all">
                            <div
                                class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary group-hover/link:bg-primary group-hover/link:text-white transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                    <path d="M8 12h8" />
                                    <path d="M8 16h5" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-sm font-bold text-slate-800">Job Safety Analysis (JSA)</div>
                                <div class="text-[10px] text-slate-500 font-medium">Daily Safe Work Format</div>
                            </div>
                        </a>
                    </div>
                </div>
                <a href="{{ request()->is('/') ? '#about' : '/#about' }}" wire:navigate
                    class="px-4 py-2 rounded-xl text-sm font-bold text-slate-600 hover:text-primary hover:bg-slate-50 transition-all">
                    About
                </a>
                <a href="{{ request()->is('/') ? '#faq' : '/#faq' }}" wire:navigate
                    class="px-4 py-2 rounded-xl text-sm font-bold text-slate-600 hover:text-primary hover:bg-slate-50 transition-all">
                    FAQ
                </a>
                <a href="{{ request()->is('/') ? '#contact' : '/#contact' }}" wire:navigate
                    class="px-4 py-2 rounded-xl text-sm font-bold text-slate-600 hover:text-primary hover:bg-slate-50 transition-all">
                    Contact
                </a>
            @endauth
        </div>

        <!-- Desktop Actions -->
        <div class="hidden md:flex items-center gap-3">
            @auth
                <div class="relative">
                    <button @click="dropdownOpen = !dropdownOpen" @click.away="dropdownOpen = false"
                        class="flex items-center gap-2.5 h-10 px-3 rounded-xl bg-secondary/80 ring-1 ring-border text-sm font-semibold text-secondary-foreground hover:bg-secondary transition-colors">
                        <div
                            class="h-7 w-7 rounded-lg bg-primary/10 text-primary flex items-center justify-center text-xs font-bold uppercase">
                            {{ substr(auth()->user()->name ?? auth()->user()->email, 0, 2) }}
                        </div>
                        <span
                            class="hidden lg:block text-xs text-muted-foreground">{{ auth()->user()->name ?? auth()->user()->email }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="transition-transform" :class="{ 'rotate-180': dropdownOpen }">
                            <path d="m6 9 6 6 6-6" />
                        </svg>
                    </button>

                    <div x-show="dropdownOpen" x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                        class="absolute right-0 mt-2 w-56 bg-card rounded-xl ring-1 ring-border shadow-xl p-2 z-50"
                        style="display: none;">
                        <div class="px-3 py-2 mb-1">
                            <p class="text-sm font-bold truncate">{{ auth()->user()->name ?? auth()->user()->email }}</p>
                            <div class="flex items-center gap-1 mt-1">
                                @if(auth()->user()->role === 'admin')
                                    <a href="{{ route('admin.dashboard') }}" wire:navigate
                                        class="flex items-center gap-2 hover:text-primary transition-colors">
                                        <span
                                            class="rounded-full bg-primary/10 text-primary border border-primary/20 text-[9px] font-black uppercase tracking-widest px-2 py-0.5">
                                            Admin Dashboard
                                        </span>
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="border-t border-border/50 my-1"></div>
                        <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('user-dashboard') }}"
                            wire:navigate
                            class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm hover:bg-secondary transition-colors font-bold">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                                class="text-primary">
                                <rect width="7" height="9" x="3" y="3" rx="1" />
                                <rect width="7" height="5" x="14" y="3" rx="1" />
                                <rect width="7" height="9" x="14" y="12" rx="1" />
                                <rect width="7" height="5" x="3" y="16" rx="1" />
                            </svg>
                            Dashboard
                        </a>
                        <div class="border-t border-border/50 my-1"></div>
                        <button wire:click="logout"
                            class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm text-destructive hover:bg-secondary transition-colors w-full text-left">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                <polyline points="16 17 21 12 16 7" />
                                <line x1="21" x2="9" y1="12" y2="12" />
                            </svg>
                            Logout
                        </button>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" wire:navigate
                    class="px-4 py-2 rounded-xl text-sm font-bold text-slate-600 hover:text-primary hover:bg-slate-50 transition-all">
                    Login
                </a>
                <a href="{{ route('register') }}" wire:navigate
                    class="group relative overflow-hidden px-6 py-2 bg-primary text-primary-foreground font-black uppercase tracking-widest text-[11px] rounded-xl shadow-lg shadow-primary/20 hover:brightness-110 active:scale-95 transition-all">
                    <div
                        class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-in-out z-0">
                    </div>
                    <span class="relative z-10">Sign up</span>
                </a>
            @endauth
        </div>

        <!-- Mobile Menu Toggle -->
        <button class="md:hidden p-2 rounded-lg transition-colors text-brand-dark hover:bg-slate-100"
            @click="mobileOpen = true">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="4" x2="20" y1="12" y2="12" />
                <line x1="4" x2="20" y1="6" y2="6" />
                <line x1="4" x2="20" y1="18" y2="18" />
            </svg>
        </button>
    </div>

    <!-- Mobile Drawer -->
    <div x-show="mobileOpen" class="fixed inset-0 z-[100]" style="display: none;">
        <!-- Backdrop -->
        <div x-show="mobileOpen" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm"
            @click="mobileOpen = false"></div>

        <!-- content -->
        <div x-show="mobileOpen" x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="fixed top-0 right-0 bottom-0 w-[280px] bg-white shadow-2xl flex flex-col z-[999] border-l border-gray-200">

            <div class="p-5 flex items-center justify-between border-b border-gray-100 bg-white">
                <img src="/logo.svg" alt="QuickJHA Logo" class="h-10 w-auto object-contain" />
                <button @click="mobileOpen = false"
                    class="p-2 rounded-lg hover:bg-gray-100 transition-colors text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-5 bg-white">
                <div class="space-y-1">
                    @auth
                        @if(auth()->user()->role === 'admin')
                            <div class="text-[11px] font-bold text-gray-400 uppercase tracking-widest px-4 mb-2">Admin</div>
                            <a href="{{ route('admin.dashboard') }}" wire:navigate @click="mobileOpen = false"
                                class="block px-4 py-3 rounded-xl text-[16px] font-bold {{ request()->routeIs('admin.dashboard') ? 'text-primary bg-primary/5' : 'text-brand-dark hover:bg-gray-50' }}">Dashboard</a>
                            <a href="{{ route('admin.reviews') }}" wire:navigate @click="mobileOpen = false"
                                class="block px-4 py-3 rounded-xl text-[16px] font-bold {{ request()->routeIs('admin.reviews') ? 'text-primary bg-primary/5' : 'text-brand-dark hover:bg-gray-50' }}">Reviews</a>
                            <a href="{{ route('admin.pricing') }}" wire:navigate @click="mobileOpen = false"
                                class="block px-4 py-3 rounded-xl text-[16px] font-bold {{ request()->routeIs('admin.pricing') ? 'text-primary bg-primary/5' : 'text-brand-dark hover:bg-gray-50' }}">Pricing</a>
                            <a href="{{ route('admin.prompts') }}" wire:navigate @click="mobileOpen = false"
                                class="block px-4 py-3 rounded-xl text-[16px] font-bold {{ request()->routeIs('admin.prompts') ? 'text-primary bg-primary/5' : 'text-brand-dark hover:bg-gray-50' }}">AI
                                Prompts</a>
                            <a href="{{ route('admin.users') }}" wire:navigate @click="mobileOpen = false"
                                class="block px-4 py-3 rounded-xl text-[16px] font-bold {{ request()->routeIs('admin.users') ? 'text-primary bg-primary/5' : 'text-brand-dark hover:bg-gray-50' }}">Users</a>
                            <a href="{{ route('admin.seo') }}" wire:navigate @click="mobileOpen = false"
                                class="block px-4 py-3 rounded-xl text-[16px] font-bold {{ request()->routeIs('admin.seo') ? 'text-primary bg-primary/5' : 'text-brand-dark hover:bg-gray-50' }}">SEO
                                Manager</a>
                            <div class="h-px bg-gray-100 my-4 mx-4"></div>
                        @else
                            <a href="{{ route('user-dashboard') }}" wire:navigate @click="mobileOpen = false"
                                class="block px-4 py-3 rounded-xl text-[16px] font-bold {{ request()->routeIs('user-dashboard') ? 'text-primary bg-primary/5' : 'text-brand-dark hover:bg-gray-50' }}">Dashboard</a>
                            <a href="{{ route('user.reviews') }}" wire:navigate @click="mobileOpen = false"
                                class="block px-4 py-3 rounded-xl text-[16px] font-bold {{ request()->routeIs('user.reviews') ? 'text-primary bg-primary/5' : 'text-brand-dark hover:bg-gray-50' }}">Professional
                                Reviews</a>
                            <div class="h-px bg-gray-100 my-4 mx-4"></div>
                        @endif
                    @endauth

                    <a href="/" wire:navigate @click="mobileOpen = false"
                        class="block px-4 py-3 rounded-xl text-[16px] font-bold {{ request()->is('/') ? 'text-primary bg-primary/5' : 'text-brand-dark hover:bg-gray-50' }}">Home</a>
                    <a href="{{ request()->is('/') ? '#services' : '/#services' }}" wire:navigate @click="mobileOpen = false"
                        class="block px-4 py-3 rounded-xl text-[16px] font-bold text-brand-dark hover:bg-gray-50">Services</a>
                    <a href="{{ request()->is('/') ? '#about' : '/#about' }}" wire:navigate @click="mobileOpen = false"
                        class="block px-4 py-3 rounded-xl text-[16px] font-bold text-brand-dark hover:bg-gray-50">About</a>
                    <a href="{{ request()->is('/') ? '#faq' : '/#faq' }}" wire:navigate @click="mobileOpen = false"
                        class="block px-4 py-3 rounded-xl text-[16px] font-bold text-brand-dark hover:bg-gray-50">FAQ</a>
                    <a href="{{ request()->is('/') ? '#contact' : '/#contact' }}" wire:navigate @click="mobileOpen = false"
                        class="block px-4 py-3 rounded-xl text-[16px] font-bold text-brand-dark hover:bg-gray-50">Contact</a>
                </div>
            </div>

            <div class="p-6 border-t border-gray-100 bg-white">
                @auth
                    <button wire:click="logout"
                        class="w-full py-4 bg-red-600 text-white rounded-xl font-bold flex items-center justify-center gap-2">
                        Logout
                    </button>
                @else
                    <div class="space-y-3">
                        <a href="{{ route('login') }}" wire:navigate @click="mobileOpen = false"
                            class="block w-full py-4 text-center border border-gray-200 rounded-xl font-bold text-black hover:bg-gray-50">
                            Login
                        </a>
                        <a href="{{ route('register') }}" wire:navigate @click="mobileOpen = false"
                            class="group relative overflow-hidden block w-full py-4 text-center bg-[#0ea5e9] text-white rounded-md font-bold shadow-lg shadow-primary/20">
                            <div
                                class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-in-out z-0">
                            </div>
                            <span class="relative z-10">Sign up</span>
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</nav>