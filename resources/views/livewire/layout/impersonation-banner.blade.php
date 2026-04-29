<?php

use Livewire\Volt\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public function stopImpersonating()
    {
        if (! session()->has('impersonator_id')) {
            return;
        }

        $adminId = session()->pull('impersonator_id');
        $admin = User::find($adminId);

        if ($admin) {
            Auth::login($admin);
            session()->flash('success', "Impersonation ended. You have returned to your admin account.");
            return redirect()->route('admin.users');
        }

        Auth::logout();
        return redirect()->route('login');
    }
}; ?>

<div>
    @if(isImpersonating())
        <div class="bg-primary px-4 py-2 text-white border-b border-primary/20 shadow-lg relative z-50">
            <div class="max-w-7xl mx-auto flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="animate-pulse"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest leading-none opacity-80">System Impersonation</p>
                        <p class="text-sm font-bold tracking-tight">Viewing as: {{ auth()->user()->email }}</p>
                    </div>
                </div>
                <button
                    wire:click="stopImpersonating"
                    class="px-4 py-2 bg-white text-primary text-[10px] font-black uppercase tracking-widest rounded-lg hover:bg-white/90 transition-all shadow-md active:scale-95">
                    Return to Admin
                </button>
            </div>
        </div>
    @endif
</div>
