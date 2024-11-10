<x-custom-modal maxWidth="2xl" modal="{{ $modal }}" submit="save">
    <x-slot name="title">
        <div class="flex flex-col">
            <span>
                Create Currency
            </span>
            <p class="text-xs">
                (*) <span class="text-red-500">Wajib diisi</span>
            </p>
        </div>
    </x-slot>

    <x-slot name="content">
        <div class="grid grid-cols-12 gap-2">
            <div class="col-span-3">
                <x-label for="name"  value="Nama Currency(*)" />
                <x-input wire:model="name" id="name" type="text" class="mt-1 w-full"/>
                <x-input-error for="name" class="mt-1" />
            </div>
            <div class="col-span-6">
                <x-label for="description"  value="Deskripsi(*)" />
                <x-input wire:model="description" id="description" type="text" class="mt-1 w-full"/>
                <x-input-error for="description" class="mt-1" />
            </div>
            <div class="col-span-3">
                <x-label for="slug"  value="Slug(*)" />
                <x-input wire:model="slug" id="slug" type="text" class="mt-1 w-full"/>
                <x-input-error for="slug" class="mt-1" />
            </div>
        </div>
    </x-slot>

    <x-slot name="footer">
        <x-secondary-button class="me-3" x-on:click="$dispatch('{{ $modal }}', { open: false })">Cancel</x-secondary-button>
        <x-button>Save</x-button>
    </x-slot>

</x-custom-modal>