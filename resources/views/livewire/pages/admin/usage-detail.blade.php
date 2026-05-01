<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Models\SafetyDocument;

new #[Layout('layouts.safety')] class extends Component {
    public SafetyDocument $project;

    public function mount($id)
    {
        $this->project = SafetyDocument::with(['user', 'reviews'])->findOrFail($id);
    }
}; ?>

<div class="pt-8 pb-20 px-4 max-w-5xl mx-auto min-h-screen">
    <!-- Header -->
    <div class="mb-10">
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('admin.dashboard') }}" wire:navigate class="w-10 h-10 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center hover:bg-primary hover:text-white transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            </a>
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="bg-purple-100 text-purple-600 border border-purple-200 rounded-full px-2 py-0.5 text-[8px] font-black uppercase tracking-widest">Financial Audit</span>
                </div>
                <h1 class="text-3xl font-black tracking-tighter">AI Usage Analysis</h1>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                </div>
                <div>
                    <h2 class="font-black text-slate-900 leading-tight">{{ $project->project_name }}</h2>
                    <p class="text-xs text-slate-500 font-medium">Owned by: <span class="font-bold text-slate-700">{{ $project->user?->email }}</span></p>
                </div>
            </div>

            <div class="flex items-center gap-8">
                <div class="text-center">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">Total Tokens</span>
                    <span class="text-xl font-black text-slate-900">{{ number_format($project->total_tokens) }}</span>
                </div>
                <div class="w-px h-10 bg-slate-100"></div>
                <div class="text-center">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">Total AI Spend</span>
                    <span class="text-2xl font-black text-purple-600">${{ number_format($project->total_ai_cost, 4) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Entries -->
    <div class="space-y-8">

        {{-- Initial Generation --}}
        <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm hover:shadow-md transition-all">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h3 class="text-lg font-black text-slate-900 italic uppercase tracking-tight mb-1">Initial Document Generation</h3>
                    <p class="text-xs text-slate-400 font-bold tracking-widest uppercase">{{ $project->created_at->format('M d, Y @ H:i:s') }}</p>
                </div>
                <span class="px-3 py-1 bg-slate-900 text-white text-[9px] font-black uppercase tracking-widest rounded-lg">Initial</span>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 p-6 bg-slate-50 rounded-2xl border border-slate-100">
                <div>
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">Input Tokens</span>
                    <span class="text-sm font-bold text-slate-700">{{ number_format($project->input_tokens) }}</span>
                </div>
                <div>
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">Output Tokens</span>
                    <span class="text-sm font-bold text-slate-700">{{ number_format($project->output_tokens) }}</span>
                </div>
                <div>
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">Model Cost</span>
                    <span class="text-sm font-black text-purple-600">${{ number_format($project->cost, 4) }}</span>
                </div>
                <div>
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">Status</span>
                    <span class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">SUCCESS</span>
                </div>
            </div>
        </div>

        {{-- Reviews History --}}
        @php $totalReviews = $project->reviews()->count(); @endphp
        @foreach($project->reviews()->latest()->get() as $idx => $review)
            @php $levelNumber = $totalReviews - $idx; @endphp
            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm hover:shadow-md transition-all">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h3 class="text-lg font-black text-slate-900 italic uppercase tracking-tight mb-1">AI Automated Review #{{ $levelNumber }}</h3>
                        <p class="text-xs text-slate-400 font-bold tracking-widest uppercase">{{ $review->created_at->format('M d, Y @ H:i:s') }}</p>
                    </div>
                    <div class="flex flex-col items-end gap-1.5">
                        <span class="px-3 py-1 bg-blue-600 text-white text-[9px] font-black uppercase tracking-widest rounded-lg">Level {{ $levelNumber }}</span>
                        <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">AI Revision</span>
                    </div>
                </div>

                <div class="mb-6">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">User Request:</span>
                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 text-sm font-medium text-slate-700 italic">
                        "{{ $review->prompt }}"
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 p-6 bg-blue-50/30 rounded-2xl border border-blue-100/50">
                    <div>
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">Input Tokens</span>
                        <span class="text-sm font-bold text-slate-700">{{ number_format($review->input_tokens) }}</span>
                    </div>
                    <div>
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">Output Tokens</span>
                        <span class="text-sm font-bold text-slate-700">{{ number_format($review->output_tokens) }}</span>
                    </div>
                    <div>
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">Review Cost</span>
                        <span class="text-sm font-black text-blue-600">${{ number_format($review->cost, 4) }}</span>
                    </div>
                    <div>
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">Method</span>
                        <span class="text-[10px] font-black text-blue-500 uppercase tracking-widest">AI_REVISION</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
