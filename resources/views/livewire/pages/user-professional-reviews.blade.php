<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\ProfessionalReview;

new #[Layout('layouts.safety')] class extends Component {
    use WithPagination;

    public string $dateFilter = 'all';
    public ?string $search = null;
    public ?string $customStartDate = null;
    public ?string $customEndDate = null;

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

    public function reviews()
    {
        $query = ProfessionalReview::where('user_id', auth()->id())
            ->where('is_paid', true)
            ->with(['safetyDocument']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('message', 'like', '%' . $this->search . '%')
                    ->orWhereHas('safetyDocument', function ($q2) {
                        $q2->where('project_name', 'like', '%' . $this->search . '%');
                    });
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

        return $query->latest()->paginate(10);
    }
}; ?>

<div class="pt-4 pb-20 px-4 max-w-7xl mx-auto min-h-screen">
    @php $reviews = $this->reviews(); @endphp
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
        <div>
            <h1 class="text-4xl font-black tracking-tighter mb-1">Professionals Reviews</h1>
            <p class="text-muted-foreground text-sm font-medium">Track the status of your document reviews by our safety
                experts.</p>
        </div>
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
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search reviews..."
                class="w-full bg-transparent border-0 p-0 text-sm font-bold tracking-tight text-slate-900 placeholder:text-slate-400 focus:ring-0 outline-none md:hidden" />
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by message or project..."
                class="hidden md:block w-full bg-transparent border-0 p-0 text-sm font-bold tracking-tight text-slate-900 placeholder:text-slate-400 placeholder:font-medium focus:ring-0 outline-none" />
        </div>

        <div class="flex flex-col md:flex-row items-stretch md:items-center gap-4 md:gap-6 w-full lg:w-auto">
            <div class="flex items-center gap-3 flex-grow md:flex-grow-0 h-[46px]">
                <div class="relative group w-full md:min-w-[260px]">
                    <select wire:model.live="dateFilter"
                        class="w-full appearance-none bg-white border border-slate-200 rounded-xl px-4 py-2.5 pr-11 text-xs font-bold text-slate-700 shadow-sm transition-all duration-300 hover:border-primary/60 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-primary/10 focus:border-primary cursor-pointer h-[46px]">
                        <option value="all">All Dates</option>
                        <option value="today">Today</option>
                        <option value="yesterday">Yesterday</option>
                        <option value="last_3_days">Last 3 Days</option>
                        <option value="custom">Manual Range</option>
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
    <div class="grid md:hidden gap-6 mb-8">
        @forelse($reviews as $r)
            <div
                class="p-5 rounded-3xl bg-white border border-slate-200 shadow-sm hover:shadow-xl hover:border-primary/30 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <span
                        class="px-3 py-1 rounded-full bg-slate-900 text-white text-[9px] font-black uppercase tracking-widest">
                        {{ $r->safetyDocument->document_type }}
                    </span>
                    @if($r->progress == 1)
                        <span
                            class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 border border-slate-200 text-[9px] font-black uppercase tracking-widest">Pending</span>
                    @elseif($r->progress == 2)
                        <span
                            class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-600 border border-blue-100 text-[9px] font-black uppercase tracking-widest">In
                            Progress</span>
                    @elseif($r->progress == 3)
                        <span
                            class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100 text-[9px] font-black uppercase tracking-widest">Completed</span>
                    @endif
                </div>

                <div class="space-y-1 mb-6">
                    <h3 class="text-[17px] font-black text-slate-900 leading-[1.2] line-clamp-2">
                        {{ Str::limit($r->safetyDocument->project_name, 50) }}
                    </h3>
                    <p class="text-[11px] font-medium text-slate-500 italic line-clamp-3">
                        "{{ $r->message }}"
                    </p>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-slate-50">
                    <div class="flex items-center gap-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                            <line x1="16" y1="2" x2="16" y2="6" />
                            <line x1="8" y1="2" x2="8" y2="6" />
                            <line x1="3" y1="10" x2="21" y2="10" />
                        </svg>
                        {{ $r->created_at->format('M d, Y') }}
                    </div>
                    <a href="{{ route('preview.' . strtolower($r->safetyDocument->document_type), ['id' => $r->safety_document_id]) }}"
                        wire:navigate
                        class="text-[10px] font-black uppercase tracking-widest text-primary flex items-center gap-1.5 hover:translate-x-1 transition-all">
                        View Document
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m9 18 6-6-6-6" />
                        </svg>
                    </a>
                </div>
            </div>
        @empty
            <div class="py-16 text-center animate-in fade-in zoom-in duration-500">
                <div
                    class="w-20 h-20 bg-slate-100 rounded-[2rem] flex items-center justify-center mx-auto mb-6 text-slate-300">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.3-4.3" />
                    </svg>
                </div>
                <h3 class="text-xl font-black tracking-tighter uppercase italic text-slate-900 mb-1">No reviews found</h3>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Adjust your filters or try again
                    later</p>
            </div>
        @endforelse
    </div>

    <!-- table -->
    <div class="hidden md:block card-surface bg-card ring-1 ring-border shadow-soft overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-secondary/50 text-muted-foreground">
                        <th
                            class="px-8 py-5 text-[10px] font-black uppercase tracking-widest border-b border-border/50">
                            Document</th>
                        <th
                            class="px-8 py-5 text-[10px] font-black uppercase tracking-widest border-b border-border/50">
                            Message</th>
                        <th
                            class="px-8 py-5 text-[10px] font-black uppercase tracking-widest border-b border-border/50">
                            Status</th>
                        <th
                            class="px-8 py-5 text-[10px] font-black uppercase tracking-widest border-b border-border/50">
                            Date</th>
                        <th
                            class="px-8 py-5 text-[10px] font-black uppercase tracking-widest border-b border-border/50 text-right">
                            Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border/30">
                    @forelse($reviews as $r)
                        <tr class="hover:bg-secondary/20 transition-colors">
                            <td class="px-8 py-6">
                                <div class="flex flex-col">
                                    <span
                                        class="text-sm font-black tracking-tight text-slate-900">{{ Str::limit($r->safetyDocument->project_name, 40) }}</span>
                                    <span
                                        class="text-[9px] font-black uppercase tracking-widest text-primary">{{ $r->safetyDocument->document_type }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <p class="text-[11px] font-medium text-slate-500 italic max-w-xs line-clamp-2"
                                    title="{{ $r->message }}">
                                    "{{ Str::limit($r->message, 80) }}"
                                </p>
                            </td>
                            <td class="px-8 py-6">
                                @if($r->progress == 1)
                                    <span
                                        class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 border border-slate-200 text-[9px] font-black uppercase tracking-widest transition-all">Pending</span>
                                @elseif($r->progress == 2)
                                    <span
                                        class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-600 border border-blue-100 text-[9px] font-black uppercase tracking-widest animate-pulse">In
                                        Progress</span>
                                @elseif($r->progress == 3)
                                    <span
                                        class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-600 border-emerald-100 text-[9px] font-black uppercase tracking-widest">Completed</span>
                                @endif
                            </td>
                            <td class="px-8 py-6 text-xs text-slate-400 font-bold">
                                {{ $r->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-8 py-6 text-right">
                                <a href="{{ route('preview.' . strtolower($r->safetyDocument->document_type), ['id' => $r->safety_document_id]) }}"
                                    wire:navigate
                                    class="inline-flex items-center gap-2 bg-primary text-primary-foreground px-5 py-2.5 rounded-full text-[10px] font-black uppercase tracking-widest hover:brightness-110 hover:translate-x-1 transition-all shadow-lg active:scale-95">
                                    View Doc
                                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="m9 18 6-6-6-6" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-24 text-center">
                                <div class="flex flex-col items-center animate-in fade-in zoom-in duration-700">
                                    <div
                                        class="w-16 h-16 bg-slate-50 rounded-3xl flex items-center justify-center mb-6 text-slate-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <circle cx="11" cy="11" r="8" />
                                            <path d="m21 21-4.3-4.3" />
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-black tracking-tighter uppercase italic text-slate-900 mb-1">No
                                        reviews found</h3>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">When you
                                        request a professional review, it will appear here.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($reviews->hasPages())
        <div class="mt-8">
            {{ $reviews->links(view: 'vendor.pagination.premium') }}
        </div>
    @endif
</div>