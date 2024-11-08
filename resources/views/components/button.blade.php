@props(['type' => 'submit', 'color' => 'gray'])

@php
    $colors = [
        'blue' => 'bg-blue-800 hover:bg-blue-700 focus:bg-blue-700 text-blue-800',
        'gray' => 'bg-gray-800 hover:bg-gray-700 focus:bg-gray-700 text-white',
        'secondary' => 'bg-slate-500 hover:bg-slate-300 focus:bg-slate-300 text-white',
        'green' => 'bg-green-800 hover:bg-green-700 focus:bg-green-700 text-white',
        'lime' => 'bg-lime-500 hover:bg-lime-300 focus:bg-green-300 text-gray-800',
        'yellow' => 'bg-yellow-800 hover:bg-yellow-700 focus:bg-yellow-700 text-yellow-800',
        'red' => 'bg-red-800 hover:bg-red-700 focus:bg-red-700 text-red-800',
    ];
@endphp

<button
    {{ $attributes->merge(['type' => $type, 'class' => 'px-4 py-2 '.$colors[$color].' border border-transparent items-center text-center rounded-md font-semibold text-xs text-white uppercase tracking-widest active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
