<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Proses Report') }}
        </h2>
    </x-slot>
    
    <div class="p-4">
        <livewire:activities.activity-table lazy="on-load" />
    </div>
</div>