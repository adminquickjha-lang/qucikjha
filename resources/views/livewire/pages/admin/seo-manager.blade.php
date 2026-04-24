<?php

use App\Models\Seo;
use Illuminate\Support\Facades\Cache;
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Layout;

new #[Layout('layouts.safety')] class extends Component {
    public $seos;
    public $selectedSeo = null;

    #[Validate('required|string|max:255')]
    public $label = '';

    #[Validate('required|string|max:255')]
    public $title = '';

    #[Validate('required|string')]
    public $description = '';

    #[Validate('nullable|string|max:255')]
    public $keywords = '';

    #[Validate('nullable|string|max:255')]
    public $og_image = '';

    public function mount()
    {
        $this->loadSeos();

        // Select first item by default if available
        if ($this->seos->count() > 0) {
            $this->selectSeo($this->seos->first()->id);
        }
    }

    public function loadSeos()
    {
        $this->seos = Seo::all();
    }

    public function selectSeo($id)
    {
        $this->selectedSeo = Seo::find($id);

        if ($this->selectedSeo) {
            $this->label = $this->selectedSeo->label;
            $this->title = $this->selectedSeo->title;
            $this->description = $this->selectedSeo->description;
            $this->keywords = $this->selectedSeo->keywords;
            $this->og_image = $this->selectedSeo->og_image;
        }
    }

    public function update()
    {
        $this->validate();

        if ($this->selectedSeo) {
            $this->selectedSeo->update([
                'label' => $this->label,
                'title' => $this->title,
                'description' => $this->description,
                'keywords' => $this->keywords,
                'og_image' => $this->og_image,
            ]);

            // Clear cache after update
            Cache::forget("seo.{$this->selectedSeo->key}");
            Cache::forget("seo_arr.{$this->selectedSeo->key}");

            $this->loadSeos();

            $this->dispatch('seo-updated');

            session()->flash('message', 'SEO record updated successfully!');
        }
    }
}; ?>
<div class="pt-4 pb-20 px-4 max-w-7xl mx-auto min-h-screen">
    <div class="bg-white overflow-hidden shadow-sm rounded-2xl">
        <div class="p-2 sm:p-6">


            <div class="flex flex-col lg:flex-row lg:h-[80vh] bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
                <!-- Sidebar -->
                <div class="w-full lg:w-1/3 border-b lg:border-b-0 lg:border-r border-gray-100 bg-gray-50/50 flex flex-col">
                    <div class="p-4 lg:p-6 border-b border-gray-100 bg-white">
                        <h2 class="text-lg lg:text-xl font-bold text-gray-800">SEO Settings</h2>
                        <p class="text-xs lg:text-sm text-gray-500 mt-1">Select a page to edit its meta data</p>
                    </div>

                    <nav class="overflow-y-auto max-h-48 lg:max-h-full lg:h-full pb-4 lg:pb-20">
                        @foreach($seos as $seo)
                            <button wire:click="selectSeo({{ $seo->id }})" @class([
                                'w-full text-left p-4 transition-all duration-200 flex items-center gap-3',
                                'bg-primary/5 border-r-4 border-primary text-primary' => $selectedSeo && $selectedSeo->id === $seo->id,
                                'hover:bg-gray-100 text-gray-600' => !($selectedSeo && $selectedSeo->id === $seo->id)
                            ])>
                                <div @class([
                                    'w-2 h-2 rounded-full',
                                    'bg-primary' => $selectedSeo && $selectedSeo->id === $seo->id,
                                    'bg-gray-300' => !($selectedSeo && $selectedSeo->id === $seo->id)
                                ])></div>
                                <div class="flex flex-col">
                                    <span class="font-semibold">{{ $seo->label }}</span>
                                    <span class="text-xs opacity-70">Key: {{ $seo->key }}</span>
                                </div>
                            </button>
                        @endforeach
                    </nav>
                </div>

                <!-- Edit Form -->
                <div class="w-full lg:w-2/3 p-4 lg:p-8 overflow-y-auto">
                    @if($selectedSeo)
                        <div class="max-w-2xl mx-auto">
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-4">
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-800">Edit {{ $selectedSeo->label }}</h3>
                                    <p class="text-gray-500">Key: <span
                                            class="font-mono text-sm bg-gray-100 px-2 py-0.5 rounded text-primary">{{ $selectedSeo->key }}</span>
                                    </p>
                                </div>
                                <div class="w-full md:w-auto">
                                    <button type="button" wire:click="update"
                                        class="w-full md:w-auto bg-primary hover:brightness-110 text-white font-bold py-3 px-6 rounded-xl transition-all duration-200 transform hover:scale-[1.01] active:scale-[0.99] shadow-md hover:shadow-lg flex items-center justify-center gap-2 border-0">
                                        <span wire:loading.remove wire:target="update">Update SEO Record</span>
                                        <span wire:loading wire:target="update">Updating...</span>
                                        <svg wire:loading.remove wire:target="update" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            @if(session()->has('message'))
                                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                                    class="bg-green-100 text-green-700 px-4 py-2 rounded-lg text-sm font-medium animate-fade-in-out mb-6">
                                    {{ session('message') }}
                                </div>
                            @endif

                            <form wire:submit="update" class="space-y-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Display Name (System
                                        Label)</label>
                                    <input type="text" wire:model="label"
                                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                                    @error('label') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">SEO Title</label>
                                    <input type="text" wire:model="title"
                                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                                    @error('title') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                    <p class="text-xs text-right mt-1"
                                        :class="($wire.title.length > 60) ? 'text-amber-500' : 'text-gray-400'">
                                        <span x-text="$wire.title.length"></span>/60 characters
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Meta Description</label>
                                    <textarea wire:model="description" rows="4"
                                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-primary transition-all"></textarea>
                                    @error('description') <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                    <p class="text-xs text-right mt-1"
                                        :class="($wire.description.length > 160) ? 'text-amber-500' : 'text-gray-400'">
                                        <span x-text="$wire.description.length"></span>/160 characters
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Keywords (Comma
                                        separated)</label>
                                    <input type="text" wire:model="keywords"
                                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                                    @error('keywords') <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Social Sharing Image (OG
                                        Image
                                        URL)</label>
                                    <input type="text" wire:model="og_image" placeholder="https://example.com/image.jpg"
                                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                                    @error('og_image') <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="h-full flex flex-col items-center justify-center text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mb-4 opacity-20" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-lg">Select a record to start editing</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>