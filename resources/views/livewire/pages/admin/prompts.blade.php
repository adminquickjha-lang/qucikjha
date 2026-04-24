<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.safety')] class extends Component {
    //
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
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                    </svg>
                    AI Management
                </span>
            </div>
            <h1 class="text-4xl font-black tracking-tighter mb-1">AI Prompts</h1>
            <p class="text-muted-foreground text-sm font-medium">Manage AI prompts and generation settings.</p>
        </div>
    </div>

    <div class="card-surface-xl bg-card border border-border/50 shadow-soft overflow-hidden">
        <div class="p-8 border-b border-border/50">
            <h3 class="text-xl font-black tracking-tight">AI Prompt Management</h3>
            <p class="text-xs text-muted-foreground font-medium mt-1">Configure AI prompts for document generation.</p>
        </div>
        <div class="p-8">
            <div class="text-center py-12">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="mx-auto text-muted-foreground/50 mb-4">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                </svg>
                <h3 class="text-lg font-bold mb-2">AI Prompts Coming Soon</h3>
                <p class="text-muted-foreground text-sm">This section will allow you to manage AI prompts and generation
                    settings.</p>
            </div>
        </div>
    </div>
</div>