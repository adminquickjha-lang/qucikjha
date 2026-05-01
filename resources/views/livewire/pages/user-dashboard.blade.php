<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('layouts.safety')] class extends Component {
    use WithPagination;
    public string $dateFilter = 'all';
    public ?string $search = null;
    public ?string $customStartDate = null;
    public ?string $customEndDate = null;
    public bool $hasAnyProjects = false;

    public function updatingDateFilter()
    {
        $this->resetPage();
    }
    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingCustomStartDate()
    {
        $this->resetPage();
    }
    public function updatingCustomEndDate()
    {
        $this->resetPage();
    }

    public function mount()
    {
        if (auth()->user()->role === 'admin') {
            return $this->redirect(route('admin.dashboard'), navigate: true);
        }

        $this->hasAnyProjects = \App\Models\SafetyDocument::where('user_id', auth()->id())->exists();
    }

    public function filteredQuery()
    {
        $query = \App\Models\SafetyDocument::where('user_id', auth()->id());

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('project_name', 'like', '%' . $this->search . '%')
                    ->orWhere('company_name', 'like', '%' . $this->search . '%')
                    ->orWhere('project_location', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->dateFilter === 'today') {
            $query->whereDate('created_at', now()->today());
        } elseif ($this->dateFilter === 'yesterday') {
            $query->whereDate('created_at', now()->yesterday());
        } elseif ($this->dateFilter === 'last_3_days') {
            $query->where('created_at', '>=', now()->subDays(3));
        } elseif ($this->dateFilter === 'custom') {
            if ($this->customStartDate) {
                $query->whereDate('created_at', '>=', $this->customStartDate);
            }
            if ($this->customEndDate) {
                $query->whereDate('created_at', '<=', $this->customEndDate);
            }
        }

        return $query;
    }

    public function projects()
    {
        return $this->filteredQuery()->latest()->paginate(10);
    }

    public function stats()
    {
        $query = $this->filteredQuery();

        return [
            ['label' => 'Documents Created', 'value' => (clone $query)->where('download_ready', true)->count(), 'icon' => 'clock', 'color' => 'text-amber-500'],
            ['label' => 'Documents Paid', 'value' => (clone $query)->where('is_paid', true)->count(), 'icon' => 'credit-card', 'color' => 'text-blue-500'],
        ];
    }
}; ?>

<div class="pt-4 pb-20 px-4 max-w-7xl mx-auto min-h-screen">
    <div>
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-12 gap-6">
            <div>
                <h1 class="text-4xl font-black tracking-tighter mb-2">Dashboard</h1>
                <p class="text-muted-foreground text-sm font-medium">
                    Welcome back, <span class="text-foreground font-bold">{{ auth()->user()->name }}</span>. Manage your
                    professional safety documents.
                </p>
            </div>
            <!-- Document Creation Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open"
                    class="bg-primary text-primary-foreground px-6 py-4 rounded-2xl font-bold text-xs uppercase tracking-widest hover:brightness-110 transition-all duration-300 flex items-center gap-3 shadow-xl shadow-primary/20 group">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <path d="M12 8v8" />
                        <path d="M8 12h8" />
                    </svg>
                    New Document
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"
                        class="transition-transform duration-300" :class="open ? 'rotate-180' : ''">
                        <polyline points="6 9 12 15 18 9" />
                    </svg>
                </button>

                <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="absolute right-0 mt-3 w-56 bg-white border border-slate-200 rounded-2xl shadow-2xl z-50 overflow-hidden"
                    style="display: none;">

                    <div class="p-2 space-y-1">
                        <a href="{{ route('generate.jha') }}" wire:navigate
                            class="flex items-center gap-3 p-3 rounded-xl hover:bg-emerald-500 hover:text-white transition-all group">
                            <div
                                class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center group-hover:bg-white/20 group-hover:text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                </svg>
                            </div>
                            <span
                                class="text-[10px] font-black uppercase tracking-widest text-slate-900 group-hover:text-white">Create
                                JHA</span>
                        </a>

                        <a href="{{ route('generate.aha') }}" wire:navigate
                            class="flex items-center gap-3 p-3 rounded-xl hover:bg-amber-500 hover:text-white transition-all group">
                            <div
                                class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center group-hover:bg-white/20 group-hover:text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path
                                        d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z" />
                                </svg>
                            </div>
                            <span
                                class="text-[10px] font-black uppercase tracking-widest text-slate-900 group-hover:text-white">Create
                                AHA</span>
                        </a>

                        <a href="{{ route('generate.jsa') }}" wire:navigate
                            class="flex items-center gap-3 p-3 rounded-xl hover:bg-primary hover:text-white transition-all group">
                            <div
                                class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center group-hover:bg-white/20 group-hover:text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                    <polyline points="14 2 14 8 20 8" />
                                </svg>
                            </div>
                            <span
                                class="text-[10px] font-black uppercase tracking-widest text-slate-900 group-hover:text-white">Create
                                JSA</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
            @foreach($this->stats() as $stat)
                <div class="card-surface p-6 flex items-center gap-5 hover:shadow-xl transition-all duration-300 group">
                    <div
                        class="w-14 h-14 rounded-2xl bg-secondary flex items-center justify-center {{ $stat['color'] }} group-hover:scale-110 transition-transform">
                        @if($stat['icon'] === 'file-text')
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L15 2z" />
                                <polyline points="14 2 14 8 20 8" />
                                <line x1="9" x2="15" y1="13" y2="13" />
                                <line x1="9" x2="15" y1="17" y2="17" />
                                <line x1="9" x2="10" y1="9" y2="9" />
                            </svg>
                        @elseif($stat['icon'] === 'clock')
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10" />
                                <polyline points="12 6 12 12 16 14" />
                            </svg>
                        @elseif($stat['icon'] === 'credit-card')
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="20" height="14" x="2" y="5" rx="2" />
                                <line x1="2" x2="22" y1="10" y2="10" />
                            </svg>
                        @elseif($stat['icon'] === 'download')
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <polyline points="7 10 12 15 17 10" />
                                <line x1="12" x2="12" y1="15" y2="3" />
                            </svg>
                        @endif
                    </div>
                    <div>
                        <p class="text-3xl font-black">{{ $stat['value'] }}</p>
                        <p class="text-[10px] text-muted-foreground font-semibold uppercase tracking-wider">
                            {{ $stat['label'] }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>

        @php $projects = $this->projects(); @endphp

        <!-- Documents List -->
        @if(!$this->hasAnyProjects)
            <div class="card-surface text-center py-24 border-dashed">
                <div class="w-24 h-24 rounded-[2rem] bg-secondary mx-auto flex items-center justify-center mb-8">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="text-muted-foreground/30">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-black mb-3 tracking-tight">No documents yet</h3>
                <p class="text-muted-foreground mb-10 max-w-sm mx-auto font-medium">
                    Create your first JHA, AHA, or JSA safety document. It only takes a few seconds.
                </p>
                <div class="relative inline-block text-left" x-data="{ open: false }">
                    <button @click="open = !open"
                        class="inline-flex items-center gap-3 bg-foreground text-background font-black px-8 py-4 rounded-2xl text-sm uppercase tracking-widest hover:bg-primary hover:text-white transition-all shadow-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14" />
                            <path d="M12 5v14" />
                        </svg>
                        Create First Document
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"
                            class="transition-transform duration-300 ml-1" :class="open ? 'rotate-180' : ''">
                            <polyline points="6 9 12 15 18 9" />
                        </svg>
                    </button>

                    <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform scale-95"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 transform scale-100"
                        x-transition:leave-end="opacity-0 transform scale-95"
                        class="absolute left-1/2 -translate-x-1/2 mt-3 w-56 bg-white border border-slate-200 rounded-2xl shadow-2xl z-50 overflow-hidden text-left"
                        style="display: none;">

                        <div class="p-2 space-y-1">
                            <a href="{{ route('generate.jha') }}" wire:navigate
                                class="flex items-center gap-3 p-3 rounded-xl hover:bg-emerald-500 hover:text-white transition-all group">
                                <div
                                    class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center group-hover:bg-white/20 group-hover:text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                    </svg>
                                </div>
                                <span
                                    class="text-[10px] font-black uppercase tracking-widest text-slate-900 group-hover:text-white">Create
                                    JHA</span>
                            </a>

                            <a href="{{ route('generate.aha') }}" wire:navigate
                                class="flex items-center gap-3 p-3 rounded-xl hover:bg-amber-500 hover:text-white transition-all group">
                                <div
                                    class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center group-hover:bg-white/20 group-hover:text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path
                                            d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z" />
                                    </svg>
                                </div>
                                <span
                                    class="text-[10px] font-black uppercase tracking-widest text-slate-900 group-hover:text-white">Create
                                    AHA</span>
                            </a>

                            <a href="{{ route('generate.jsa') }}" wire:navigate
                                class="flex items-center gap-3 p-3 rounded-xl hover:bg-primary hover:text-white transition-all group">
                                <div
                                    class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center group-hover:bg-white/20 group-hover:text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                        <polyline points="14 2 14 8 20 8" />
                                    </svg>
                                </div>
                                <span
                                    class="text-[10px] font-black uppercase tracking-widest text-slate-900 group-hover:text-white">Create
                                    JSA</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div id="document-history" class="space-y-8">
                <!-- Premium Filter Header -->
                <div
                    class="flex flex-col lg:flex-row justify-between items-stretch lg:items-center bg-white p-3 rounded-[2rem] ring-1 ring-slate-200 shadow-[0_8px_30px_rgb(0,0,0,0.04)] gap-3 bg-gradient-to-br from-white to-slate-50/50">
                    <div
                        class="flex-grow flex items-center gap-4 pl-4 bg-slate-100/50 rounded-2xl px-4 py-2 border border-slate-200/50 group focus-within:ring-2 focus-within:ring-primary/20 transition-all">
                        <div class="text-slate-400 group-focus-within:text-primary transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8" />
                                <path d="m21 21-4.3-4.3" />
                            </svg>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search documents..."
                            class="w-full bg-transparent border-0 p-0 text-sm font-bold tracking-tight text-slate-900 placeholder:text-slate-400 focus:ring-0 outline-none md:hidden" />
                        <input type="text" wire:model.live.debounce.300ms="search"
                            placeholder="Search by project name, company or location..."
                            class="hidden md:block w-full bg-transparent border-0 p-0 text-sm font-bold tracking-tight text-slate-900 placeholder:text-slate-400 placeholder:font-medium focus:ring-0 outline-none" />
                    </div>

                    <div class="flex flex-col md:flex-row items-stretch md:items-center gap-4 md:gap-6 w-full lg:w-auto">
                        <div class="flex items-center gap-3 flex-grow md:flex-grow-0 h-[46px]">
                            <div class="relative group w-full md:min-w-[260px]">
                                <select wire:model.live="dateFilter"
                                    class="w-full appearance-none bg-white border border-slate-200 rounded-xl px-4 py-2.5 pr-11 text-xs font-bold text-slate-700 shadow-sm transition-all duration-300 hover:border-primary/60 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-primary/10 focus:border-primary cursor-pointer h-[46px]">
                                    <option value="all">All Documents</option>
                                    <option value="today">Today</option>
                                    <option value="yesterday">Yesterday</option>
                                    <option value="last_3_days">Last 3 Days</option>
                                    <option value="custom">Manual Range</option>
                                </select>

                                <!-- Icon -->
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-slate-400 group-hover:text-primary transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="m6 9 6 6 6-6" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        @if($dateFilter === 'custom')
                            <div
                                class="flex items-center bg-white border border-slate-200 rounded-2xl overflow-hidden animate-in slide-in-from-top-4 md:slide-in-from-left-4 fade-in duration-500 shadow-sm focus-within:ring-4 focus-within:ring-primary/10 transition-all h-[46px] w-full md:w-auto">
                                <div class="relative group/date flex-grow md:flex-grow-0">
                                    <input type="date" wire:model.live="customStartDate"
                                        class="bg-transparent border-0 text-[10px] font-black uppercase px-4 py-3 text-slate-900 focus:ring-0 outline-none w-full md:w-36 lg:w-40 cursor-pointer" />
                                    <span
                                        class="absolute -top-1.5 left-4 px-1 bg-white text-[7px] font-black text-primary uppercase tracking-widest leading-none z-10 pointer-events-none">Start</span>
                                </div>
                                <div class="w-px h-6 bg-slate-200 flex-shrink-0"></div>
                                <div class="relative group/date flex-grow md:flex-grow-0">
                                    <input type="date" wire:model.live="customEndDate"
                                        class="bg-transparent border-0 text-[10px] font-black uppercase px-4 py-3 text-slate-900 focus:ring-0 outline-none w-full md:w-36 lg:w-40 cursor-pointer" />
                                    <span
                                        class="absolute -top-1.5 left-4 px-1 bg-white text-[7px] font-black text-primary uppercase tracking-widest leading-none z-10 pointer-events-none">End</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Card Grid View (Mobile Only) -->
                <div class="grid md:hidden gap-6">
                    @forelse($projects as $p)
                        @php
                            $status = $p['status'] ?? 'Generated';
                        @endphp
                        <a href="{{ route('preview.' . strtolower($p->document_type), ['id' => $p->id]) }}" wire:navigate
                            class="group block">
                            <div
                                class="p-5 rounded-3xl bg-white border border-slate-200 shadow-sm hover:shadow-xl hover:border-primary/30 transition-all duration-300">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="px-3 py-1 rounded-full bg-slate-900 text-white text-[9px] font-black uppercase tracking-widest">
                                            {{ $p->document_type }}
                                        </span>
                                        <span
                                            class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest {{ $p->is_paid ? 'bg-blue-50 text-blue-600' : 'bg-rose-50 text-rose-600' }}">
                                            {{ $p->is_paid ? 'Paid' : 'Unpaid' }}
                                        </span>
                                    </div>
                                    <div class="text-slate-300 group-hover:text-primary transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path d="M5 12h14" />
                                            <path d="m12 5 7 7-7 7" />
                                        </svg>
                                    </div>
                                </div>

                                <div class="space-y-1 mb-6">
                                    <h3
                                        class="text-[17px] font-black text-slate-900 leading-[1.2] group-hover:text-primary transition-colors line-clamp-2">
                                        {{ Str::limit($p->project_name, 50) }}
                                    </h3>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tight">
                                        {{ Str::limit($p->company_name, 50) }}
                                    </p>
                                </div>

                                <div class="flex items-center justify-between pt-4 border-t border-slate-50">
                                    <div
                                        class="flex items-center gap-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                                        <span class="flex items-center gap-1.5 whitespace-nowrap">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                                                fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                                                <line x1="16" y1="2" x2="16" y2="6" />
                                                <line x1="8" y1="2" x2="8" y2="6" />
                                                <line x1="3" y1="10" x2="21" y2="10" />
                                            </svg>
                                            {{ $p->created_at->format('M d, Y') }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-slate-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                                            <circle cx="12" cy="10" r="3" />
                                        </svg>
                                        <span
                                            class="text-[10px] font-bold uppercase tracking-tight">{{ Str::limit($p->project_location, 30) }}</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="col-span-full py-16 text-center animate-in fade-in zoom-in duration-500">
                            <div
                                class="w-20 h-20 bg-slate-100 rounded-[2rem] flex items-center justify-center mx-auto mb-6 text-slate-300">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8" />
                                    <path d="m21 21-4.3-4.3" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-black tracking-tighter uppercase italic text-slate-900 mb-1">No matching
                                reports</h3>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Try adjusting your search
                                or filters</p>
                        </div>
                    @endforelse
                </div>

                <!-- Table View (Desktop Only) -->
                <div class="hidden md:block card-surface bg-card ring-1 ring-border shadow-soft overflow-hidden">
                    <div class="px-8 py-6 border-b border-border/50 bg-secondary/30">
                        <h3 class="flex items-center gap-3 text-lg font-black tracking-tight italic uppercase">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"
                                class="text-primary">
                                <path d="M3 3v18h18" />
                                <path d="m19 9-5 5-4-4-3 3" />
                            </svg>
                            Documents History
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-secondary/50 text-muted-foreground">
                                    <th
                                        class="px-8 py-5 text-[10px] font-semibold uppercase tracking-wider border-b border-border/50">
                                        Document</th>
                                    <th
                                        class="px-8 py-5 text-[10px] font-semibold uppercase tracking-wider border-b border-border/50">
                                        Type</th>
                                    <th
                                        class="px-8 py-5 text-[10px] font-semibold uppercase tracking-wider border-b border-border/50">
                                        Company</th>
                                    <th
                                        class="px-8 py-5 text-[10px] font-semibold uppercase tracking-wider border-b border-border/50">
                                        Date</th>
                                    <th
                                        class="px-8 py-5 text-[10px] font-semibold uppercase tracking-wider border-b border-border/50">
                                        Status</th>
                                    <th
                                        class="px-8 py-5 text-[10px] font-semibold uppercase tracking-wider border-b border-border/50">
                                        Payment</th>
                                    <th
                                        class="px-8 py-5 text-[10px] font-semibold uppercase tracking-wider border-b border-border/50 text-right">
                                        Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border/30">
                                @forelse($projects as $p)
                                    @php
                                        $status = $p['status'] ?? 'Generated';
                                    @endphp
                                    <tr class="group/row">
                                        <td class="px-8 py-6">
                                            <div class="font-black text-sm tracking-tight text-slate-900">
                                                {{ Str::limit($p->project_name, 50) }}
                                            </div>
                                        </td>
                                        <td class="px-8 py-6">
                                            <span
                                                class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 border border-slate-200 text-[9px] font-black uppercase tracking-widest">
                                                {{ $p->document_type }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-6 text-slate-500 text-xs font-bold uppercase tracking-tight">
                                            {{ Str::limit($p->company_name, 40) }}
                                        </td>
                                        <td class="px-8 py-6 text-slate-400 text-xs font-bold whitespace-nowrap">
                                            {{ $p->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-8 py-6">
                                            <span
                                                class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest border transition-all {{ $p->download_ready ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-amber-50 text-amber-600 border-amber-100' }}">
                                                {{ $p->download_ready ? 'READY' : 'PENDING' }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-6">
                                            <span
                                                class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest border transition-all {{ $p->is_paid ? 'bg-blue-50 text-blue-600 border-blue-100' : 'bg-rose-50 text-rose-600 border-rose-100' }}">
                                                {{ $p->is_paid ? 'PAID' : 'UNPAID' }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-6 text-right">
                                            <a href="{{ route('preview.' . strtolower($p->document_type), ['id' => $p->id]) }}"
                                                wire:navigate
                                                class="inline-flex items-center gap-2 bg-primary text-primary-foreground px-5 py-2.5 rounded-full text-[10px] font-black uppercase tracking-widest hover:brightness-110 hover:translate-x-1 transition-all shadow-lg shadow-primary/20 active:scale-95">
                                                Open
                                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="m9 18 6-6-6-6" />
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-8 py-24 text-center">
                                            <div class="flex flex-col items-center animate-in fade-in zoom-in duration-700">
                                                <div
                                                    class="w-16 h-16 bg-slate-50 rounded-3xl flex items-center justify-center mb-6 text-slate-300">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round">
                                                        <circle cx="11" cy="11" r="8" />
                                                        <path d="m21 21-4.3-4.3" />
                                                    </svg>
                                                </div>
                                                <h3
                                                    class="text-xl font-black tracking-tighter uppercase italic text-slate-900 mb-1">
                                                    No matching reports found</h3>
                                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">
                                                    Adjust your filters to explore your document history</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-8">
                    {{ $projects->links(view: 'vendor.pagination.premium', data: ['scrollTo' => '#document-history']) }}
                </div>
            </div>
        @endif
    </div>

</div>