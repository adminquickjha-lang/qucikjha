<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Models\Setting;

new #[Layout('layouts.safety')] class extends Component
{
    public string $jhaPrice = '19.90';
    public string $ahaPrice = '19.90';
    public string $jsaPrice = '19.00';

    public function mount()
    {
        $this->jhaPrice = Setting::where('key', 'jha_price')->value('value') ?? '19.90';
        $this->ahaPrice = Setting::where('key', 'aha_price')->value('value') ?? '19.90';
        $this->jsaPrice = Setting::where('key', 'jsa_price')->value('value') ?? '19.00';
    }

    public function handleSave()
    {
        Setting::updateOrCreate(['key' => 'jha_price'], ['value' => $this->jhaPrice]);
        Setting::updateOrCreate(['key' => 'aha_price'], ['value' => $this->ahaPrice]);
        Setting::updateOrCreate(['key' => 'jsa_price'], ['value' => $this->jsaPrice]);

        $this->dispatch('swal', [
            'title' => 'Pricing Updated',
            'text' => 'Global document pricing has been successfully updated.',
            'icon' => 'success'
        ]);
    }
}; ?>

<div class="pt-4 pb-20 px-4 max-w-7xl mx-auto min-h-screen">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
        <div>
            <h1 class="text-4xl font-black tracking-tighter mb-1">Service Pricing</h1>
            <p class="text-muted-foreground text-sm font-medium">Manage global pricing for all document types.</p>
        </div>
        <button wire:click="handleSave" class="flex items-center gap-2.5 px-6 py-3 rounded-xl bg-primary text-primary-foreground text-sm font-black uppercase tracking-widest shadow-xl shadow-primary/20 hover:brightness-110 active:scale-[0.98] transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            Save Pricing
        </button>
    </div>

    <div class="card-surface-xl p-10 bg-card border border-border/50">
        <div class="space-y-6">
            @foreach([
                ['label' => 'JHA – Job Hazard Analysis', 'key' => 'jhaPrice', 'desc' => 'Standard compliance document.'],
                ['label' => 'AHA – Activity Hazard Analysis', 'key' => 'ahaPrice', 'desc' => 'Enhanced activity-based analysis.'],
                ['label' => 'JSA – Job Safety Analysis', 'key' => 'jsaPrice', 'desc' => 'Sequential safety breakdown.'],
            ] as $price)
                <div class="p-6 rounded-2xl bg-secondary/50 border border-border flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <p class="font-black text-lg mb-1">{{ $price['label'] }}</p>
                        <p class="text-xs text-muted-foreground font-medium italic">{{ $price['desc'] }}</p>
                    </div>
                    <div class="flex items-center gap-4 bg-card rounded-xl p-2 px-4 shadow-sm border border-border/50">
                        <span class="text-base font-black text-primary">$</span>
                        <input 
                            wire:model="{{ $price['key'] }}" 
                            type="number" 
                            class="w-20 bg-transparent border-0 font-black text-xl p-0 focus:ring-0 text-center"
                        />
                        <span class="text-[10px] font-black uppercase tracking-widest text-muted-foreground">USD</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
