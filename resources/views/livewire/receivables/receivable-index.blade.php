<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Receivable Document
        </h2>
    </x-slot>

    <livewire:receivables.receivable-table />
    <livewire:receivables.receivable-filter />
    <livewire:receivables.receivable-create />
    <livewire:receivables.receivable-edit />
    <livewire:receivables.receivable-delete />
    {{-- <livewire:receivables.receivable-download-document /> --}}
</div>