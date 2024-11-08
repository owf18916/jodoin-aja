@props(['id', 'maxWidth' => null, 'submit' => null, 'modal' => null])

@php
$id = $id ?? md5($attributes->wire('model'));

$maxWidth = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
    '3xl' => 'sm:max-w-3xl',
    '4xl' => 'sm:max-w-4xl',
    '5xl' => 'sm:max-w-5xl',
    '6xl' => 'sm:max-w-6xl',
    '7xl' => 'sm:max-w-7xl',
][$maxWidth ?? '2xl'];
@endphp

<div
    {{-- {{ '@'.$modal }}.window="show = event.detail.open;console.log(event.detail)" --}}
    x-on:{{ $modal }}.window.dispatch="show = event.detail.open"
    x-data="{ show: false }"
    x-on:close.stop="show = false"
    x-on:keydown.escape.window="show = false"
    x-show="show"
    id="{{ $id }}"
    class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"
    style="display: none;"
>
    <div x-show="show"
        x-on:click="show = false"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 transform transition-all">
        <div class="absolute inset-0 bg-gray-500 z-50 opacity-75"></div>
    </div>

    <div 
        class="mb-6 bg-white rounded-lg shadow-xl transform transition-all sm:w-full {{ $maxWidth }} sm:mx-auto"
        x-show="show"
        x-trap.inert.noscroll="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
        @if ($submit)
            <form wire:submit="{{ $submit }}">
        @endif
        <div class="px-6 py-4">
            <div class="text-lg font-medium text-gray-900 flex justify-between">
                {{ $title }}
            </div>
    
            <div class="mt-4 text-sm text-gray-600">
                {{ $content }}
            </div>
        </div>
    
        <div class="flex flex-row justify-end px-6 py-4 bg-gray-100 text-end">
            {{ $footer }}
        </div>
        @if ($submit)
            </form>
        @endif
    </div>
</div>
