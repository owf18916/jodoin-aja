@props(['text' => '', 'type'])

@php
    $colors = [
        'info' => 'border-2 border-blue-200 text-blue-800',
        'basic' => 'border-2 border-slate-200',
        'secondary' => 'border-2 border-slate-500 text-white',
        'success' => 'border-2 border-green-200 text-green-800',
        'warning' => 'border-2 border-yellow-200 text-slate-500',
        'danger' => 'border-2 border-red-200 text-red-800',
        'rose' => 'border-2 border-rose-100 text-gray-800',
    ];
@endphp

<a
    {{ $attributes->merge(['class' => 'relative grid items-center px-2 py-1 inline-flex hover:opacity-60 text-xs text-center text-gray-900 leading-5 font-semibold rounded-full '.$colors[$type ?? 'info'] ]) }}>
    {{ $slot }}
</a>