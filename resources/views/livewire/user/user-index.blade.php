<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            User
        </h2>
    </x-slot>
    
    <div class="p-4">
        <livewire:user.user-table />
    
        <livewire:user.user-edit lazy="on-load" />
    </div>
</div>