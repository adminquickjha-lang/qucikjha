@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between py-4">
        {{-- Mobile View --}}
        <div class="flex flex-1 justify-between sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-6 py-3 text-[11px] font-black uppercase tracking-widest text-slate-400 bg-slate-50 border border-slate-200 rounded-2xl cursor-not-allowed select-none">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <button wire:click="previousPage" dusk="previousPage-after" class="relative inline-flex items-center px-6 py-3 text-[11px] font-black uppercase tracking-widest text-slate-700 bg-white border border-slate-200 rounded-2xl hover:bg-slate-50 hover:border-primary/30 active:scale-95 transition-all shadow-sm">
                    {!! __('pagination.previous') !!}
                </button>
            @endif

            @if ($paginator->hasMorePages())
                <button wire:click="nextPage" dusk="nextPage-after" class="relative inline-flex items-center px-6 py-3 text-[11px] font-black uppercase tracking-widest text-slate-700 bg-white border border-slate-200 rounded-2xl hover:bg-slate-50 hover:border-primary/30 active:scale-95 transition-all shadow-sm">
                    {!! __('pagination.next') !!}
                </button>
            @else
                <span class="relative inline-flex items-center px-6 py-3 text-[11px] font-black uppercase tracking-widest text-slate-400 bg-slate-50 border border-slate-200 rounded-2xl cursor-not-allowed select-none">
                    {!! __('pagination.next') !!}
                </span>
            @endif
        </div>

        {{-- Desktop View --}}
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-[0.2em] italic">
                    {!! __('Showing') !!} <span class="text-slate-900">{{ $paginator->firstItem() ?? 0 }}</span> {!! __('to') !!} <span class="text-slate-900">{{ $paginator->lastItem() ?? 0 }}</span> {!! __('of') !!} <span class="text-slate-900">{{ $paginator->total() }}</span> {!! __('Results') !!}
                </p>
            </div>

            <div class="flex items-center gap-2">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span class="w-10 h-10 flex items-center justify-center text-slate-300 bg-slate-50 border border-slate-100 rounded-xl cursor-not-allowed select-none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                    </span>
                @else
                    <button wire:click="previousPage" dusk="previousPage-before"
                       class="w-10 h-10 flex items-center justify-center text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 hover:border-primary/50 hover:text-primary transition-all active:scale-90 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                    </button>
                @endif

                {{-- Pagination Elements --}}
                <div class="flex items-center gap-1.5 p-1 bg-slate-100/50 rounded-2xl border border-slate-200/50">
                    @foreach ($elements as $element)
                        @if (is_string($element))
                            <span class="px-3 text-[11px] font-black text-slate-400">{{ $element }}</span>
                        @endif

                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page"
                                          class="w-10 h-10 flex items-center justify-center bg-primary text-primary-foreground text-[11px] font-black rounded-xl shadow-lg shadow-primary/20 select-none">
                                        {{ $page }}
                                    </span>
                                @else
                                    <button wire:click="gotoPage({{ $page }})"
                                       class="w-10 h-10 flex items-center justify-center text-slate-600 hover:bg-white hover:text-primary hover:shadow-md rounded-xl text-[11px] font-black transition-all active:scale-90">
                                        {{ $page }}
                                    </button>
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                </div>

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <button wire:click="nextPage" dusk="nextPage-before"
                       class="w-10 h-10 flex items-center justify-center text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 hover:border-primary/50 hover:text-primary transition-all active:scale-90 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                    </button>
                @else
                    <span class="w-10 h-10 flex items-center justify-center text-slate-300 bg-slate-50 border border-slate-100 rounded-xl cursor-not-allowed select-none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                    </span>
                @endif
            </div>
        </div>
    </nav>
@endif
