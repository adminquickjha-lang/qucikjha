<?php

use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.safety', ['seoKey' => 'jsa'])] class extends Component {
    public function mount()
    {
        // Add any mount logic if necessary
    }

    public function howItWorks()
    {
        return [
            ['step' => '01', 'title' => 'Fill Project Details', 'desc' => 'Enter your project name, location, company info, and job details for the JSA.', 'icon' => 'file-check'],
            ['step' => '02', 'title' => 'Select Regulations', 'desc' => 'Choose applicable OSHA regulations and project safety requirements for the job.', 'icon' => 'shield-check'],
            ['step' => '03', 'title' => 'Document Creation', 'desc' => 'Our engine identifies job hazards, safe work practices, and control measures instantly.', 'icon' => 'zap'],
            ['step' => '04', 'title' => 'Download & Use', 'desc' => 'Preview, pay, and download your JSA in PDF or Word.', 'icon' => 'download'],
        ];
    }

    public function faqs()
    {
        return [
            [
                'question' => 'What is a Job Safety Analysis (JSA)?',
                'answer' => 'A Job Safety Analysis (JSA) is a safety planning document that breaks a job into steps, identifies the hazards in each step, and defines the safe work practices needed to complete the job safely.',
            ],
            [
                'question' => 'How is JSA different from JHA?',
                'answer' => 'JSA and JHA are closely related, but JSA is often used to emphasize safe work procedures and crew-level execution for a job. It is especially useful for toolbox talks, pre-task briefings, and standard site operations.',
            ],
            [
                'question' => 'Can I edit the created JSA?',
                'answer' => 'Absolutely. After download, you receive a PDF and a fully editable Word version so you can tailor safe work practices, controls, and notes for your jobsite conditions.',
            ],
        ];
    }

    public function getJsaPriceProperty()
    {
        return Setting::where('key', 'jsa_price')->value('value') ?? '19.00';
    }
}; ?>

<div class="overflow-x-hidden">
    <!-- 1. Hero Section -->
    <section class="relative min-h-[500px] flex items-center pt-4 pb-20 bg-slate-50 text-brand-dark">
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

        <div class="container mx-auto px-4 relative z-10 mb-10">
            <div class="flex flex-col items-center justify-center text-center max-w-6xl mx-auto">
                <div class="space-y-6 max-w-3xl mx-auto mt-4">
                    <span class="bg-primary/10 text-primary border border-primary/20 rounded-full px-5 py-1.5 text-xs font-black tracking-widest uppercase shadow-sm">
                        Job Safety Analysis Detail
                    </span>
                    <h1 class="text-3xl sm:text-4xl md:text-6xl lg:text-7xl font-extrabold tracking-tight mb-4 leading-[1.2] text-brand-dark max-w-5xl mx-auto text-center">
                        <span class="block">Create Professional</span>
                        <span class="text-primary block">Safety JSAs</span>
                    </h1>
                    
                    <p class="text-lg md:text-xl text-slate-600 max-w-2xl mx-auto leading-relaxed font-medium opacity-90 mb-8">
                        Instantly create practical Job Safety Analysis documents tailored to your work steps. Professional formatting, zero effort, and built for daily field execution.
                    </p>

                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a href="{{ route('generate.jsa') }}" wire:navigate class="group relative inline-flex items-center justify-center px-10 py-5 font-black text-sm uppercase tracking-widest text-white bg-primary rounded-2xl shadow-xl shadow-primary/20 transition-all overflow-hidden w-full sm:w-auto border-0">
                            <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-in-out"></div>
                            <span class="relative z-10 flex items-center gap-2">
                                Make Your JSA
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="group-hover:translate-x-1 transition-transform"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                            </span>
                        </a>
                        <a href="/examples/jsa.pdf" download class="group relative inline-flex items-center justify-center gap-3 bg-white border border-slate-200 text-brand-dark font-black px-8 py-5 rounded-2xl text-sm uppercase tracking-widest hover:bg-slate-50 transition-all w-full sm:w-auto shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                            Download Sample
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. What is JSA Section -->
    <section id="what-is-jsa" class="py-20 bg-slate-50 border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div class="space-y-6">
                    <span class="bg-primary/10 text-primary border border-primary/20 rounded-full px-4 py-1 text-xs font-semibold inline-block">The Foundation of Execution</span>
                    <h2 class="text-4xl md:text-5xl font-black tracking-tight text-slate-900">What is a Job Safety Analysis?</h2>
                    <p class="text-lg text-slate-600 leading-relaxed font-medium">
                        A Job Safety Analysis (JSA) is a practical technique used to identify hazards associated with a specific job and define the safest way to complete each step. It focuses on the job sequence, worker actions, tools, and immediate work conditions.
                    </p>
                    <p class="text-lg text-slate-600 leading-relaxed font-medium">
                        By breaking the job into simple steps, a JSA helps teams communicate safe work practices clearly and maintain consistency during daily site operations, toolbox talks, and pre-task planning.
                    </p>

                    <div class="pt-4 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 12 2 2 4-4"/><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        </div>
                        <span class="text-slate-900 font-bold">Recommended for daily site safety and toolbox planning</span>
                    </div>
                </div>
                <div class="relative">
                    <div class="bg-white p-8 rounded-3xl shadow-xl border border-slate-100">
                        <h3 class="text-xl font-bold mb-6 border-b pb-4">Key Components of our JSA</h3>
                        <ul class="space-y-4">
                            <li class="flex items-start gap-4">
                                <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold shrink-0 mt-1">1</div>
                                <div>
                                    <h4 class="font-bold text-slate-800">Job Step Breakdown</h4>
                                    <p class="text-sm text-slate-500">Divide the job into clear and easy-to-follow work steps.</p>
                                </div>
                            </li>
                            <li class="flex items-start gap-4">
                                <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold shrink-0 mt-1">2</div>
                                <div>
                                    <h4 class="font-bold text-slate-800">Hazard Identification</h4>
                                    <p class="text-sm text-slate-500">Identify the risks workers may face during each step of the job.</p>
                                </div>
                            </li>
                            <li class="flex items-start gap-4">
                                <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold shrink-0 mt-1">3</div>
                                <div>
                                    <h4 class="font-bold text-slate-800">Safe Work Practices</h4>
                                    <p class="text-sm text-slate-500">Define controls, PPE, and procedures for safer job execution.</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. Features Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-16">
                <span class="bg-primary/10 text-primary border border-primary/20 rounded-full px-4 py-1 text-xs font-semibold mb-4 inline-block">JSA Features</span>
                <h2 class="text-4xl md:text-5xl font-black tracking-tight mb-4">Precision Engineered for Safety</h2>
                <p class="text-muted-foreground max-w-xl mx-auto font-medium">Our platform creates JSAs equipped with robust features designed perfectly for rigorous safety environments.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                @foreach([
                    ['title' => 'Clear Job Step Flow', 'desc' => 'Straightforward work sequencing that crews can follow in the field.', 'icon' => 'list'],
                    ['title' => 'Hazard Awareness', 'desc' => 'Identify step-by-step hazards before the job begins.', 'icon' => 'bar-chart'],
                    ['title' => 'Targeted Control Measures', 'desc' => 'Assign practical controls that match the work being done.', 'icon' => 'shield'],
                    ['title' => 'Training and Briefing Support', 'desc' => 'Useful for toolbox talks and daily crew coordination.', 'icon' => 'book'],
                    ['title' => 'PPE Recommendations', 'desc' => 'Match required protective gear to each stage of the job.', 'icon' => 'hard-hat'],
                    ['title' => 'Signature Ready', 'desc' => 'Built-in formatting ready for review, sign-off, and field use.', 'icon' => 'pen']
                ] as $feature)
                    <div class="p-8 rounded-3xl bg-slate-50 border border-slate-100 hover:border-primary/50 hover:shadow-lg transition-all group">
                        <div class="w-14 h-14 bg-primary/10 rounded-2xl flex items-center justify-center text-primary mb-6 group-hover:scale-110 transition-transform">
                            @if($feature['icon'] === 'list')
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 6h13"/><path d="M8 12h13"/><path d="M8 18h13"/><path d="M3 6h.01"/><path d="M3 12h.01"/><path d="M3 18h.01"/></svg>
                            @elseif($feature['icon'] === 'bar-chart')
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M7 17v-3"/><path d="M12 17v-7"/><path d="M17 17V7"/></svg>
                            @elseif($feature['icon'] === 'shield')
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            @elseif($feature['icon'] === 'book')
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/></svg>
                            @elseif($feature['icon'] === 'hard-hat')
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12a10 10 0 1 1 20 0H2Z"/><path d="M7 12V8a5 5 0 0 1 10 0v4"/></svg>
                            @elseif($feature['icon'] === 'pen')
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                            @endif
                        </div>
                        <h3 class="font-bold text-lg mb-2 text-slate-800">{{ $feature['title'] }}</h3>
                        <p class="text-sm text-slate-500">{{ $feature['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 4. Content Section -->
    <section class="py-20 bg-slate-900 border-y border-slate-800 text-white relative overflow-hidden text-center">
        <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-primary/10 rounded-full blur-[150px]"></div>
        <div class="max-w-4xl mx-auto px-4 relative z-10">
            <h2 class="text-3xl md:text-4xl font-black mb-6 tracking-tight">The Easiest Way to Stay Compliant</h2>
            <p class="text-slate-400 text-lg mb-10 font-medium">
                Our engine does the heavy lifting, identifying hazards and proposing safe work practices so you can focus on the job. Daily safety planning is just a few clicks away.
            </p>
            <div class="flex justify-center gap-12">
                <div class="text-center">
                    <div class="text-4xl font-black text-primary mb-1">10k+</div>
                    <div class="text-xs font-bold uppercase tracking-widest text-slate-500">Documents</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-black text-primary mb-1">5k+</div>
                    <div class="text-xs font-bold uppercase tracking-widest text-slate-500">Safety Pros</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-black text-primary mb-1">30s</div>
                    <div class="text-xs font-bold uppercase tracking-widest text-slate-500">Creation</div>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. How It Works Section (Home Version) -->
    <section id="how-it-works" class="py-20 bg-slate-50 scroll-mt-24">
        <div class="text-center mb-10">
            <span class="bg-primary/10 text-primary border border-primary/20 rounded-full px-4 py-1 text-xs font-semibold mb-4 inline-block">Workflow</span>
            <h2 class="text-4xl md:text-5xl font-black mb-4 tracking-tight">How It Works</h2>
            <p class="text-muted-foreground max-w-xl mx-auto font-medium">From project details to a professional safety document in seconds.</p>
        </div>

        <div class="relative max-w-7xl mx-auto px-4">
            <!-- Connecting Line (Desktop Only) -->
            <div class="hidden md:block absolute top-[24px] left-[12.5%] right-[12.5%] h-0.5 bg-slate-200 z-0"></div>

            <div class="grid md:grid-cols-4 gap-12 relative z-10">
                @foreach($this->howItWorks() as $i => $item)
                    <div class="flex flex-col items-center text-center group">
                        <!-- Number Circle -->
                        <div class="w-12 h-12 rounded-full bg-[#1e293b] border-4 border-white text-white font-black flex items-center justify-center mb-6 relative z-20 shadow-xl group-hover:scale-110 group-hover:bg-primary transition-all">
                            {{ $item['step'] }}
                        </div>

                        <!-- Icon Container -->
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
                                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
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
                                <p class="text-[10px] text-muted-foreground font-semibold leading-relaxed tracking-wide uppercase">
                                    {{ $line }}{{ !$loop->last ? '.' : '' }}
                                </p>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>



    <!-- 7 & 9. Industries & Compliance -->
    <section class="py-20 bg-slate-900 text-white">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h2 class="text-3xl md:text-5xl font-black tracking-tight mb-6">Built for Every Industry. Compliant Everywhere.</h2>
            <p class="text-slate-400 max-w-2xl mx-auto mb-16 font-medium text-lg">From construction crews to maintenance teams, our JSAs support clear communication, practical controls, and daily jobsite safety execution.</p>

            <div class="flex flex-wrap justify-center gap-4 mb-12">
                <span class="bg-white/10 px-6 py-3 rounded-full text-sm font-bold border border-white/10">Construction</span>
                <span class="bg-white/10 px-6 py-3 rounded-full text-sm font-bold border border-white/10">Manufacturing</span>
                <span class="bg-white/10 px-6 py-3 rounded-full text-sm font-bold border border-white/10">Maintenance</span>
                <span class="bg-white/10 px-6 py-3 rounded-full text-sm font-bold border border-white/10">Electrical</span>
                <span class="bg-white/10 px-6 py-3 rounded-full text-sm font-bold border border-white/10">Utilities</span>
            </div>

            <div class="flex items-center justify-center gap-8 border-t border-white/10 pt-10">
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-500/20 rounded-2xl mx-auto mb-4 flex items-center justify-center text-blue-400">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <span class="font-black text-xl">OSHA 1910</span>
                    <p class="text-xs text-white/50 uppercase tracking-widest mt-1">General Industry</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-emerald-500/20 rounded-2xl mx-auto mb-4 flex items-center justify-center text-emerald-400">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/></svg>
                    </div>
                    <span class="font-black text-xl">OSHA 1926</span>
                    <p class="text-xs text-white/50 uppercase tracking-widest mt-1">Construction</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 10. Testimonials -->
    <section class="py-20 bg-slate-50 border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h2 class="text-4xl md:text-5xl font-black tracking-tight mb-16 text-slate-900">What Safety Pros Say</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 text-left">
                    <div class="flex text-yellow-400 mb-4">
                        @for($i=0; $i<5; $i++) <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg> @endfor
                    </div>
                    <p class="text-slate-600 font-semibold mb-6">"The JSA creator made our morning planning much easier. We had clear steps, hazards, and safe work practices ready in seconds."</p>
                    <div class="font-black text-slate-900 text-sm">Mark T.</div>
                    <div class="text-xs text-slate-500 uppercase tracking-wider">Safety Director</div>
                </div>
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 text-left">
                    <div class="flex text-yellow-400 mb-4">
                        @for($i=0; $i<5; $i++) <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg> @endfor
                    </div>
                    <p class="text-slate-600 font-semibold mb-6">"Our crew uses the editable Word file for toolbox talks, and the format is simple enough for everyone to understand quickly."</p>
                    <div class="font-black text-slate-900 text-sm">Sarah J.</div>
                    <div class="text-xs text-slate-500 uppercase tracking-wider">Site Manager</div>
                </div>
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 text-left">
                    <div class="flex text-yellow-400 mb-4">
                        @for($i=0; $i<5; $i++) <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg> @endfor
                    </div>
                    <p class="text-slate-600 font-semibold mb-6">"For routine and repeat work, having a fast, professional JSA helps us stay organized and communicate hazards much better."</p>
                    <div class="font-black text-slate-900 text-sm">Dave C.</div>
                    <div class="text-xs text-slate-500 uppercase tracking-wider">Operations Lead</div>
                </div>
            </div>
        </div>
    </section> -->

    <!-- 11. FAQ -->
    <section class="py-20 bg-white">
        <div class="max-w-3xl mx-auto px-4">
            <div class="text-center mb-12">
                <span class="bg-primary/10 text-primary border border-primary/20 rounded-full px-4 py-1 text-xs font-semibold mb-4 inline-block">Support</span>
                <h2 class="text-4xl font-black mb-4 tracking-tight">JSA FAQ</h2>
            </div>

            <div class="space-y-4" x-data="{ active: null }">
                @foreach($this->faqs() as $i => $faq)
                    <div class="bg-slate-50 rounded-2xl overflow-hidden border border-slate-200">
                        <button @click="active = (active === {{ $i }} ? null : {{ $i }})" class="w-full text-left p-6 font-black text-slate-800 flex justify-between items-center transition-colors hover:text-primary">
                            <span>{{ $faq['question'] }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" :class="{ 'rotate-180': active === {{ $i }} }" class="transition-transform"><path d="m6 9 6 6 6-6"/></svg>
                        </button>
                        <div x-show="active === {{ $i }}" style="display: none;" class="px-6 pb-6 text-slate-600 font-medium">
                            {{ $faq['answer'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

</div>
