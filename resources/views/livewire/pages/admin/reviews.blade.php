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
        $query = ProfessionalReview::with(['user', 'safetyDocument'])
            ->where('is_paid', true);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('message', 'like', '%' . $this->search . '%')
                    ->orWhereHas('safetyDocument', function ($q2) {
                        $q2->where('project_name', 'like', '%' . $this->search . '%')
                            ->orWhere('company_name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('user', function ($q2) {
                        $q2->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('email', 'like', '%' . $this->search . '%');
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

    public function markAsCompleted($id)
    {
        $review = ProfessionalReview::findOrFail($id);
        $review->update(['progress' => 3]);

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Review Completed',
            'text' => 'The professional review has been marked as completed.'
        ]);
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
            <h1 class="text-4xl font-black tracking-tighter mb-1">Professionals Reviews</h1>
            <p class="text-muted-foreground text-sm font-medium">Manage and track all requested professionals reviews.
            </p>
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
            <input type="text" wire:model.live.debounce.300ms="search"
                placeholder="Search by message, project, company or user..."
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

    <!-- table -->
    <div class="card-surface-xl bg-card border border-border/50 shadow-soft overflow-hidden">
        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-secondary/50 text-muted-foreground">
                        <th
                            class="px-8 py-5 text-[10px] font-black uppercase tracking-widest border-b border-border/50">
                            User</th>
                        <th
                            class="px-8 py-5 text-[10px] font-black uppercase tracking-widest border-b border-border/50">
                            Document</th>
                        <th
                            class="px-8 py-5 text-[10px] font-black uppercase tracking-widest border-b border-border/50">
                            Message</th>
                        <th
                            class="px-8 py-5 text-[10px] font-black uppercase tracking-widest border-b border-border/50">
                            Payment</th>
                        <th
                            class="px-8 py-5 text-[10px] font-black uppercase tracking-widest border-b border-border/50">
                            Progress</th>
                        <th
                            class="px-8 py-5 text-[10px] font-black uppercase tracking-widest border-b border-border/50">
                            Date</th>
                        <th
                            class="px-8 py-5 text-[10px] font-black uppercase tracking-widest border-b border-border/50 text-right">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border/30">
                    @php $reviews = $this->reviews(); @endphp
                    @foreach($reviews as $r)
                        <tr class="hover:bg-secondary/20 transition-colors">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-[10px] font-black text-primary border border-primary/20">
                                        {{ strtoupper(substr($r->user->name ?? $r->user->email, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold tracking-tight">{{ $r->user->name }}</p>
                                        <p class="text-[10px] text-muted-foreground font-medium">{{ $r->user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6 font-bold text-sm tracking-tight">
                                <div class="flex flex-col">
                                    <span
                                        class="text-xs font-bold tracking-tight">{{ Str::limit($r->safetyDocument->project_name, 40) }}</span>
                                    <span
                                        class="text-[9px] font-black uppercase tracking-widest text-primary">{{ $r->safetyDocument->document_type }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex flex-col max-w-xs">
                                    <p class="text-[10px] font-medium leading-relaxed text-slate-500 line-clamp-2 italic"
                                        title="{{ $r->message }}">
                                        "{{ Str::limit($r->message, 100) }}"
                                    </p>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <span
                                    class="px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest border {{ $r->is_paid ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : 'bg-amber-100 text-amber-700 border-amber-200' }}">
                                    {{ $r->is_paid ? 'Paid' : 'Unpaid' }}
                                </span>
                            </td>
                            <td class="px-8 py-6">
                                @if($r->progress == 1)
                                    <span
                                        class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 border border-slate-200 text-[9px] font-black uppercase tracking-widest">Pending</span>
                                @elseif($r->progress == 2)
                                    <span
                                        class="px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-700 border border-blue-200 text-[9px] font-black uppercase tracking-widest">In
                                        Progress</span>
                                @elseif($r->progress == 3)
                                    <span
                                        class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-700 border border-emerald-200 text-[9px] font-black uppercase tracking-widest">Completed</span>
                                @endif
                            </td>
                            <td class="px-8 py-6 text-xs text-muted-foreground font-medium">
                                {{ $r->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('review.secure', ['token' => $r->token]) }}" wire:navigate
                                        class="p-2 bg-primary/10 text-primary hover:bg-primary hover:text-white rounded-lg transition-all"
                                        title="Login and Review">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                                            <polyline points="10 17 15 12 10 7" />
                                            <line x1="15" x2="3" y1="12" y2="12" />
                                        </svg>
                                    </a>

                                    @if($r->progress < 3)
                                        <button wire:click="markAsCompleted({{ $r->id }})"
                                            wire:confirm="Are you sure you want to mark this review as completed?"
                                            class="p-2 bg-emerald-100 text-emerald-600 hover:bg-emerald-500 hover:text-white rounded-lg transition-all shadow-sm"
                                            title="Mark as Completed">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                                fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <polyline points="20 6 9 17 4 12" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="block md:hidden">
            @php $reviews = $this->reviews(); @endphp
            <div class="p-4 space-y-4 text-left">
                @foreach($reviews as $r)
                    <div
                        class="bg-card border border-border/40 rounded-[2rem] p-6 shadow-sm hover:shadow-md transition-all duration-300">
                        <div class="flex justify-between items-start gap-4 mb-6">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-2xl bg-primary/10 flex items-center justify-center text-[11px] font-black text-primary border border-primary/20">
                                    {{ strtoupper(substr($r->user->name ?? $r->user->email, 0, 1)) }}
                                </div>
                                <div class="space-y-0.5">
                                    <p class="text-[13px] font-black tracking-tight text-slate-900 leading-tight">
                                        {{ Str::limit($r->user->name, 20) }}</p>
                                    <p class="text-[8px] text-muted-foreground font-black uppercase tracking-widest">
                                        {{ Str::limit($r->user->email, 25) }}</p>
                                </div>
                            </div>
                            <span
                                class="px-2.5 py-1 rounded-full text-[8px] font-black uppercase tracking-widest border shadow-sm {{ $r->is_paid ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-amber-50 text-amber-600 border-amber-100' }}">
                                {{ $r->is_paid ? 'Paid' : 'Unpaid' }}
                            </span>
                        </div>

                        <div class="bg-secondary/30 rounded-2xl p-4 mb-6 border border-border/30">
                            <div class="flex items-center gap-2 mb-2">
                                <span
                                    class="px-2 py-0.5 rounded bg-primary/20 text-primary text-[7px] font-black uppercase tracking-wider">{{ $r->safetyDocument->document_type }}</span>
                                <h5 class="text-[11px] font-black tracking-tight text-slate-800">
                                    {{ Str::limit($r->safetyDocument->project_name, 35) }}</h5>
                            </div>
                            <p class="text-[11px] font-medium leading-[1.6] text-slate-500 italic">
                                "{{ Str::limit($r->message, 80) }}"
                            </p>
                        </div>

                        <div
                            class="flex justify-between items-center bg-slate-50/50 -mx-6 -mb-6 p-4 px-6 rounded-b-[2rem] border-t border-border/30">
                            <div class="flex flex-col gap-0.5">
                                <p class="text-[7px] font-black uppercase tracking-widest text-muted-foreground">Status</p>
                                @if($r->progress == 1)
                                    <span
                                        class="flex items-center gap-1.5 text-[9px] font-black uppercase tracking-widest text-slate-400">
                                        <span class="w-1 h-1 rounded-full bg-slate-400"></span>
                                        Pending
                                    </span>
                                @elseif($r->progress == 2)
                                    <span
                                        class="flex items-center gap-1.5 text-[9px] font-black uppercase tracking-widest text-blue-600">
                                        <span class="w-1 h-1 rounded-full bg-blue-500 animate-pulse"></span>
                                        Reviewing
                                    </span>
                                @elseif($r->progress == 3)
                                    <span
                                        class="flex items-center gap-1.5 text-[9px] font-black uppercase tracking-widest text-emerald-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <polyline points="20 6 9 17 4 12" />
                                        </svg>
                                        Completed
                                    </span>
                                @endif
                            </div>
                            <div class="flex items-center gap-2.5">
                                <a href="{{ route('review.secure', ['token' => $r->token]) }}" wire:navigate
                                    class="p-2.5 bg-white text-primary hover:bg-primary hover:text-white rounded-xl border border-primary/20 shadow-sm transition-all"
                                    title="Login and Review">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                                        <polyline points="10 17 15 12 10 7" />
                                        <line x1="15" x2="3" y1="12" y2="12" />
                                    </svg>
                                </a>

                                @if($r->progress < 3)
                                    <button wire:click="markAsCompleted({{ $r->id }})"
                                        wire:confirm="Are you sure you want to mark this review as completed?"
                                        class="p-2.5 bg-emerald-500 text-white hover:brightness-110 rounded-xl shadow-lg shadow-emerald-500/20 transition-all"
                                        title="Mark as Completed">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <polyline points="20 6 9 17 4 12" />
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @if($reviews->hasPages())
            <div class="px-8 py-6 border-t border-border/50 bg-secondary/50">
                {{ $reviews->links(view: 'vendor.pagination.premium') }}
            </div>
        @endif
    </div>
</div>