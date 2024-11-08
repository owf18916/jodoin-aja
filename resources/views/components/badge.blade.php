@props(['text' => '', 'type'])

@php
    $colors = [
        'info' => 'bg-blue-200 text-blue-800',
        'basic' => 'bg-slate-200',
        'secondary' => 'bg-slate-500 text-white',
        'success' => 'bg-green-200 text-black',
        'warning' => 'bg-yellow-200 text-slate-500',
        'danger' => 'bg-red-200 text-red-800',
        'rose' => 'bg-rose-100 text-gray-800',
    ];
@endphp

<span
    {{ $attributes->merge(['class' => 'relative grid items-center px-2 py-1 inline-flex hover:opacity-60 text-xs text-center text-gray-900 leading-5 font-semibold rounded-full '.$colors[$type ?? 'info'] ]) }}>
    {{ $slot }}
</span>