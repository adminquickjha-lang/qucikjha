<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\User;

new #[Layout('layouts.safety')] class extends Component {
    use WithPagination;

    public ?string $search = null;
    public string $role = 'all';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRole()
    {
        $this->resetPage();
    }

    public function users()
    {
        $query = User::withCount('safetyDocuments');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->role !== 'all') {
            $query->where('role', $this->role);
        }

        return $query->latest()
            ->paginate(10);
    }

    public function impersonate($userId)
    {
        if (!auth()->user()->canImpersonate()) {
            abort(403);
        }

        $userToImpersonate = User::findOrFail($userId);

        if ($userToImpersonate->id === auth()->id()) {
            $this->dispatch('swal', title: 'Error', text: 'You cannot impersonate yourself.', icon: 'error');
            return;
        }

        session(['impersonator_id' => auth()->id()]);
        auth()->login($userToImpersonate);

        $displayName = $userToImpersonate->name ?: $userToImpersonate->email;
        session()->flash('success', "You are now impersonating {$displayName}.");

        return redirect()->route('user-dashboard');
    }

    public function verifyEmail($userId)
    {
        if (!auth()->user()->canImpersonate()) {
            abort(403);
        }

        $user = User::findOrFail($userId);
        $user->markEmailAsVerified();

        $this->dispatch('swal', title: 'Success', text: $user->email . ' has been verified manually.', icon: 'success');
    }
}; ?>

<div class="pt-4 pb-20 px-4 max-w-7xl mx-auto min-h-screen">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
        <div>
            <h1 class="text-4xl font-black tracking-tighter mb-1">User Management</h1>
            <p class="text-muted-foreground text-sm font-medium">Manage platform members and their activity.</p>
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
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search members..."
                class="w-full bg-transparent border-0 p-0 text-sm font-bold tracking-tight text-slate-900 placeholder:text-slate-400 focus:ring-0 outline-none md:hidden" />
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by name or email address..."
                class="hidden md:block w-full bg-transparent border-0 p-0 text-sm font-bold tracking-tight text-slate-900 placeholder:text-slate-400 placeholder:font-medium focus:ring-0 outline-none" />
        </div>

        <div class="flex flex-col md:flex-row items-stretch md:items-center gap-4 md:gap-6 w-full lg:w-auto">
            <div class="flex items-center gap-3 flex-grow md:flex-grow-0 h-[46px]">
                <div class="relative group w-full md:min-w-[200px]">
                    <select wire:model.live="role"
                        class="w-full appearance-none bg-white border border-slate-200 rounded-xl px-4 py-2.5 pr-11 text-xs font-bold text-slate-700 shadow-sm transition-all duration-300 hover:border-primary/60 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-primary/10 focus:border-primary cursor-pointer h-[46px]">
                        <option value="all">All Roles</option>
                        <option value="admin">Administrator</option>
                        <option value="user">Subscriber</option>
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
        </div>
    </div>

    <div class="card-surface-xl bg-card border border-border/50 shadow-soft overflow-hidden">
        <div class="p-8 border-b border-border/50">
            <h3 class="text-xl font-black tracking-tight">Active Users</h3>
            <p class="text-xs text-muted-foreground font-medium mt-1">Status and document count for all registered
                members.</p>
        </div>
        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-secondary/50 text-muted-foreground">
                        <th
                            class="px-8 py-5 text-[10px] font-black uppercase tracking-widest border-b border-border/50">
                            Member</th>
                        <th
                            class="px-8 py-5 text-[10px] font-black uppercase tracking-widest border-b border-border/50">
                            Access Role</th>
                        <th
                            class="px-8 py-5 text-[10px] font-black uppercase tracking-widest border-b border-border/50 text-center">
                            Documents</th>
                        <th
                            class="px-8 py-5 text-[10px] font-black uppercase tracking-widest border-b border-border/50">
                            Current Status</th>
                        <th
                            class="px-8 py-5 text-[10px] font-black uppercase tracking-widest border-b border-border/50 text-right">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border/30">
                    @php $usersList = $this->users(); @endphp
                    @foreach($usersList as $u)
                        <tr class="hover:bg-secondary/20 transition-colors">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-black text-xs">
                                        {{ strtoupper(substr($u->name ?: $u->email, 0, 2)) }}
                                    </div>
                                    <span class="text-sm font-bold tracking-tight">{{ $u->email }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <span
                                    class="px-2.5 py-0.5 rounded-full {{ $u->role === 'admin' ? 'bg-primary/10 text-primary border-primary/20' : 'bg-secondary text-muted-foreground border-border' }} border text-[9px] font-black uppercase tracking-widest">
                                    {{ $u->role === 'admin' ? 'Administrator' : 'Subscriber' }}
                                </span>
                            </td>
                            <td class="px-8 py-6 text-sm font-bold text-center">{{ $u->safety_documents_count }}</td>
                            <td class="px-8 py-6">
                                <span
                                    class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest {{ $u->hasVerifiedEmail() ? 'text-emerald-600' : 'text-amber-600' }}">
                                    <span
                                        class="w-1.5 h-1.5 rounded-full {{ $u->hasVerifiedEmail() ? 'bg-emerald-500 animate-pulse' : 'bg-amber-500' }}"></span>
                                    {{ $u->hasVerifiedEmail() ? 'Operational' : 'Pending Verification' }}
                                </span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                @if(auth()->user()->canImpersonate() && $u->id !== auth()->id())
                                    <button @click="
                                                    Swal.fire({
                                                        title: 'Impersonate User?',
                                                        text: 'You are about to log in as {{ $u->email }}. Any actions you take will be on their behalf.',
                                                        icon: 'info',
                                                        showCancelButton: true,
                                                        confirmButtonText: 'Yes, Proceed',
                                                        cancelButtonText: 'Cancel',
                                                        confirmButtonColor: 'hsl(199, 89%, 48%)',
                                                        cancelButtonColor: '#94a3b8',
                                                        background: '#ffffff',
                                                        customClass: {
                                                            popup: 'rounded-3xl border-none shadow-2xl p-6',
                                                            title: 'text-2xl font-black tracking-tight text-slate-900',
                                                            htmlContainer: 'text-sm text-slate-500 font-medium leading-relaxed',
                                                            confirmButton: 'rounded-xl font-black uppercase tracking-widest text-[10px] px-8 py-4',
                                                            cancelButton: 'rounded-xl font-black uppercase tracking-widest text-[10px] px-8 py-4'
                                                        }
                                                    }).then((result) => {
                                                        if (result.isConfirmed) {
                                                            $wire.impersonate({{ $u->id }})
                                                        }
                                                    })
                                                "
                                        class="inline-flex items-center px-4 py-2 border border-border/50 text-[10px] font-black uppercase tracking-widest rounded-xl text-primary hover:bg-primary/5 transition-all shadow-sm">
                                        Impersonate
                                    </button>

                                    @if(!$u->hasVerifiedEmail())
                                        <button @click="
                                                            Swal.fire({
                                                                title: 'Verify Email Manually?',
                                                                text: 'This will instantly mark {{ $u->email }} as verified. Only do this if you have confirmed the user\'s identity.',
                                                                icon: 'info',
                                                                showCancelButton: true,
                                                                confirmButtonText: 'Verify Now',
                                                                cancelButtonText: 'Cancel',
                                                                confirmButtonColor: 'hsl(142, 72%, 29%)',
                                                                cancelButtonColor: '#94a3b8',
                                                                background: '#ffffff',
                                                                customClass: {
                                                                    popup: 'rounded-3xl border-none shadow-2xl p-6',
                                                                    title: 'text-2xl font-black tracking-tight text-slate-900',
                                                                    htmlContainer: 'text-sm text-slate-500 font-medium leading-relaxed',
                                                                    confirmButton: 'rounded-xl font-black uppercase tracking-widest text-[10px] px-8 py-4',
                                                                    cancelButton: 'rounded-xl font-black uppercase tracking-widest text-[10px] px-8 py-4'
                                                                }
                                                            }).then((result) => {
                                                                if (result.isConfirmed) {
                                                                    $wire.verifyEmail({{ $u->id }})
                                                                }
                                                            })
                                                        "
                                            class="ml-2 inline-flex items-center px-4 py-2 border border-emerald-200 text-[10px] font-black uppercase tracking-widest rounded-xl text-emerald-600 bg-emerald-50/50 hover:bg-emerald-50 transition-all shadow-sm">
                                            Verify User
                                        </button>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="block md:hidden">
            @php $usersList = $this->users(); @endphp
            <div class="p-4 space-y-4">
                @foreach($usersList as $u)
                    <div
                        class="bg-card border border-border/40 rounded-[2rem] p-6 shadow-sm hover:shadow-md transition-all duration-300">
                        <div class="flex justify-between items-start gap-4 mb-6">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center font-black text-xs border border-primary/20">
                                    {{ strtoupper(substr($u->name ?: $u->email, 0, 2)) }}
                                </div>
                                <div class="space-y-0.5">
                                    <p class="text-[13px] font-black tracking-tight text-slate-900 leading-tight">
                                        {{ Str::limit($u->name ?: 'Member', 20) }}
                                    </p>
                                    <p
                                        class="text-[9px] text-muted-foreground font-black uppercase tracking-widest italic opacity-70">
                                        {{ Str::limit($u->email, 25) }}
                                    </p>
                                </div>
                            </div>
                            <span
                                class="px-2.5 py-1 rounded-full {{ $u->role === 'admin' ? 'bg-primary/10 text-primary border-primary/20' : 'bg-secondary text-muted-foreground border-border' }} border text-[7px] font-black uppercase tracking-widest shadow-sm">
                                {{ $u->role === 'admin' ? 'Admin' : 'Subscriber' }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-6 pt-6 border-t border-border/30">
                            <div class="space-y-1">
                                <p class="text-[8px] font-black uppercase tracking-widest text-muted-foreground opacity-60">
                                    Activity</p>
                                <p class="text-[10px] font-black tracking-tight text-slate-700 uppercase italic">
                                    {{ $u->safety_documents_count }} Documents</p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-[8px] font-black uppercase tracking-widest text-muted-foreground opacity-60">
                                    Security</p>
                                <div class="flex items-center gap-1.5">
                                    <span
                                        class="w-1 h-1 rounded-full {{ $u->hasVerifiedEmail() ? 'bg-emerald-500 animate-pulse' : 'bg-amber-500' }}"></span>
                                    <span
                                        class="text-[9px] font-black uppercase tracking-widest {{ $u->hasVerifiedEmail() ? 'text-emerald-600' : 'text-amber-600' }}">
                                        {{ $u->hasVerifiedEmail() ? 'Verified' : 'Pending' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2.5 pt-2">
                            @if(auth()->user()->canImpersonate() && $u->id !== auth()->id())
                                <div class="flex flex-wrap items-center gap-2 w-full">
                                    <button @click="
                                                    Swal.fire({
                                                        title: 'Quick Access',
                                                        text: 'Access {{ $u->email }} profile?',
                                                        icon: 'info',
                                                        showCancelButton: true,
                                                        confirmButtonText: 'Proceed',
                                                        cancelButtonText: 'Cancel',
                                                        confirmButtonColor: 'hsl(142, 72%, 29%)',
                                                        cancelButtonColor: '#94a3b8',
                                                        background: '#ffffff',
                                                        customClass: {
                                                            popup: 'rounded-[2rem] border-none shadow-2xl p-8',
                                                            title: 'text-xl font-black tracking-tighter text-slate-900',
                                                            htmlContainer: 'text-[13px] text-slate-500 font-medium leading-relaxed',
                                                            confirmButton: 'rounded-xl font-black uppercase tracking-widest text-[9px] px-8 py-4',
                                                            cancelButton: 'rounded-xl font-black uppercase tracking-widest text-[9px] px-8 py-4'
                                                        }
                                                    }).then((result) => {
                                                        if (result.isConfirmed) {
                                                            $wire.impersonate({{ $u->id }})
                                                        }
                                                    })
                                                "
                                        class="inline-flex items-center justify-center px-6 h-11 bg-primary text-white text-[9px] font-black uppercase tracking-widest rounded-2xl shadow-lg shadow-primary/20 hover:brightness-110 active:scale-[0.98] transition-all">
                                        Impersonate
                                    </button>

                                    @if(!$u->hasVerifiedEmail())
                                        <button @click="
                                                            Swal.fire({
                                                                title: 'Manual Verify',
                                                                text: 'Mark {{ $u->email }} as verified?',
                                                                icon: 'info',
                                                                showCancelButton: true,
                                                                confirmButtonText: 'Verify Now',
                                                                cancelButtonText: 'Cancel'
                                                            }).then((result) => {
                                                                if (result.isConfirmed) {
                                                                    $wire.verifyEmail({{ $u->id }})
                                                                }
                                                            })
                                                        "
                                            class="inline-flex items-center justify-center px-5 h-11 border border-emerald-200 text-[9px] font-black uppercase tracking-widest rounded-2xl text-emerald-600 bg-emerald-50 shadow-sm hover:bg-emerald-100/50 transition-all">
                                            Verify User
                                        </button>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @if($usersList->hasPages())
            <div class="px-8 py-6 border-t border-border/50 bg-secondary/50">
                {{ $usersList->links(view: 'vendor.pagination.premium') }}
            </div>
        @endif
    </div>
</div>