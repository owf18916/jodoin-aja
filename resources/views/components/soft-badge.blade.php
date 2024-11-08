@props(['text' => '', 'type'])

@php
    $colors = [
        'info' => 'bg-blue-200 text-blue-800',
        'secondary' => 'bg-gray-50 text-gray-600 ring-gray-500/10',
        'success' => 'bg-green-50 text-green-700 ring-green-600/20',
        'warning' => 'bg-yellow-200 text-yellow-800',
        'danger' => 'bg-red-50 text-red-700 ring-red-600/10'
    ];
@endphp

<span
    {{ $attributes->merge(['class' => 'inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset '.$colors[$type ?? 'info']]) }}>
    {{ $slot }}
</span>