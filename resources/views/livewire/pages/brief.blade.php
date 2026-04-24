<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.safety')] class extends Component
{
    public function sections()
    {
        return [
            [
                'title' => '1. Project Overview',
                'content' => 'QuickJHA is a web-based platform designed to automate the creation of high-quality safety documents. It targets construction managers, safety officers, and contractors who need rapid, compliant documentation. The platform uses AI to create three types of safety analysis documents:',
                'bullets' => [
                    'JHA – Job Hazard Analysis: Identifies hazards associated with each job step and recommends control measures.',
                    'AHA – Activity Hazard Analysis: A more detailed analysis used primarily in USACE (Army Corps of Engineers) projects.',
                    'JSA – Job Safety Analysis: Similar to JHA, focused on breaking down jobs into steps and identifying hazards.',
                ],
            ],
            [
                'title' => '2. Domain & Hosting',
                'content' => 'The platform is hosted at QuickJHA.com. The domain should be configured with SSL encryption, fast CDN delivery, and mobile-responsive hosting. The site must load in under 3 seconds on mobile devices.',
            ],
            [
                'title' => '3. Homepage Interface',
                'content' => 'The homepage features a clean, conversion-focused layout with three core service cards displaying transparent pricing. Each card links to the document creation form.',
                'table' => [
                    'headers' => ['Service', 'Price', 'Description'],
                    'rows' => [
                        ['JHA', '$15', 'Job Hazard Analysis – Standard safety document'],
                        ['AHA', '$20', 'Activity Hazard Analysis – Detailed USACE-compliant'],
                        ['JSA', '$12', 'Job Safety Analysis – Essential job step breakdown'],
                    ],
                ],
            ],
            [
                'title' => '4. User Accounts & Login System',
                'content' => 'Users can register and log in to access their dashboard. The system tracks all created documents and payment status.',
                'bullets' => [
                    'Email-based registration and login',
                    'Password recovery via email',
                    'User dashboard showing all past documents',
                    'Document status tracking (Created, Paid, Downloaded)',
                    'Re-download capability for paid documents',
                    'Admin access via admin@QuickJHA.com',
                ],
            ],
        ];
    }
}; ?>

<div class="pt-4 pb-20 px-4 max-w-4xl mx-auto min-h-screen">
    <header class="mb-16">
        <h1 class="text-4xl md:text-5xl font-black mb-4 tracking-tighter">Full Project Specification</h1>
        <p class="text-muted-foreground font-medium text-lg italic">QuickJHA Technical Requirements & Implementation Guide</p>
    </header>

    <div class="space-y-8" x-data="{ active: 0 }">
        @foreach($this->sections() as $i => $section)
            <div class="card-surface-lg overflow-hidden ring-1 ring-border shadow-soft hover:shadow-xl transition-all duration-500 bg-card">
                <button
                    @click="active = (active === {{ $i }} ? null : {{ $i }})"
                    class="w-full flex items-center justify-between p-8 md:p-10 text-left group"
                >
                    <h2 class="text-2xl font-black tracking-tight group-hover:text-primary transition-colors">{{ $section['title'] }}</h2>
                    <svg 
                        xmlns="http://www.w3.org/2000/svg" 
                        width="24" 
                        height="24" 
                        viewBox="0 0 24 24" 
                        fill="none" 
                        stroke="currentColor" 
                        stroke-width="3" 
                        stroke-linecap="round" 
                        stroke-linejoin="round" 
                        class="text-muted-foreground shrink-0 transition-transform duration-300"
                        :class="{ 'rotate-180': active === {{ $i }} }"
                    >
                        <path d="m6 9 6 6 6-6"/>
                    </svg>
                </button>
                
                <div 
                    x-show="active === {{ $i }}" 
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 -translate-y-4"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="px-8 pb-10 md:px-10 md:pb-12 -mt-4"
                    style="display: none;"
                >
                    <p class="text-muted-foreground mb-8 text-base leading-relaxed font-medium">{{ $section['content'] }}</p>

                    @if(isset($section['bullets']))
                        <ul class="space-y-4 mb-8">
                            @foreach($section['bullets'] as $b)
                                <li class="flex items-start gap-4 text-foreground/80 text-sm font-semibold">
                                    <div class="w-2 h-2 rounded-full bg-primary mt-1.5 shrink-0 shadow-lg shadow-primary/20"></div>
                                    {{ $b }}
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    @if(isset($section['table']))
                        <div class="overflow-x-auto bg-secondary/30 rounded-2xl p-1 border border-border/50">
                            <table class="w-full text-left text-sm border-separate border-spacing-0">
                                <thead class="bg-card/50">
                                    <tr>
                                        @foreach($section['table']['headers'] as $h)
                                            <th class="px-6 py-4 font-black uppercase tracking-widest text-[10px] text-muted-foreground border-b border-border/50">
                                                {{ $h }}
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-border/30">
                                    @foreach($section['table']['rows'] as $row)
                                        <tr class="hover:bg-card/30 transition-colors">
                                            @foreach($row as $cell)
                                                <td class="px-6 py-4 font-bold text-foreground/80 lowercase italic">{{ $cell }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

        <div class="p-10 md:p-14 bg-foreground text-background rounded-[3rem] shadow-2xl relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-64 h-64 bg-primary/20 rounded-full blur-[80px] -translate-y-1/2 translate-x-1/3"></div>
            
            <h2 class="text-3xl font-black mb-8 tracking-tighter relative z-10">Technical Stack Summary</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-background/80 text-base font-medium relative z-10">
                @foreach([
                    'Laravel 11 + Livewire Volt',
                    'Tailwind CSS (Advanced Design System)',
                    'Alpine.js Interactivity',
                    'Full Auth System Replacement',
                    'Single Page Application (SPA) Mode',
                    'Mobile-First Premium Architecture',
                    'Lucide SVG Vector Icons',
                    'Modern Glassmorphism UI',
                ] as $item)
                    <div class="flex items-center gap-4 group/item">
                        <div class="w-6 h-6 rounded-lg bg-primary/20 flex items-center justify-center text-primary shrink-0 group-hover/item:bg-primary group-hover/item:text-white transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                        </div>
                        {{ $item }}
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
