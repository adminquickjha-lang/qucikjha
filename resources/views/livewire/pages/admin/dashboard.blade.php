<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\SafetyDocument;

new #[Layout('layouts.safety')] class extends Component {
    use WithPagination;

    public $fromDate;
    public $toDate;
    public $search;
    public $documentType = 'all';

    public function updatingFromDate()
    {
        $this->resetPage();
    }
    public function updatingToDate()
    {
        $this->resetPage();
    }
    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingDocumentType()
    {
        $this->resetPage();
    }

    public function projects()
    {
        return SafetyDocument::with('user')
            ->when($this->fromDate, fn($q) => $q->whereDate('created_at', '>=', $this->fromDate))
            ->when($this->toDate, fn($q) => $q->whereDate('created_at', '<=', $this->toDate))
            ->when($this->documentType !== 'all', fn($q) => $q->where('document_type', $this->documentType))
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('project_name', 'like', '%' . $this->search . '%')
                        ->orWhere('company_name', 'like', '%' . $this->search . '%')
                        ->orWhereHas('user', function ($u) {
                            $u->where('email', 'like', '%' . $this->search . '%')
                                ->orWhere('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->latest()
            ->paginate(10);
    }

    public function resetFilters()
    {
        $this->reset(['fromDate', 'toDate', 'search', 'documentType']);
        $this->resetPage();
    }

    public function stats()
    {
        $totalOrders = SafetyDocument::count();
        $paidOrdersCount = SafetyDocument::where('is_paid', true)->count();
        $totalRevenue = SafetyDocument::where('is_paid', true)->sum('amount');
        $downloads = SafetyDocument::where('download_ready', true)->count();
        $totalAiCost = SafetyDocument::sum('cost') + \App\Models\DocumentReview::sum('cost');

        return [
            ['label' => 'Total Orders', 'value' => $totalOrders, 'icon' => 'file-text', 'color' => 'text-primary'],
            ['label' => 'Revenue', 'value' => '$' . number_format($totalRevenue, 2), 'icon' => 'dollar-sign', 'color' => 'text-emerald-500'],
            ['label' => 'AI Spend', 'value' => '$' . number_format($totalAiCost, 2), 'icon' => 'zap', 'color' => 'text-purple-500'],
            ['label' => 'Docs Ready', 'value' => $downloads, 'icon' => 'download', 'color' => 'text-amber-500'],
        ];
    }
}; ?>

<div class="pt-4 pb-20 px-4 max-w-7xl mx-auto min-h-screen">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span
                    class="bg-primary/10 text-primary border border-primary/20 rounded-full px-3 py-0.5 text-[9px] font-black uppercase tracking-widest flex items-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                    </svg>
                    System Admin
                </span>
            </div>
            <h1 class="text-4xl font-black tracking-tighter mb-1">Admin Dashboard</h1>
            <p class="text-muted-foreground text-sm font-medium">Review and track all document creations.</p>
        </div>

        <!-- Admin Document Creation Dropdown -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open"
                class="bg-primary text-primary-foreground px-6 py-4 rounded-2xl font-bold text-xs uppercase tracking-widest hover:brightness-110 transition-all duration-300 flex items-center gap-3 shadow-xl shadow-primary/20 group">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10" />
                    <path d="M12 8v8" />
                    <path d="M8 12h8" />
                </svg>
                Create Document
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
                class="absolute right-0 mt-3 w-56 bg-card border border-border/50 rounded-2xl shadow-2xl z-50 overflow-hidden"
                style="display: none;">

                <div class="p-2 space-y-1">
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
                        <span class="text-[10px] font-black uppercase tracking-widest">Create JSA</span>
                    </a>

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
                        <span class="text-[10px] font-black uppercase tracking-widest">Create JHA</span>
                    </a>

                    <a href="{{ route('generate.aha') }}" wire:navigate
                        class="flex items-center gap-3 p-3 rounded-xl hover:bg-amber-500 hover:text-white transition-all group">
                        <div
                            class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center group-hover:bg-white/20 group-hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z" />
                            </svg>
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-widest">Create AHA</span>
                    </a>
                </div>
            </div>
        </div>

    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
        @foreach($this->stats() as $stat)
            <div class="card-surface p-6 flex items-center gap-5 hover:shadow-xl transition-all duration-300 group">
                <div
                    class="w-14 h-14 rounded-2xl bg-secondary flex items-center justify-center {{ $stat['color'] }} group-hover:scale-110 transition-transform border border-border/50">
                    @if($stat['icon'] === 'file-text')
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L15 2z" />
                            <polyline points="14 2 14 8 20 8" />
                            <line x1="9" x2="15" y1="13" y2="13" />
                            <line x1="9" x2="15" y1="17" y2="17" />
                            <line x1="9" x2="10" y1="9" y2="9" />
                        </svg>
                    @elseif($stat['icon'] === 'dollar-sign')
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" x2="12" y1="2" y2="22" />
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                        </svg>
                    @elseif($stat['icon'] === 'trending-up')
                            <path d="m22 7-8.5 15.5-5.5-5.5-6.5 6.5" />
                        </svg>
                    @elseif($stat['icon'] === 'zap')
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
                        </svg>
                    @elseif($stat['icon'] === 'download')
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                            <polyline points="7 10 12 15 17 10" />
                            <line x1="12" x2="12" y1="15" y2="3" />
                        </svg>
                    @endif
                </div>
                <div>
                    <p class="text-2xl font-black">{{ $stat['value'] }}</p>
                    <p class="text-[9px] text-muted-foreground font-black uppercase tracking-widest">{{ $stat['label'] }}
                    </p>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Premium Filter Header -->
    <div
        class="mb-8 flex flex-col lg:flex-row justify-between items-stretch lg:items-center bg-white p-3 rounded-[2rem] ring-1 ring-slate-200 shadow-[0_8px_30px_rgb(0,0,0,0.04)] gap-3 bg-gradient-to-br from-white to-slate-50/50">
        <div
            class="flex-grow flex items-center gap-4 pl-4 bg-slate-100/50 rounded-2xl px-4 py-2 border border-slate-200/50 group focus-within:ring-2 focus-within:ring-primary/20 transition-all">
            <div class="text-slate-400 group-focus-within:text-primary transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8" />
                    <path d="m21 21-4.3-4.3" />
                </svg>
            </div>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search orders..."
                class="w-full bg-transparent border-0 p-0 text-sm font-bold tracking-tight text-slate-900 placeholder:text-slate-400 focus:ring-0 outline-none md:hidden" />
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by project, company or user email..."
                class="hidden md:block w-full bg-transparent border-0 p-0 text-sm font-bold tracking-tight text-slate-900 placeholder:text-slate-400 placeholder:font-medium focus:ring-0 outline-none" />
        </div>

        <div class="flex flex-col md:flex-row items-stretch md:items-center gap-4 md:gap-6 w-full lg:w-auto">
            <div class="flex items-center gap-3 flex-grow md:flex-grow-0 h-[46px]">
                <div class="relative group w-full md:min-w-[140px]">
                    <select wire:model.live="documentType"
                        class="w-full appearance-none bg-white border border-slate-200 rounded-xl px-4 py-2.5 pr-11 text-xs font-bold text-slate-700 shadow-sm transition-all duration-300 hover:border-primary/60 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-primary/10 focus:border-primary cursor-pointer h-[46px]">
                        <option value="all">All Types</option>
                        <option value="JSA">JSA</option>
                        <option value="JHA">JHA</option>
                        <option value="AHA">AHA</option>
                    </select>
                    <div
                        class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-slate-400 group-hover:text-primary transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m6 9 6 6 6-6" />
                        </svg>
                    </div>
                </div>
            </div>

            <div
                class="flex items-center bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm focus-within:ring-4 focus-within:ring-primary/10 transition-all h-[46px] w-full md:w-auto">
                <div class="relative group/date flex-grow md:flex-grow-0">
                    <input type="date" wire:model.live="fromDate"
                        class="bg-transparent border-0 text-[10px] font-black uppercase px-4 py-3 text-slate-900 focus:ring-0 outline-none w-full md:w-36 lg:w-40 cursor-pointer" />
                    <span
                        class="absolute -top-1.5 left-4 px-1 bg-white text-[7px] font-black text-primary uppercase tracking-widest leading-none z-10 pointer-events-none">From</span>
                </div>
                <div class="w-px h-6 bg-slate-200 flex-shrink-0"></div>
                <div class="relative group/date flex-grow md:flex-grow-0">
                    <input type="date" wire:model.live="toDate"
                        class="bg-transparent border-0 text-[10px] font-black uppercase px-4 py-3 text-slate-900 focus:ring-0 outline-none w-full md:w-36 lg:w-40 cursor-pointer" />
                    <span
                        class="absolute -top-1.5 left-4 px-1 bg-white text-[7px] font-black text-primary uppercase tracking-widest leading-none z-10 pointer-events-none">To</span>
                </div>
            </div>

            @if($fromDate || $toDate || $search || $documentType !== 'all')
                <button wire:click="resetFilters"
                    class="h-[46px] px-6 flex items-center justify-center gap-2 bg-slate-100 text-slate-600 border border-slate-200 rounded-2xl hover:bg-destructive hover:text-white transition-all shadow-sm font-black text-[10px] uppercase tracking-widest"
                    title="Clear Filters">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8" />
                        <path d="M3 3v5h5" />
                    </svg>
                    Reset
                </button>
            @endif
        </div>
    </div>

    <div class="card-surface-xl bg-card border border-border/50 shadow-soft overflow-hidden">
        <div class="p-8 border-b border-border/50 bg-secondary/5">
            <div>
                <h3 class="text-xl font-black tracking-tight">Recent Orders</h3>
                <p class="text-xs text-muted-foreground font-medium mt-1">Review all created safety documents.</p>
            </div>
        </div>
        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-secondary/50 text-muted-foreground">
                        <th
                            class="px-8 py-5 text-[10px] font-black uppercase tracking-widest border-b border-border/50">
                            Project</th>
                        <th
                            class="px-8 py-5 text-[10px] font-black uppercase tracking-widest border-b border-border/50">
                            Type</th>
                        <th
                            class="px-8 py-5 text-[10px] font-black uppercase tracking-widest border-b border-border/50">
                            Company</th>
                        <th
                            class="px-8 py-5 text-[10px] font-black uppercase tracking-widest border-b border-border/50">
                            Date</th>
                        <th
                            class="px-8 py-5 text-[10px] font-black uppercase tracking-widest border-b border-border/50">
                            User</th>
                        <th
                            class="px-8 py-5 text-[10px] font-black uppercase tracking-widest border-b border-border/50">
                            Status</th>
                        <th
                            class="px-8 py-5 text-[10px] font-black uppercase tracking-widest border-b border-border/50 text-right">
                            AI Cost</th>
                        <th
                            class="px-8 py-5 text-[10px] font-black uppercase tracking-widest border-b border-border/50 text-right">
                            Doc Price</th>
                        <th
                            class="px-8 py-5 text-[10px] font-black uppercase tracking-widest border-b border-border/50 text-right">
                            Tokens</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border/30">
                    @php $projects = $this->projects(); @endphp
                    @if($projects->isEmpty())
                        <tr>
                            <td colspan="7" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-14 h-14 rounded-2xl bg-secondary flex items-center justify-center text-muted-foreground">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L15 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                                    </div>
                                    <p class="text-sm font-black uppercase tracking-widest text-muted-foreground">No orders found</p>
                                    <p class="text-xs text-muted-foreground font-medium">Try adjusting your date filters or check back later.</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                    @foreach($projects as $p)

                        <tr class="hover:bg-secondary/20 transition-colors">
                            <td class="px-8 py-6 font-bold text-sm tracking-tight">{{ Str::limit($p->project_name, 40) }}</td>
                            <td class="px-8 py-6">
                                <span
                                    class="px-2.5 py-0.5 rounded-full bg-primary/10 text-primary border border-primary/20 text-[9px] font-black uppercase tracking-widest">
                                    {{ $p->document_type }}
                                </span>
                            </td>
                            <td class="px-8 py-6 text-muted-foreground text-xs font-black uppercase tracking-widest italic">
                                {{ Str::limit($p->company_name, 30) }}
                            </td>
                            <td class="px-8 py-6 text-muted-foreground text-xs font-black uppercase tracking-widest italic whitespace-nowrap">
                                {{ $p->created_at?->format('d-m-Y') }}
                            </td>
                            <td class="px-8 py-6 text-xs font-medium">
                                <div class="flex items-center gap-2">
                                    <div
                                        class="w-6 h-6 rounded-full bg-primary/10 flex items-center justify-center text-[9px] font-black text-primary border border-primary/20">
                                        {{ strtoupper(substr($p->user?->name ?? 'U', 0, 1)) }}
                                    </div>
                                    {{ $p->user?->name ?? 'Unknown User' }}
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <span
                                    class="px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest border {{ $p->is_paid ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : 'bg-amber-100 text-amber-700 border-amber-200' }}">
                                    {{ $p->is_paid ? 'Paid' : 'Unpaid' }}
                                </span>
                            </td>
                            <td class="px-8 py-6 text-right font-bold text-sm text-purple-600">
                                ${{ number_format($p->total_ai_cost, 4) }}
                            </td>
                            <td class="px-8 py-6 text-right font-black text-sm">${{ $p->amount ?: '19.90' }}</td>
                            <td class="px-8 py-6 text-right">
                                <a href="{{ route('admin.usage', ['id' => $p->id]) }}" wire:navigate
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-purple-50 text-purple-600 border border-purple-200 rounded-lg text-[9px] font-black uppercase tracking-widest hover:bg-purple-600 hover:text-white transition-all whitespace-nowrap">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    {{ number_format($p->total_tokens) }} tokens
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="block md:hidden">
            @php $projects = $this->projects(); @endphp
            @if($projects->isEmpty())
                <div class="p-10 flex flex-col items-center gap-3 text-center">
                    <div class="w-14 h-14 rounded-2xl bg-secondary flex items-center justify-center text-muted-foreground">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L15 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                    </div>
                    <p class="text-sm font-black uppercase tracking-widest text-muted-foreground">No orders found</p>
                    <p class="text-xs text-muted-foreground font-medium">Try adjusting your date filters or check back later.</p>
                </div>
            @else
            <div class="p-4 space-y-4">
                @foreach($projects as $p)
                    <div
                        class="bg-card border border-border/40 rounded-[2rem] p-6 shadow-sm hover:shadow-md transition-all duration-300">
                        <div class="flex justify-between items-start gap-4 mb-5">
                            <div class="space-y-1.5 flex-grow">
                                <span
                                    class="inline-flex px-2.5 py-0.5 rounded-full bg-primary/10 text-primary border border-primary/20 text-[8px] font-black uppercase tracking-widest leading-none">
                                    {{ $p->document_type }}
                                </span>
                                <h4 class="font-black text-[13px] tracking-tight leading-snug text-slate-900">
                                    {{ Str::limit($p->project_name, 45) }}
                                </h4>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <span class="block font-black text-[14px] text-slate-900">${{ $p->amount ?: '19.90' }}</span>
                                <span class="text-[8px] font-black uppercase tracking-widest text-emerald-600">Doc Price</span>
                                <span class="block text-[8px] font-black uppercase tracking-widest text-muted-foreground mt-0.5">{{ $p->created_at?->format('d M Y') }}</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-1.5 mb-5 opacity-80">
                            <div class="flex items-center gap-2">
                                <span class="w-1 h-1 rounded-full bg-slate-400"></span>
                                <p class="text-[9px] font-bold tracking-tight uppercase text-slate-500 italic">
                                    {{ Str::limit($p->company_name, 35) }}</p>
                            </div>
                            <div class="flex items-center justify-between mt-2">
                                <a href="{{ route('admin.usage', ['id' => $p->id]) }}" wire:navigate class="flex items-center gap-1.5 px-2 py-1 bg-purple-50 text-purple-600 rounded-lg text-[8px] font-black uppercase tracking-widest border border-purple-100 hover:bg-purple-600 hover:text-white transition-all whitespace-nowrap">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    {{ number_format($p->total_tokens) }} tokens
                                </a>
                                <span class="text-[9px] font-black text-purple-600/60 italic">${{ number_format($p->total_ai_cost, 4) }}</span>
                            </div>
                        </div>

                        <div class="flex justify-between items-center pt-5 border-t border-border/30">
                            <div class="flex items-center gap-2.5">
                                <div
                                    class="w-7 h-7 rounded-full bg-primary/10 flex items-center justify-center text-[9px] font-black text-primary border border-primary/20">
                                    {{ strtoupper(substr($p->user?->name ?? 'U', 0, 1)) }}
                                </div>
                                <span
                                    class="text-[9px] font-black uppercase tracking-widest text-slate-500">{{ Str::limit($p->user?->name ?? 'Unknown', 15) }}</span>
                            </div>
                            <span
                                class="px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest border {{ $p->is_paid ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-amber-50 text-amber-600 border-amber-100' }}">
                                {{ $p->is_paid ? 'Paid' : 'Unpaid' }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
            @endif
        </div>
        @if($projects->hasPages())
            <div class="px-8 py-6 border-t border-border/50 bg-secondary/50">
                {{ $projects->links(view: 'vendor.pagination.premium') }}
            </div>
        @endif
    </div>
</div>