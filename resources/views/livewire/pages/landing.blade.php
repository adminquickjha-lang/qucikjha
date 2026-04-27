





<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Models\Setting;
use App\Mail\ContactFormMail;
use Illuminate\Support\Facades\Mail;

new #[Layout('layouts.safety', ['seoKey' => 'home'])] class extends Component {
    /** Contact form fields */
    public string $contactName = '';
    public string $contactEmail = '';
    public string $contactSubject = '';
    public string $contactMessage = '';
    public bool $messageSent = false;

    public function mount()
    {
        if (auth()->check() && auth()->user()->role === 'admin') {
            return $this->redirect(route('admin.dashboard'), navigate: true);
        }
    }

    /**
     * Validate and send the contact form email to Mailtrap.
     */
    public function sendMessage(): void
    {
        $this->validate([
            'contactName' => ['required', 'string', 'max:100'],
            'contactEmail' => ['required', 'email', 'max:255'],
            'contactSubject' => ['nullable', 'string', 'max:255'],
            'contactMessage' => ['required', 'string', 'min:10'],
        ], [
            'contactName.required' => 'Please enter your name.',
            'contactEmail.required' => 'Please enter your email address.',
            'contactEmail.email' => 'Please enter a valid email address.',
            'contactMessage.required' => 'Please enter a message.',
            'contactMessage.min' => 'Your message must be at least 10 characters.',
        ]);

        $formData = [
            'name' => $this->contactName,
            'email' => $this->contactEmail,
            'subject' => $this->contactSubject ?: 'General Inquiry',
            'message' => $this->contactMessage,
        ];

        defer(fn() => Mail::to(config('mail.from.address'))->send(new ContactFormMail($formData)));

        $this->reset('contactName', 'contactEmail', 'contactSubject', 'contactMessage');
        $this->messageSent = true;
    }

    public function services()
    {
        $jhaPrice = Setting::where('key', 'jha_price')->value('value') ?? '19.90';
        $ahaPrice = Setting::where('key', 'aha_price')->value('value') ?? '19.90';
        $jsaPrice = Setting::where('key', 'jsa_price')->value('value') ?? '19.00';

        return [
            [
                'title' => 'DEVELOP JHA NOW',
                'desc' => 'Job Hazard Analysis',
                'longDesc' => 'A professional assessment identifying hazards and controls per Cal/OSHA/ANSI. Includes RAC, tools/equipment specs, Competent Persons, and a dedicated Toolbox Talk (TBT) sheet.',
                'price' => '$' . $jhaPrice,
                'icon' => 'shield-alert',
                'type' => 'jha',
                'color' => 'from-blue-500 to-cyan-400',
                'bgColor' => 'bg-blue-50/50',
                'iconBg' => 'bg-blue-500/10',
                'features' => ['5+ Job Steps', 'Risk Assessment Codes', 'Control Measures', 'OSHA Compliant'],
            ],
            [
                'title' => 'DEVELOP AHA NOW',
                'desc' => 'Activity Hazard Analysis',
                'longDesc' => 'A specialized framework compliant with USACE and ANSI standards. Features activity-specific risk controls, RAC scoring, PPE requirements, and official sign-off sections.',
                'price' => '$' . $ahaPrice,
                'icon' => 'alert-triangle',
                'type' => 'aha',
                'color' => 'from-amber-500 to-orange-400',
                'bgColor' => 'bg-amber-50/50',
                'iconBg' => 'bg-amber-500/10',
                'features' => ['Initial & Residual RAC', 'PPE Matrix', 'EM 385-1-1 Refs', 'Training Reqs'],
            ],
            [
                'title' => 'DEVELOP JSA NOW',
                'desc' => 'Job Safety Analysis',
                'longDesc' => 'A high-level professional tool for identifying hazards and controls based on your selected standards. Documents responsible Competent Persons and provides a formal sign-off for site personnel.',
                'price' => '$' . $jsaPrice,
                'icon' => 'shield-check',
                'type' => 'jsa',
                'color' => 'from-emerald-500 to-teal-400',
                'bgColor' => 'bg-emerald-50/50',
                'iconBg' => 'bg-emerald-500/10',
                'features' => ['Safe Work Practices', 'Toolbox Talk Ready', 'Signature Fields', 'Quick Creation'],
            ],
        ];
    }

    public function howItWorks()
    {
        return [
            ['step' => '01', 'title' => 'Fill Project Details', 'desc' => 'Enter your project name, location, company info, and upload your logo.', 'icon' => 'file-check'],
            ['step' => '02', 'title' => 'Select Regulations', 'desc' => 'Choose applicable OSHA, Cal/OSHA, or EM 385-1-1 standards.', 'icon' => 'shield-check'],
            ['step' => '03', 'title' => 'Creates Document', 'desc' => 'Our Tech creates a comprehensive safety document with hazards, controls & risk ratings.', 'icon' => 'zap'],
            ['step' => '04', 'title' => 'Download & Use', 'desc' => 'Preview, pay, and download in PDF or Word format.', 'icon' => 'download'],
        ];
    }

    public function stats()
    {
        return [
            ['value' => '10,000+', 'label' => 'Documents Created', 'icon' => 'file-check'],
            ['value' => '30s', 'label' => 'Creation Time', 'icon' => 'clock'],
            ['value' => '100%', 'label' => 'OSHA Compliant', 'icon' => 'shield-check'],
            ['value' => '5,000+', 'label' => 'Safety Professionals', 'icon' => 'users'],
        ];
    }

    public function faqs()
    {
        return [
            [
                'question' => "What is a JHA and JSA difference?",
                'answer' => "JHA (Job Hazard Analysis) focuses on identifying hazards before they occur, while JSA (Job Safety Analysis) is a more prescriptive method that breaks down a job into steps to identify hazards and controls for each step. Our Tech ensures both are highly accurate and industry-specific."
            ],
            [
                'question' => "What is Activity Hazard Analysis (AHA)?",
                'answer' => "AHA is a documented process typically used in USACE projects to identify hazards and provide control measures for specific activities. It includes initial and residual risk assessment codes, ensuring full compliance with EM 385-1-1 standards."
            ],
            [
                'question' => "What does Job Safety Analysis (JSA)?",
                'answer' => "A JSA provides a structured way to identify and control hazards associated with each step of a job. It is essential for daily safety briefings and helps workers understand risks before starting a task."
            ],
            [
                'question' => "What is the role of Tech in safety documents?",
                'answer' => "Our Tech uses advanced algorithms to analyze your project details and industry-standard safety data to create comprehensive, compliant documents in seconds, reducing manual errors and saving hours of paperwork."
            ],
            [
                'question' => "How can payment be made?",
                'answer' => "We offer secure online payments through all major credit cards and digital wallets. You only pay for what you create, with no hidden subscription fees."
            ],
            [
                'question' => "What is the format of documentation?",
                'answer' => "All created documents are provided in high-quality PDF and editable Word formats, allowing you to easily print, share, or further customize them as needed."
            ],
        ];
    }
}; ?>

<div class="overflow-x-hidden">
    <!-- Hero Section -->
    <section class="relative min-h-[500px] flex items-center pt-12 pb-12 bg-slate-50 text-brand-dark">

        <!-- Animated Primary Color Background Blobs -->
        <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden">
            <!-- Large primary blob top-left -->
            <div class="absolute -top-20 -left-20 w-[500px] h-[500px] bg-primary/25 rounded-full blur-[100px] animate-blob"></div>
            <!-- Medium primary blob top-right -->
            <div class="absolute -top-10 -right-20 w-[400px] h-[400px] bg-sky-400/20 rounded-full blur-[90px] animate-blob animation-delay-2000"></div>
            <!-- Small accent blob bottom center -->
            <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-[350px] h-[350px] bg-primary/15 rounded-full blur-[80px] animate-blob animation-delay-4000"></div>

            <!-- Glass shimmer overlay -->
            <div class="absolute inset-0 bg-gradient-to-br from-white/40 via-white/10 to-transparent backdrop-blur-[1px]"></div>

            <!-- Subtle grid pattern -->
            <div class="absolute inset-0 opacity-[0.03]" style="background-image: linear-gradient(hsl(var(--primary)) 1px, transparent 1px), linear-gradient(90deg, hsl(var(--primary)) 1px, transparent 1px); background-size: 40px 40px;"></div>
        </div>

        <div class="container mx-auto px-4 relative z-10 mb-20 lg:mb-0">
            <div class="flex flex-col items-center justify-center text-center max-w-6xl mx-auto">
                
                <!-- Floating Glass Cards (Desktop) -->
                <div class="hidden xl:block">
                    <div class="absolute top-12 left-0 w-32 h-32 bg-primary/20 backdrop-blur-xl border border-primary/30 rounded-3xl flex items-center justify-center animate-bounce shadow-2xl shadow-primary/20" style="animation-duration: 4s;">
                        <div class="w-16 h-16 bg-primary/30 rounded-2xl flex items-center justify-center border border-primary/40">
                            <svg class="w-10 h-10 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path></svg>
                        </div>
                    </div>
                    <div class="absolute top-12 right-0 w-32 h-32 bg-primary/20 backdrop-blur-xl border border-primary/30 rounded-3xl flex items-center justify-center animate-bounce shadow-2xl shadow-primary/20" style="animation-duration: 6s;">
                        <div class="w-16 h-16 bg-primary/30 rounded-2xl flex items-center justify-center border border-primary/40">
                            <svg class="w-10 h-10 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"></path><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Text Content -->
                <div class="relative z-20 flex flex-col items-center mt-2 md:mt-4">
                    <h1 class="text-5xl md:text-7xl lg:text-7xl font-extrabold tracking-tight mb-4 leading-[1.2] text-brand-dark max-w-5xl mx-auto text-center">
                        Make Your Custom <br>
                        <span class="text-primary">JHA, AHA, JSA in minutes</span>
                    </h1>
                    
                    <p class="text-lg md:text-xl text-slate-600 max-w-3xl mx-auto mb-8 leading-relaxed font-medium opacity-90 text-center">
                        JHA, JSA & AHA in 30 Seconds Regulatory Compliant & Zero Effort.
                    </p>

                    <div class="mb-6 flex flex-wrap items-center justify-center gap-4 md:mb-0">
                        @foreach($this->services() as $service)
                            <a href="/examples/{{ $service['type'] }}.pdf" download
                                class="group relative inline-flex items-center justify-center w-full px-8 py-4 md:w-auto font-bold text-sm uppercase tracking-widest text-white transition-all duration-300 bg-primary rounded-2xl hover:brightness-110 shadow-xl shadow-primary/20 overflow-hidden">
                                <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-in-out"></div>
                                <span class="relative z-10 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                    {{ str_replace(['DEVELOP ', ' NOW'], '', $service['title']) }} SAMPLE
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>

        <!-- Overlapping Stats Bar -->
        <div class="absolute -bottom-10 left-1/2 -translate-x-1/2 w-full max-w-6xl px-4 z-50">
            <div class="bg-brand-dark/90 backdrop-blur-2xl border border-white/10 rounded-3xl py-6 px-8 shadow-[0_20px_50px_rgba(0,0,0,0.5)]">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:divide-x md:divide-white/10">
                    <!-- Item 1 -->
                    <div class="flex items-center justify-center gap-4 px-4">
                        <div class="w-10 h-10 rounded-full bg-green-500/20 flex items-center justify-center border border-green-500/30 shrink-0">
                            <svg class="w-6 h-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div class="text-left">
                            <div class="text-xl font-bold text-white leading-none">30</div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-1">Seconds</div>
                        </div>
                    </div>
                    <!-- Item 2 -->
                    <div class="flex items-center justify-center gap-4 px-4">
                        <div class="w-10 h-10 rounded-full bg-blue-500/20 flex items-center justify-center border border-blue-500/30 shrink-0">
                            <svg class="w-6 h-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        </div>
                        <div class="text-left">
                            <div class="text-xl font-bold text-white leading-none">Trust</div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-1">Regulatory Compliant</div>
                        </div>
                    </div>
                    <!-- Item 3 -->
                    <div class="flex items-center justify-center gap-4 px-4">
                        <div class="w-10 h-10 rounded-full bg-yellow-500/20 flex items-center justify-center border border-yellow-500/30 shrink-0">
                            <svg class="w-6 h-6 text-yellow-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        </div>
                        <div class="text-left">
                            <div class="text-xl font-bold text-white leading-none">5,000+</div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-1">Safety Professionals</div>
                        </div>
                    </div>
                    <!-- Item 4 -->
                    <div class="flex items-center justify-center gap-4 px-4">
                        <div class="w-10 h-10 rounded-full bg-cyan-500/20 flex items-center justify-center border border-cyan-500/30 shrink-0">
                            <svg class="w-6 h-6 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z"></path></svg>
                        </div>
                        <div class="text-left">
                            <div class="text-xl font-bold text-white leading-none">10,000+</div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-1">Safety Documents</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Client Logos Marquee -->
    <div class="bg-white py-8 border-b border-slate-100 overflow-hidden relative group">
        <div class="max-w-7xl mx-auto px-4 mb-8">
            <!-- <p class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 text-center">Trusted by Industry Leaders Worldwide</p> -->
        </div>
        
        <div class="flex items-center gap-4 animate-marquee whitespace-nowrap">
            @foreach(range(1, 4) as $batch)
                <div class="flex items-center gap-4 min-w-full justify-around shrink-0 px-2">
                    @foreach([
                            'EverGreen-480x298.jpg',
                            'Frankfurter-Zurich-480x161.jpg',
                            'On-Point-Drones-480x270.jpg',
                            'Optimal-Trading-BV-480x170.jpg',
                            'Sabriam-Logo-480x280.jpg',
                            'icgm-log-480x237.jpg',
                            'reliance-jpeg-480x146.jpg',
                            'Veren-Industries_LOGO_-01-480x179.png'
                        ] as $logo)
                                                                                                                                                            <div class="flex items-center justify-center transition-all duration-500 transform hover:scale-110 px-4">
                                                                                                                                                                <img src="/{{ $logo }}" alt="Client Logo" class="h-10 md:h-12 w-auto object-contain">
                                                                                                                                                            </div>
                    @endforeach
                </div>
            @endforeach
        </div>

        <style>
            @keyframes marquee {
                from { transform: translateX(0); }
                to { transform: translateX(-100%); }
            }
            .animate-marquee {
                animation: marquee 40s linear infinite;
            }
            .animate-marquee:hover {
                animation-play-state: paused;
            }
        </style>
    </div>

    <!-- Main Content Container -->
    <div class="bg-slate-50 py-10 border-y border-slate-100">
        <div class="max-w-7xl mx-auto px-4">
            <!-- Services / Pricing Section -->
            <section id="services" class="scroll-mt-24">
                <div class="text-center mb-10">
                    <span class="bg-primary/10 text-primary border border-primary/20 rounded-full px-4 py-1 text-xs font-semibold mb-4 inline-block">Our Services</span>
                    <h2 class="text-4xl md:text-5xl font-black mb-4 tracking-tight">Choose Your Safety Document</h2>
                    <p class="text-muted-foreground max-w-xl mx-auto font-medium">Transparent pricing. No hidden fees. Pay per document.</p>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    @foreach($this->services() as $s)
                        <div class="relative h-full">
                                @if(isset($s['popular']))
                                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 z-10">
                                        <span class="bg-gradient-to-r from-amber-500 to-orange-500 text-white border-0 shadow-lg px-5 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest flex items-center gap-1.5 animate-pulse">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="currentColor" class="text-white"><path d="M12 17.27 18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                            Most Popular
                                        </span>
                                    </div>
                                @endif

                                 <div class="card-surface p-6 flex flex-col h-full bg-primary/10 ring-1 ring-border transition-all duration-500">
                                    <div class="w-12 h-12 rounded-xl {{ $s['iconBg'] }} flex items-center justify-center mb-4 transition-transform">
                                        @if($s['icon'] === 'shield-alert')
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
                                        @elseif($s['icon'] === 'alert-triangle')
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
                                        @elseif($s['icon'] === 'shield-check')
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
                                        @endif
                                    </div>

                                    @if($s['type'] === 'jha')
                                        <a href="{{ route('services.jha') }}" wire:navigate class="hover:text-primary transition-colors">
                                            <h3 class="text-2xl font-bold mb-1">{{ $s['title'] }}</h3>
                                        </a>
                                    @elseif($s['type'] === 'aha')
                                        <a href="{{ route('services.aha') }}" wire:navigate class="hover:text-primary transition-colors">
                                            <h3 class="text-2xl font-bold mb-1">{{ $s['title'] }}</h3>
                                        </a>
                                    @elseif($s['type'] === 'jsa')
                                        <a href="{{ route('services.jsa') }}" wire:navigate class="hover:text-primary transition-colors">
                                            <h3 class="text-2xl font-bold mb-1">{{ $s['title'] }}</h3>
                                        </a>
                                    @else
                                        <h3 class="text-2xl font-bold mb-1">{{ $s['title'] }}</h3>
                                    @endif
                                    <p class="text-muted-foreground font-bold text-sm mb-3">{{ $s['desc'] }}</p>

                                    <p class="text-sm text-muted-foreground leading-relaxed mb-2 flex-grow">
                                        {{ $s['longDesc'] }}
                                    </p>

                                    @if($s['type'] === 'jha')
                                        <a href="{{ route('services.jha') }}" wire:navigate class="text-primary text-xs font-black uppercase tracking-widest hover:underline mb-2 inline-block">Learn more about JHA →</a>
                                    @elseif($s['type'] === 'aha')
                                        <a href="{{ route('services.aha') }}" wire:navigate class="text-primary text-xs font-black uppercase tracking-widest hover:underline mb-2 inline-block">Learn more about AHA →</a>
                                    @elseif($s['type'] === 'jsa')
                                        <a href="{{ route('services.jsa') }}" wire:navigate class="text-primary text-xs font-black uppercase tracking-widest hover:underline mb-2 inline-block">Learn more about JSA →</a>
                                    @endif

                                    <div class="flex items-baseline gap-2 mb-3">
                                        <span class="text-4xl font-black text-foreground">{{ $s['price'] }}</span>
                                        <span class="text-muted-foreground text-xs font-medium">/ document</span>
                                    </div>

                                    <div class="h-px bg-border/50 w-full mb-3"></div>

                                    <ul class="space-y-3 mb-6">
                                        @foreach($s['features'] as $feature)
                                            <li class="flex items-center gap-4 text-sm font-semibold">
                                                <div class="w-5 h-5 rounded-full bg-accent/20 flex items-center justify-center text-accent">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                                </div>
                                                <span class="text-foreground/80">{{ $feature }}</span>
                                            </li>
                                        @endforeach
                                    </ul>


                                    <a href="{{ route('generate.' . $s['type']) }}" wire:navigate class="group relative flex items-center justify-center w-full py-4 rounded-xl font-bold text-sm uppercase tracking-widest text-center transition-all bg-primary text-primary-foreground shadow-primary/25 shadow-xl hover:shadow-primary/50 overflow-hidden">
                                        <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-in-out"></div>
                                        <span class="relative z-10">DEVELOP {{ explode(' ', $s['title'])[1] }} now</span>
                                    </a>
                                </div>
                            </div>
                    @endforeach
                </div>  
            </section>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-10">
        <!-- How It Works Section -->
        <section id="how-it-works" class="scroll-mt-24">
        <div class="text-center mb-10">
            <span class="bg-primary/10 text-primary border border-primary/20 rounded-full px-4 py-1 text-xs font-semibold mb-4 inline-block">Workflow</span>
            <h2 class="text-5xl font-black mb-4 tracking-tight">How It Works</h2>
            <p class="text-muted-foreground max-w-xl mx-auto font-medium">From project details to a professional safety document in seconds.</p>
        </div>

        <div class="relative max-w-7xl mx-auto px-4">
            <!-- Connecting Line (Desktop Only) - Starts at step 1 center, ends at step 4 center -->
            <div class="hidden md:block absolute top-[24px] left-[12.5%] right-[12.5%] h-0.5 bg-slate-200 z-0"></div>

            <div class="grid md:grid-cols-4 gap-12 relative z-10">
                @foreach($this->howItWorks() as $i => $item)
                    <div class="flex flex-col items-center text-center group">
                        <!-- Number Circle -->
                        <div class="w-12 h-12 rounded-full bg-brand-dark border-4 border-white text-white font-black flex items-center justify-center mb-6 relative z-20 shadow-xl group-hover:scale-110 transition-transform">
                            {{ $item['step'] }}
                        </div>

                        <!-- Icon/Illustration Container -->
                        <div class="mb-6 h-20 flex items-end justify-center transition-transform duration-500 group-hover:-translate-y-2">
                            <div class="relative">
                                @if($item['icon'] === 'file-check')
                                    <div class="w-16 h-16 bg-blue-500/10 rounded-2xl flex items-center justify-center text-blue-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><path d="m9 15 2 2 4-4"/></svg>
                                    </div>
                                @elseif($item['icon'] === 'shield-check')
                                    <div class="w-16 h-16 bg-emerald-500/10 rounded-2xl flex items-center justify-center text-emerald-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
                                    </div>
                                @elseif($item['icon'] === 'zap')
                                    <div class="w-16 h-16 bg-amber-500/10 rounded-2xl flex items-center justify-center text-amber-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 14a2 2 0 0 1 2-2h3V6l11 11h-3v5L4 14z"/></svg>
                                    </div>
                                @elseif($item['icon'] === 'download')
                                    <div class="w-16 h-16 bg-cyan-500/10 rounded-2xl flex items-center justify-center text-cyan-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Text -->
                        <h3 class="font-black text-xl mb-3 tracking-tight group-hover:text-primary transition-colors">{{ $item['title'] }}</h3>
                        <div class="space-y-1">
                            @foreach(explode('. ', $item['desc']) as $line)
                                <p class="text-xs text-muted-foreground font-semibold leading-relaxed tracking-wide">
                                    {{ $line }}{{ !$loop->last ? '.' : '' }}
                                </p>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    </div>

    <!-- About Section (Full-Width Background) -->
    <div class="bg-slate-50 py-10 border-y border-slate-100 relative overflow-hidden group">
        <div class="absolute top-0 right-0 w-[400px] h-[400px] bg-primary/5 rounded-full blur-[100px] -translate-y-1/2 translate-x-1/2"></div>
        <div class="max-w-7xl mx-auto px-4 relative z-10">
            <section id="about" class="scroll-mt-24">
                <div class="grid lg:grid-cols-2 gap-16 items-center">
                    <div class="space-y-8">
                        <span class="bg-primary/10 text-primary border border-primary/20 rounded-full px-4 py-1 text-xs font-semibold inline-block">About QuickJHA</span>
                        <h2 class="text-4xl md:text-5xl font-black tracking-tight leading-[1.1]">
                            Leading the Way in <br /><span class="gradient-text italic text-primary">Safety Documents</span>
                        </h2>
                        <p class="text-muted-foreground text-lg leading-relaxed font-medium">
                            We started with a simple belief: safety compliance shouldn't be a bureaucratic nightmare. By combining years of expertise in occupational health and safety with cutting edge technology, we've developed a platform that allows safety professionals to focus on what truly matters saving lives.
                        </p>
                        <div class="space-y-6 pt-4">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary font-bold shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                </div>
                                <div>
                                    <span class="font-black text-lg block leading-none mb-1">Fast & Reliable</span>
                                    <span class="text-sm text-muted-foreground">Create documents in seconds, not hours.</span>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary font-bold shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                </div>
                                <div>
                                    <span class="font-black text-lg block leading-none mb-1">Collaborative</span>
                                    <span class="text-sm text-muted-foreground font-medium">From Fortune 500 to local contractors.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="relative" x-data="{ 
                        activeSlide: 0, 
                        slides: [
                            {
                                text: 'QuickJHA has saved our team hundreds of hours in paperwork this year alone.',
                                author: 'Mark Thompson, Safety Director'
                            },
                            {
                                text: 'The accuracy and OSHA compliance of these documents are unmatched.',
                                author: 'Sarah Jenkins, Project Manager'
                            },
                            {
                                text: 'Finally, a platform that truly understands strict EM 385-1-1 requirements.',
                                author: 'David Chen, Chief Engineer'
                            }
                        ]
                    }" x-init="setInterval(() => activeSlide = (activeSlide + 1) % slides.length, 5000)">
                        <div class="aspect-square bg-gradient-to-br from-primary via-blue-500 to-accent rounded-3xl p-1 shadow-2xl overflow-hidden group">
                            <div class="w-full h-full rounded-[1.45rem] bg-card overflow-hidden relative">
                                
                                <img 
                                    src="/about.jpg" 
                                    alt="Construction Safety" 
                                    class="w-full h-full absolute inset-0 object-cover transition-transform duration-1000 group-hover:scale-110 z-0"
                                />
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-90 z-10 pointer-events-none"></div>
                                
                                <div class="absolute bottom-10 left-6 right-6 z-20">
                                    <template x-for="(slide, index) in slides" :key="'text-'+index">
                                        <div x-show="activeSlide === index"
                                             x-transition:enter="transition ease-out duration-700 delay-300"
                                             x-transition:enter-start="opacity-0 translate-y-8"
                                             x-transition:enter-end="opacity-100 translate-y-0"
                                             x-transition:leave="transition ease-in duration-300 absolute w-full"
                                             x-transition:leave-start="opacity-100 translate-y-0"
                                             x-transition:leave-end="opacity-0 translate-y-8"
                                             class="bg-white/10 backdrop-blur-xl border border-white/20 p-6 md:p-8 rounded-3xl text-white text-center shadow-[0_8px_32px_0_rgba(0,0,0,0.3)] min-h-[140px] flex flex-col justify-center">
                                            <p class="text-base md:text-lg font-bold leading-tight" x-text="'&quot;' + slide.text + '&quot;'"></p>
                                            <div class="flex items-center gap-3 mt-5 justify-center">
                                                <div class="w-8 h-[2px] bg-primary rounded-full"></div>
                                                <span class="text-xs font-black text-white/90 uppercase tracking-widest" x-text="slide.author"></span>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <!-- Dots Nav -->
                                <div class="absolute bottom-4 left-0 right-0 flex justify-center gap-2 z-30">
                                    <template x-for="(slide, index) in slides" :key="'dot-'+index">
                                        <button @click="activeSlide = index" 
                                                class="w-2 h-2 rounded-full transition-all duration-300 shadow-xl"
                                                :class="activeSlide === index ? 'bg-primary w-6' : 'bg-white/50 hover:bg-white'"></button>
                                    </template>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    

    <!-- Industries Section -->
    <section id="industries" class="py-10 bg-white scroll-mt-24">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-10">
                <span class="bg-primary/10 text-primary border border-primary/20 rounded-full px-4 py-1 text-xs font-semibold mb-4 inline-block">Sectors We Serve</span>
                <h2 class="text-4xl md:text-5xl font-black mb-4 tracking-tight">Industries</h2>
                <p class="text-muted-foreground max-w-3xl mx-auto font-medium">Versatile safety document creation adapted for diverse industrial requirements.</p>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach([
                        ['name' => 'Construction & Demolition', 'icon' => 'hard-hat'],
                        ['name' => 'Manufacturing', 'icon' => 'factory'],
                        ['name' => 'Oil & Gas', 'icon' => 'droplets'],
                        ['name' => 'Electrical and Mechanical', 'icon' => 'bolt'],
                        ['name' => 'Transportation & Warehousing', 'icon' => 'truck'],
                        ['name' => 'Renovation & Cleaning', 'icon' => 'brush'],
                        ['name' => 'Services and Utilities', 'icon' => 'settings'],
                    ] as $industry)
                                                                                                                                                            <div class="p-8 rounded-3xl bg-slate-50 border border-slate-100 flex flex-col items-center text-center group hover:border-primary hover:shadow-2xl transition-all duration-500 relative overflow-hidden">
                                                                                                                                                                <div class="absolute inset-0 bg-primary translate-y-full group-hover:translate-y-0 transition-transform duration-500 ease-in-out z-0"></div>
                                                                                                                                                                <div class="relative z-10 w-12 h-12 rounded-2xl bg-primary/10 text-primary mb-4 flex items-center justify-center group-hover:bg-white group-hover:text-primary transition-colors duration-500">
                                                                                                                                                                    @if($industry['icon'] === 'hard-hat')
                                                                                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12a10 10 0 1 1 20 0H2Z"/><path d="M7 12V8a5 5 0 0 1 10 0v4"/></svg>
                                                                                                                                                                    @elseif($industry['icon'] === 'factory')
                                                                                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 20V4l-7 3V4l-7 3V4L3 7v13h17Z"/><path d="M3 13h17"/><path d="M3 17h17"/></svg>
                                                                                                                                                                    @elseif($industry['icon'] === 'droplets')
                                                                                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M7 16.3c2.2 0 4-1.8 4-4 0-3.3-4-6.3-4-6.3S3 9 3 12.3c0 2.2 1.8 4 4 4Z"/><path d="M17 19.3c1.7 0 3-1.3 3-3 0-2.5-3-4.7-3-4.7s-3 2.2-3 4.7c0 1.7 1.3 3 3 3Z"/></svg>
                                                                                                                                                                    @elseif($industry['icon'] === 'truck')
                                                                                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10 17H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v10"/><path d="M13 17h6l4-5V7h-4"/><circle cx="7.5" cy="17.5" r="2.5"/><circle cx="17.5" cy="17.5" r="2.5"/></svg>
                                                                                                                                                                    @elseif($industry['icon'] === 'bolt')
                                                                                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                                                                                                                                                                    @elseif($industry['icon'] === 'brush')
                                                                                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9.06 11.9 8.07-8.06a2.85 2.85 0 1 1 4.03 4.03l-8.06 8.08"/><path d="M7.07 14.94c-3.91.39-7.07 3.54-7.07 7.06h21.01c0-3.52-3.16-6.67-7.07-7.06"/><path d="M11 15v3"/><path d="M8 15v2"/><path d="M14 15v2"/></svg>
                                                                                                                                                                    @elseif($industry['icon'] === 'settings')
                                                                                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.1a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
                                                                                                                                                                    @endif
                                                                                                                                                                </div>
                                                                                                                                                                <span class="relative z-10 font-black text-[13px] md:text-sm tracking-widest text-brand-dark group-hover:text-white transition-colors duration-500">{{ $industry['name'] }}</span>
                                                                                                                                                            </div>
                @endforeach
            </div>
        </div>
    </section>



<!-- Key Features Section -->
    <div class="bg-slate-50 py-10 border-y border-slate-100">
        <div class="max-w-7xl mx-auto px-4">
        <section id="features" class="scroll-mt-24">
        <div class="text-center mb-10">
            <span class="bg-primary/10 text-primary border border-primary/20 rounded-full px-4 py-1 text-xs font-semibold mb-4 inline-block">Platform Excellence</span>
            <h2 class="text-4xl md:text-5xl font-black mb-4 tracking-tight text-brand-dark">Key Features</h2>
            <p class="text-slate-600 max-w-xl mx-auto font-medium">Why the biggest contractors trust our engine.</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach([
                    ['title' => 'Regulation-Compliant Safety Documents', 'desc' => 'OSHA, Cal/OSHA & EM 385-1-1 compliant documents created with Tech precision.', 'icon' => 'shield-check', 'color' => 'blue'],
                    ['title' => 'Create in 30 Seconds (Word & PDF)', 'desc' => 'High-speed creation in professional formats ready for immediate download.', 'icon' => 'zap', 'color' => 'cyan'],
                    ['title' => 'Professionally Formatted, Ready-to-Use', 'desc' => 'Audit-ready layouts that meet all corporate and government standards out of the box.', 'icon' => 'file-check', 'color' => 'emerald'],
                    ['title' => 'Project-Specific & Customizable Outputs', 'desc' => 'Tailor every document to your unique job site conditions and step-by-step tasks.', 'icon' => 'edit', 'color' => 'amber'],
                ] as $f)
                                                                                                                                                        <div class="p-8 rounded-3xl bg-white border border-slate-200 hover:border-primary/40 hover:shadow-2xl transition-all duration-500 group">
                                                                                                                                                            <div class="w-14 h-14 rounded-2xl bg-{{ $f['color'] }}-500/10 text-{{ $f['color'] }}-600 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                                                                                                                                                                @if($f['icon'] === 'shield-check')
                                                                                                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
                                                                                                                                                                @elseif($f['icon'] === 'zap')
                                                                                                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                                                                                                                                                                @elseif($f['icon'] === 'file-check')
                                                                                                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><path d="m9 15 2 2 4-4"/></svg>
                                                                                                                                                                @elseif($f['icon'] === 'edit')
                                                                                                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                                                                                                                                                @endif
                                                                                                                                                            </div>
                                                                                                                                                            <h3 class="text-xl font-black text-brand-dark mb-2 leading-tight">{{ $f['title'] }}</h3>
                                                                                                                                                            <p class="text-slate-500 text-xs font-semibold leading-relaxed">{{ $f['desc'] }}</p>
                                                                                                                                                        </div>
            @endforeach
        </div>
    </section>

    </div> <!-- End of content container -->
    </div>

    <!-- Trust / Security Section -->
    <section class="bg-foreground py-10 relative overflow-hidden">
        <!-- Background aesthetic -->
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-primary/20 rounded-full blur-[100px] -translate-y-1/2 translate-x-1/3"></div>
        
        <div class="max-w-7xl mx-auto px-4 relative z-10">
            <div class="grid lg:grid-cols-2 gap-16 items-center text-background">
                <div>
                    <span class="bg-white/10 text-white rounded-full px-5 py-1.5 text-xs font-semibold inline-flex items-center gap-2 mb-8 border border-white/5">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-primary"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        Enterprise Security
                    </span>
                    <h2 class="text-4xl md:text-5xl font-black mb-8 tracking-tight leading-none text-white">
                        Trusted by Safety Professionals Worldwide
                    </h2>
                    <p class="text-white/60 text-lg leading-relaxed mb-12 font-medium">
                        Our documents meet OSHA General Industry (29 CFR 1910), OSHA Construction (29 CFR 1926),
                        Cal/OSHA, and EM 385-1-1 (USACE) standards. SSL encrypted. WCAG 2.1 accessible.
                    </p>
                    <a href="{{ auth()->check() ? route('user-dashboard') : route('register') }}" wire:navigate class="group relative inline-flex items-center justify-center gap-3 bg-primary text-primary-foreground font-black px-10 py-5 rounded-2xl text-sm uppercase tracking-widest shadow-2xl shadow-primary/30 hover:shadow-primary/50 transition-all overflow-hidden">
                        <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-in-out"></div>
                        <span class="relative z-10 flex items-center gap-3">
                            Start Creating Now
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="group-hover:translate-x-1 transition-transform"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                        </span>
                    </a>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    @foreach([
                            ['icon' => 'shield-check', 'label' => 'OSHA Compliant', 'sub' => '29 CFR 1910 & 1926'],
                            ['icon' => 'file-check', 'label' => 'EM 385-1-1', 'sub' => 'USACE Safety Manual'],
                            ['icon' => 'lock', 'label' => 'SSL Encrypted', 'sub' => 'End-to-end security'],
                            ['icon' => 'star', 'label' => 'Cal/OSHA', 'sub' => 'California standards'],
                        ] as $item)
                                                                                                                                                            <div class="p-8 rounded-3xl bg-white/5 border border-white/5 transition-all">
                                                                                                                                                                <div class="w-10 h-10 rounded-xl bg-primary/20 flex items-center justify-center mb-6 text-primary transition-transform">
                                                                                                                                                                    @if($item['icon'] === 'shield-check')
                                                                                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
                                                                                                                                                                    @elseif($item['icon'] === 'file-check')
                                                                                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><path d="m9 15 2 2 4-4"/></svg>
                                                                                                                                                                    @elseif($item['icon'] === 'lock')
                                                                                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                                                                                                                                                    @elseif($item['icon'] === 'star')
                                                                                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                                                                                                                                                    @endif
                                                                                                                                                                </div>
                                                                                                                                                                <div class="font-black text-white text-base mb-1">{{ $item['label'] }}</div>
                                                                                                                                                                <div class="text-white/40 text-[10px] font-semibold uppercase tracking-wider">{{ $item['sub'] }}</div>
                                                                                                                                                            </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 py-10"> <!-- Re-opening content container for FAQ & Contact -->

    <!-- FAQ Section -->
    <section id="faq" class="mb-16 scroll-mt-24">
        <div class="text-center mb-10">
            <span class="bg-primary/10 text-primary border border-primary/20 rounded-full px-4 py-1 text-xs font-semibold mb-4 inline-block">Support</span>
            <h2 class="text-4xl md:text-5xl font-black mb-4 tracking-tight">Frequently Asked Questions</h2>
            <p class="text-muted-foreground max-w-xl mx-auto font-medium">Everything you need to know about our safety documents.</p>
        </div>

        <div class="max-w-3xl mx-auto space-y-4" x-data="{ active: null }">
            @foreach($this->faqs() as $i => $faq)
                <div class="card-surface overflow-hidden group hover:ring-primary/30 transition-all border border-border/50">
                    <button 
                        @click="active = (active === {{ $i }} ? null : {{ $i }})"
                        class="w-full text-left p-6 flex items-center justify-between gap-4 font-black group-hover:text-primary transition-colors"
                        :class="{ 'text-primary': active === {{ $i }} }"
                    >
                        <div class="flex items-center gap-4">
                            <span class="w-2 h-2 rounded-full bg-primary shrink-0 group-hover:animate-pulse"></span>
                            <span class="text-base md:text-lg tracking-tight">{{ $faq['question'] }}</span>
                        </div>
                        <svg 
                            xmlns="http://www.w3.org/2000/svg" 
                            width="20" 
                            height="20" 
                            viewBox="0 0 24 24" 
                            fill="none" 
                            stroke="currentColor" 
                            stroke-width="3" 
                            stroke-linecap="round" 
                            stroke-linejoin="round" 
                            class="shrink-0 transition-transform duration-300"
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
                        class="p-6 pt-0 border-t border-border/50 mt-2"
                        style="display: none;"
                    >
                        <p class="text-muted-foreground text-sm md:text-base leading-relaxed font-medium pt-4">
                            {{ $faq['answer'] }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="mb-8 scroll-mt-24">
        <div class="text-center mb-10">
            <span class="bg-primary/10 text-primary border border-primary/20 rounded-full px-4 py-1 text-xs font-semibold mb-4 inline-block">Get In Touch</span>
            <h2 class="text-4xl md:text-5xl font-black mb-4 tracking-tight">Ready to Automate Your Safety?</h2>
            <p class="text-muted-foreground max-w-xl mx-auto font-medium">Have questions or need a custom enterprise solution? We're here to help.</p>
        </div>

        <div class="card-surface-xl bg-card border border-border/50 overflow-hidden shadow-2xl max-w-7xl mx-auto">
            <div class="grid md:grid-cols-5">
                <div class="md:col-span-2 bg-foreground p-10 md:p-14 text-background space-y-12 relative overflow-hidden">
                    <!-- Decor -->
                    <div class="absolute top-0 right-0 w-40 h-40 bg-primary/20 rounded-full blur-[80px] -translate-y-1/2 translate-x-1/3"></div>
                    
                    <div class="relative z-10">
                        <h3 class="text-3xl font-black mb-4 tracking-tighter">Contact Details</h3>
                        <p class="text-background/60 text-sm font-medium">Reach out to us for any technical or enterprise-level queries.</p>
                    </div>

                    <div class="space-y-10 relative z-10">
                        <div class="flex items-center gap-6 group">
                            <div class="w-14 h-14 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-all duration-300 shadow-xl shadow-black/20">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            </div>
                            <div>
                                <h4 class="font-black text-white text-base leading-none mb-1">Expert Support</h4>
                                <p class="text-background/40 text-xs mt-1">Available 24/7 for you.</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-6 group">
                            <div class="w-14 h-14 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-all duration-300 shadow-xl shadow-black/20">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
                            </div>
                            <div>
                                <h4 class="font-black text-white text-base leading-none mb-1">Safe & Secure</h4>
                                <p class="text-background/40 text-xs mt-1">SSL Encryption guaranteed.</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 pt-6 relative z-10">
                   <div class="flex items-center gap-4 text-background/80">
    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
        <rect width="20" height="16" x="2" y="4" rx="2" />
        <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
    </svg>

    <a href="mailto:admin@QuickJHA.com" class="text-sm font-bold hover:underline">
        admin@QuickJHA.com
    </a>
</div>
                       <div class="flex items-center gap-4 text-background/80">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l2.27-2.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                        </svg>
                        
                        <a href="tel:+923007162279" class="text-sm font-bold hover:underline">
                            +92 300 716 2279
                        </a>
                    </div>

                        <div class="flex items-center gap-4 text-background/80">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            
                            <a href="https://www.google.com/maps?q=Interstate+35+Frontage+Rd,+Suite+400+Austin,+TX" target="_blank" class="text-sm font-bold hover:underline">
                                Plot no, M22V+7Q5, 3A Korang Road, Markaz I 10 Markaz I-10, Islamabad, 44000
                            </a>
                        </div>
                    </div>
                </div>

                <div class="md:col-span-3 p-10 md:p-14 bg-white relative">
                    <!-- Subtle background decoration -->
                    <div class="absolute top-0 right-0 w-64 h-64 bg-slate-50 rounded-bl-full -z-10"></div>
                    
                    {{-- Success Toast --}}
                    @if($messageSent)
                        <div
                            x-data="{ show: true }"
                            x-show="show"
                            x-init="setTimeout(() => { show = false; $wire.set('messageSent', false); }, 5000)"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-4"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-300"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-4"
                            class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 flex items-start gap-4 bg-white border border-green-100 rounded-2xl shadow-2xl shadow-green-500/10 p-5 w-full max-w-sm"
                            role="alert"
                            id="contact-toast"
                        >
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-green-500/10 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-slate-900">Message Sent!</p>
                                <p class="text-xs text-slate-500 mt-0.5">Thank you! We'll get back to you shortly.</p>
                            </div>
                            <button @click="show = false; $wire.set('messageSent', false)" class="flex-shrink-0 text-slate-400 hover:text-slate-600 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                            </button>
                        </div>
                    @endif

                    <form class="space-y-8 relative z-10" wire:submit.prevent="sendMessage">
                        <div class="grid sm:grid-cols-2 gap-8">
                            <!-- Name Field -->
                            <div class="space-y-3">
                                <label class="text-[11px] font-black uppercase tracking-[0.15em] text-slate-800 ml-1 flex items-center gap-2">
                                    Your Name
                                    <span class="text-primary">*</span>
                                </label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-primary/40 group-focus-within:text-primary transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    </div>
                                    <input wire:model="contactName" type="text" id="contact-name" placeholder="Enter Your Name" class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl py-4 pl-12 pr-5 text-sm font-bold text-slate-900 placeholder:text-slate-400 placeholder:font-medium focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all duration-300 @error('contactName') border-red-400 @enderror" />
                                </div>
                                @error('contactName')
                                    <p class="text-red-500 text-xs font-semibold ml-1 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email Field -->
                            <div class="space-y-3">
                                <label class="text-[11px] font-black uppercase tracking-[0.15em] text-slate-800 ml-1 flex items-center gap-2">
                                    Work Email
                                    <span class="text-primary">*</span>
                                </label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-primary/40 group-focus-within:text-primary transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                                    </div>
                                    <input wire:model="contactEmail" type="email" id="contact-email" placeholder="Enter your Email" class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl py-4 pl-12 pr-5 text-sm font-bold text-slate-900 placeholder:text-slate-400 placeholder:font-medium focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all duration-300 @error('contactEmail') border-red-400 @enderror" />
                                </div>
                                @error('contactEmail')
                                    <p class="text-red-500 text-xs font-semibold ml-1 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Subject Field -->
                        <div class="space-y-3">
                            <label class="text-[11px] font-black uppercase tracking-[0.15em] text-slate-800 ml-1">Subject</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-primary/40 group-focus-within:text-primary transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                                </div>
                                <input wire:model="contactSubject" type="text" id="contact-subject" placeholder="How can we help you?" class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl py-4 pl-12 pr-5 text-sm font-bold text-slate-900 placeholder:text-slate-400 placeholder:font-medium focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all duration-300" />
                            </div>
                        </div>

                        <!-- Message Field -->
                        <div class="space-y-3">
                            <label class="text-[11px] font-black uppercase tracking-[0.15em] text-slate-800 ml-1 flex items-center gap-2">
                                Message
                                <span class="text-primary">*</span>
                            </label>
                            <div class="relative group">
                                <div class="absolute top-5 left-5 flex items-start pointer-events-none text-primary/40 group-focus-within:text-primary transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                </div>
                                <textarea wire:model="contactMessage" rows="4" id="contact-message" placeholder="Tell us more about your specific needs or enterprise requirements..." class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl py-4 pl-12 pr-5 text-sm font-bold text-slate-900 placeholder:text-slate-400 placeholder:font-medium focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all duration-300 resize-none @error('contactMessage') border-red-400 @enderror"></textarea>
                            </div>
                            @error('contactMessage')
                                <p class="text-red-500 text-xs font-semibold ml-1 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <button type="submit" id="contact-submit" wire:loading.attr="disabled" class="group relative w-full rounded-2xl py-5 bg-primary text-white font-black text-sm uppercase tracking-[0.2em] shadow-xl shadow-primary/30 hover:shadow-primary/50 hover:shadow-2xl active:scale-[0.98] transition-all overflow-hidden flex items-center justify-center gap-3 disabled:opacity-70 disabled:cursor-not-allowed">
                                <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-in-out"></div>
                                <span class="relative z-10 flex items-center gap-3 text-white">
                                    <span wire:loading.remove wire:target="sendMessage">Send Message</span>
                                    <span wire:loading wire:target="sendMessage">Sending...</span>
                                    <svg wire:loading.remove wire:target="sendMessage" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="group-hover:translate-x-1 transition-transform"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
                                    <svg wire:loading wire:target="sendMessage" class="animate-spin" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    </div>
    <style>
        @keyframes spin-slow {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .animate-spin-slow {
            animation: spin-slow 15s linear infinite;
        }
    </style>
</div>
