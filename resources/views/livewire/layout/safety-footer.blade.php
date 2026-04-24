<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<footer class="bg-brand-dark text-white print:hidden border-t border-white/5 relative overflow-hidden">
    <!-- Glow aesthetic for footer -->
    <div class="absolute top-0 left-1/4 w-64 h-64 bg-blue-500/5 rounded-full blur-[100px]"></div>

    <div class="max-w-7xl mx-auto px-4 relative z-10">
        <!-- Main Footer -->
        <div class="py-20 grid grid-cols-1 md:grid-cols-4 gap-12">
            <!-- Brand -->
            <div class="md:col-span-1">
                <a href="/" wire:navigate class="flex items-center mb-4 group">
                    <img src="/logo.svg" alt="QuickJHA Logo" class=w-auto object-contain transition-transform
                        brightness-110" />
                </a>
                <p class="text-sm text-slate-400 leading-relaxed mb-6 font-medium">
                    Professional Tech-powered safety document creation. OSHA & Cal/OSHA compliant. Trusted by
                    thousands of safety experts globally.
                </p>
                <!-- <div class="flex gap-4">
                    <div
                        class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center hover:bg-primary/20 transition-all cursor-pointer">
                        <svg class="w-5 h-5 text-slate-400" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" />
                        </svg>
                    </div>
                    <div
                        class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center hover:bg-primary/20 transition-all cursor-pointer">
                        <svg class="w-5 h-5 text-slate-400" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.84 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" />
                        </svg>
                    </div>
                </div> -->
            </div>

            <!-- Quick Links -->
            <div>
                <h4 class="font-black text-sm mb-8 uppercase tracking-[0.2em] text-white">Services</h4>
                <ul class="space-y-4">
                    <li><a href="{{ route('services.jha') }}" wire:navigate
                            class="text-sm text-slate-400 hover:text-primary transition-colors font-medium flex items-center gap-2"><span
                                class="w-1 h-1 rounded-full bg-primary/40"></span> JHA – Job Hazard Analysis</a></li>
                    <li><a href="{{ route('services.aha') }}" wire:navigate
                            class="text-sm text-slate-400 hover:text-primary transition-colors font-medium flex items-center gap-2"><span
                                class="w-1 h-1 rounded-full bg-primary/40"></span> AHA – Activity Hazard Analysis</a>
                    </li>
                    <li><a href="{{ route('services.jsa') }}" wire:navigate
                            class="text-sm text-slate-400 hover:text-primary transition-colors font-medium flex items-center gap-2"><span
                                class="w-1 h-1 rounded-full bg-primary/40"></span> JSA – Job Safety Analysis</a></li>
                </ul>
            </div>

            <!-- Company -->
            <div>
                <h4 class="font-black text-sm mb-8 uppercase tracking-[0.2em] text-white">Company</h4>
                <ul class="space-y-4">
                    <li><a href="{{ route('terms') }}" wire:navigate
                            class="text-sm text-slate-400 hover:text-primary transition-colors font-medium">Terms of
                            Service</a></li>
                    <li><a href="{{ route('privacy') }}" wire:navigate
                            class="text-sm text-slate-400 hover:text-primary transition-colors font-medium">Privacy
                            Policy</a></li>
                    <li><a href="{{ route('refund') }}" wire:navigate
                            class="text-sm text-slate-400 hover:text-primary transition-colors font-medium">Refund
                            Policy</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div>
                <h4 class="font-black text-sm mb-8 uppercase tracking-[0.2em] text-white">Contact</h4>
                <ul class="space-y-4">
                    <li class="flex items-center gap-3 text-sm text-slate-400 font-medium">
                        <div
                            class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center text-primary shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round">
                                <rect width="20" height="16" x="2" y="4" rx="2" />
                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                            </svg>
                        </div>

                        <a href="mailto:admin@QuickJHA.com" class="hover:text-white transition">
                            admin@QuickJHA.com
                        </a>
                    </li>
                    <li class="flex items-center gap-3 text-sm text-slate-400 font-medium">
                        <div
                            class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center text-primary shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path
                                    d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l2.27-2.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                            </svg>
                        </div>

                        <a href="tel:+923007162279" class="hover:text-white transition">
                            +92 300 716 2279
                        </a>
                    </li>
                    <li class="flex items-center gap-3 text-sm text-slate-400 font-medium">
                        <div
                            class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center text-primary shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                                <circle cx="12" cy="10" r="3" />
                            </svg>
                        </div>
                        Building No 4, sector I-10/4, Islamabad
                    </li>
                </ul>
            </div>
        </div>

        <div class="h-px bg-white/5 w-full mb-1"></div>

        <!-- Bottom Bar -->
        <div class="py-10 flex flex-col md:flex-row justify-between items-center gap-6">
            <p class="text-[12px] font-bold text-slate-500 uppercase tracking-widest">
                © {{ date('Y') }} QuickJHA. All rights reserved.
            </p>
            <div class="flex flex-wrap gap-6 text-[11px] font-black uppercase tracking-[0.2em] text-slate-400">
                <div class="flex items-center gap-2 cursor-default">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                        <path d="m9 12 2 2 4-4" />
                    </svg>
                    OSHA
                </div>
                <div class="flex items-center gap-2 cursor-default">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z" />
                        <polyline points="14 2 14 8 20 8" />
                        <path d="m9 15 2 2 4-4" />
                    </svg>
                    EM 385-1-1
                </div>
                <div class="flex items-center gap-2 cursor-default">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                    </svg>
                    SSL Secured
                </div>
            </div>
        </div>
    </div>
</footer>