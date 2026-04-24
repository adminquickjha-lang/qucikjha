<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Models\Setting;

new #[Layout('layouts.safety')] class extends Component
{
    public string $headerColor = '#1a3a6b';
    public string $tableHeaderColor = '#2c5f9e';
    public string $ahaHeaderColor = '#1a3a6b';
    public string $ahaTableHeaderColor = '#2c5f9e';
    public string $ahaMainTableBg = '#ffffff';
    public string $jhaHeaderColor = '#1a3a6b';
    public string $jhaTableHeaderColor = '#2c5f9e';
    public string $jhaMainTableBg = '#ffffff';
    public string $jsaHeaderColor = '#1a3a6b';
    public string $jsaTableHeaderColor = '#2c5f9e';
    public string $jsaMainTableBg = '#ffffff';
    public string $racEColor = '#c0392b';
    public string $racHColor = '#e67e22';
    public string $racMColor = '#f1c40f';
    public string $racLColor = '#27ae60';
    public string $disclaimerText = '';
    public bool $saved = false;

    public function mount()
    {
        $this->headerColor = Setting::where('key', 'header_color')->value('value') ?? '#1a3a6b';
        $this->tableHeaderColor = Setting::where('key', 'table_header_color')->value('value') ?? '#2c5f9e';
        
        $this->ahaHeaderColor = Setting::where('key', 'aha_header_color')->value('value') ?? $this->headerColor;
        $this->ahaTableHeaderColor = Setting::where('key', 'aha_table_header_color')->value('value') ?? $this->tableHeaderColor;
        $this->ahaMainTableBg = Setting::where('key', 'aha_main_table_bg')->value('value') ?? '#ffffff';
        
        $this->jhaHeaderColor = Setting::where('key', 'jha_header_color')->value('value') ?? $this->headerColor;
        $this->jhaTableHeaderColor = Setting::where('key', 'jha_table_header_color')->value('value') ?? $this->tableHeaderColor;
        $this->jhaMainTableBg = Setting::where('key', 'jha_main_table_bg')->value('value') ?? '#ffffff';

        $this->jsaHeaderColor = Setting::where('key', 'jsa_header_color')->value('value') ?? $this->headerColor;
        $this->jsaTableHeaderColor = Setting::where('key', 'jsa_table_header_color')->value('value') ?? $this->tableHeaderColor;
        $this->jsaMainTableBg = Setting::where('key', 'jsa_main_table_bg')->value('value') ?? '#ffffff';

        $this->racEColor = Setting::where('key', 'rac_e_color')->value('value') ?? '#c0392b';
        $this->racHColor = Setting::where('key', 'rac_h_color')->value('value') ?? '#e67e22';
        $this->racMColor = Setting::where('key', 'rac_m_color')->value('value') ?? '#f1c40f';
        $this->racLColor = Setting::where('key', 'rac_l_color')->value('value') ?? '#27ae60';
        $this->disclaimerText = Setting::where('key', 'disclaimer_text')->value('value') ?? 'This document has been reviewed...';
    }

    public function handleSave()
    {
        Setting::updateOrCreate(['key' => 'aha_header_color'], ['value' => $this->ahaHeaderColor]);
        Setting::updateOrCreate(['key' => 'aha_table_header_color'], ['value' => $this->ahaTableHeaderColor]);
        Setting::updateOrCreate(['key' => 'aha_main_table_bg'], ['value' => $this->ahaMainTableBg]);

        Setting::updateOrCreate(['key' => 'jha_header_color'], ['value' => $this->jhaHeaderColor]);
        Setting::updateOrCreate(['key' => 'jha_table_header_color'], ['value' => $this->jhaTableHeaderColor]);
        Setting::updateOrCreate(['key' => 'jha_main_table_bg'], ['value' => $this->jhaMainTableBg]);

        Setting::updateOrCreate(['key' => 'jsa_header_color'], ['value' => $this->jsaHeaderColor]);
        Setting::updateOrCreate(['key' => 'jsa_table_header_color'], ['value' => $this->jsaTableHeaderColor]);
        Setting::updateOrCreate(['key' => 'jsa_main_table_bg'], ['value' => $this->jsaMainTableBg]);
        
        Setting::updateOrCreate(['key' => 'rac_e_color'], ['value' => $this->racEColor]);
        Setting::updateOrCreate(['key' => 'rac_h_color'], ['value' => $this->racHColor]);
        Setting::updateOrCreate(['key' => 'rac_m_color'], ['value' => $this->racMColor]);
        Setting::updateOrCreate(['key' => 'rac_l_color'], ['value' => $this->racLColor]);
        Setting::updateOrCreate(['key' => 'disclaimer_text'], ['value' => $this->disclaimerText]);

        $this->dispatch('swal', [
            'title' => 'Template Saved',
            'text' => 'Template settings have been successfully updated.',
            'icon' => 'success'
        ]);
    }
}; ?>

<div class="pt-4 pb-20 px-4 max-w-7xl mx-auto min-h-screen">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
        <div>
            <h1 class="text-4xl font-black tracking-tighter mb-1">Document Template</h1>
            <p class="text-muted-foreground text-sm font-medium">Customize the visual identity of created PDFs.</p>
        </div>
        <button wire:click="handleSave" class="flex items-center gap-2.5 px-6 py-3 rounded-xl bg-primary text-primary-foreground text-sm font-black uppercase tracking-widest shadow-xl shadow-primary/20 hover:brightness-110 active:scale-[0.98] transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            Save Template
        </button>
    </div>

    <div class="card-surface-xl p-10 bg-card border border-border/50">
        <!-- Global Colors -->
        <div class="mb-12">
            <h4 class="text-xs font-black uppercase tracking-[0.2em] text-muted-foreground mb-8 pb-2 border-b border-border/50">Global Assessment Colors (Standard)</h4>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach([
                    ['label' => 'Extreme Risk', 'key' => 'racEColor'],
                    ['label' => 'High Risk', 'key' => 'racHColor'],
                    ['label' => 'Moderate Risk', 'key' => 'racMColor'],
                    ['label' => 'Low Risk', 'key' => 'racLColor'],
                ] as $c)
                    <div class="p-6 rounded-2xl bg-secondary/50 border border-border">
                        <p class="font-black text-xs uppercase tracking-widest mb-4">{{ $c['label'] }}</p>
                        <div class="flex items-center gap-3">
                            <input type="color" wire:model="{{ $c['key'] }}" class="w-10 h-10 rounded-lg border border-border/50 cursor-pointer" />
                            <input type="text" wire:model="{{ $c['key'] }}" class="flex-1 bg-card ring-1 ring-border rounded-lg px-3 py-2 text-xs font-mono font-bold focus:ring-1 focus:ring-primary outline-none" />
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Document Specific Table Headers -->
        <div class="mb-12">
            <h4 class="text-xs font-black uppercase tracking-[0.2em] text-primary mb-8 pb-2 border-b border-primary/20">Document Table Headers</h4>
            <div class="grid md:grid-cols-3 gap-6">
                @foreach([
                    ['label' => 'AHA Table Header', 'key' => 'ahaTableHeaderColor'],
                    ['label' => 'JHA Table Header', 'key' => 'jhaTableHeaderColor'],
                    ['label' => 'JSA Table Header', 'key' => 'jsaTableHeaderColor'],
                ] as $c)
                    <div class="p-6 rounded-2xl bg-secondary/50 border border-border">
                        <p class="font-black text-xs uppercase tracking-widest mb-4">{{ $c['label'] }}</p>
                        <div class="flex items-center gap-3">
                            <input type="color" wire:model="{{ $c['key'] }}" class="w-10 h-10 rounded-lg border border-border/50 cursor-pointer" />
                            <input type="text" wire:model="{{ $c['key'] }}" class="flex-1 bg-card ring-1 ring-border rounded-lg px-3 py-2 text-xs font-mono font-bold focus:ring-1 focus:ring-primary outline-none" />
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Text Controls -->
        <div class="space-y-8">
            <h4 class="text-xs font-black uppercase tracking-[0.2em] text-muted-foreground mb-4 pb-2 border-b border-border/50">Default Document Content</h4>
            <div class="grid gap-6">
                <div>
                    <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground mb-2 block">Legal Disclaimer / Footer Text</label>
                    <textarea wire:model="disclaimerText" rows="4" class="w-full bg-secondary/50 ring-1 ring-border rounded-2xl p-5 text-sm font-medium focus:ring-2 focus:ring-primary outline-none transition-all resize-none shadow-inner"></textarea>
                </div>
            </div>
        </div>
    </div>
</div>
