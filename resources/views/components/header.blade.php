@props(['title', 'backUrl' => null, 'showBack' => true])

<div class="flex items-center gap-3">
    @if($showBack)
        @if($backUrl)
            <a href="{{ $backUrl }}" wire:navigate class="p-1.5 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition text-gray-500 hover:text-indigo-600 shadow-sm group">
                <svg class="w-5 h-5 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
        @else
            <button onclick="history.back()" class="p-1.5 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition text-gray-500 hover:text-indigo-600 shadow-sm group">
                <svg class="w-5 h-5 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </button>
        @endif
    @endif
    
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ $title }}
    </h2>
</div>
