@props(['title', 'subtitle' => null, 'backUrl' => null, 'backAction' => null, 'showBack' => true])

<div class="flex items-center gap-4">
    @if($showBack)
        @php
            $previous = url()->previous();
            $current = url()->current();
            $finalBackUrl = $backUrl ?? ($previous == $current ? route('dashboard') : $previous);
        @endphp
        
        @if($backAction)
            <button wire:click="{{ $backAction }}" 
                    class="p-2.5 bg-white border border-gray-200 rounded-2xl hover:bg-gray-50 transition-all text-gray-400 hover:text-indigo-600 shadow-sm group flex items-center justify-center"
                    title="Cerrar / Ir atrás">
                <svg class="w-6 h-6 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </button>
        @else
            <a href="{{ $finalBackUrl }}" 
               wire:navigate 
               class="p-2.5 bg-white border border-gray-200 rounded-2xl hover:bg-gray-50 transition-all text-gray-400 hover:text-indigo-600 shadow-sm group flex items-center justify-center"
               title="Ir atrás">
                <svg class="w-6 h-6 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
        @endif
    @endif
    
    <div class="flex flex-col">
        <h2 class="font-extrabold text-2xl text-gray-900 leading-tight tracking-tight">
            {{ $title }}
        </h2>
        @if($subtitle)
            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mt-0.5 opacity-70">{{ $subtitle }}</p>
        @endif
    </div>
</div>
