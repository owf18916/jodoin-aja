<x-custom-modal maxWidth="2xl" modal="{{ $modal }}" submit="save">
    <x-slot name="title">
        Edit User
    </x-slot>

    <x-slot name="content">
        <div class="grid grid-cols-12 gap-2">
            <div class="col-span-12">
                <x-label for="form.name"  value="Name" />
                <x-input wire:model="form.name" id="form.name" type="text" class="mt-1 w-full" />
                <x-input-error for="form.name" class="mt-1" />
            </div>
            <div class="col-span-6">
                <x-label for="form.initial"  value="Initial" />
                <x-input wire:model="form.initial" id="form.initial" type="text" class="mt-1 w-full"/>
                <x-input-error for="form.initial" class="mt-1" />
            </div>
            <div class="col-span-6">
                <x-label for="form.email"  value="Email" />
                <x-input wire:model="form.email" id="form.email" type="text" class="mt-1 w-full"/>
                <x-input-error for="form.email" class="mt-1" />
            </div>
            <div class="col-span-6">
                <x-label for="roles"  value="Role" />
                <x-tom
                    x-init="$el.roles = new Tom($el, {
                        plugins: {
                            clear_button:{
                                title:'Hapus semua',
                            },
                            remove_button:{
                                title:'Hapus item ini',
                            }
                        },
                        sortField: {
                            field: 'name',
                            direction: 'asc'
                        },
                        valueField: 'name',
                        labelField: 'name',
                        searchField: 'name',
                        allowEmptyOption: true,
                    })"
                    @set-role-edit-options.window="
                        $el.roles.clear();
                        $el.roles.clearOptions();
                        $el.roles.addOptions(event.detail.data);
                        event.detail.id.forEach((name) => $el.roles.addItem(name));"
                    wire:model="form.roles"
                    id="roles"
                    class="mt-1 w-full"
                    multiple>
                    <option></option>
                </x-tom>
            </div>
            <div class="col-span-6">
                <x-label for="status"  value="Status" />
                <x-select
                    wire:model="form.status"
                    id="status"
                    class="mt-1 w-full">
                    <option></option>
                    @foreach ($form->setStatusOptions() as $status)
                        <option value="{{ $status['id'] }}" {{ $status['id'] == $form->status ? ' selected' : '' }}>{{ $status['label'] }}</option>
                    @endforeach
                </x-select>
            </div>
            @role('Administrator')
            <div class="col-span-12">
                <x-label for="permissions"  value="Permission" />
                <x-tom
                    x-init="$el.permissions = new Tom($el, {
                        plugins: {
                            clear_button:{
                                title:'Hapus semua',
                            },
                            remove_button:{
                                title:'Hapus item ini',
                            }
                        },
                        sortField: {
                            field: 'name',
                            direction: 'asc'
                        },
                        valueField: 'name',
                        labelField: 'name',
                        searchField: 'name',
                        allowEmptyOption: true,
                    })"
                    @set-permission-edit-options.window="
                        console.log(event.detail.id);
                        $el.permissions.clear();
                        $el.permissions.clearOptions();
                        $el.permissions.addOptions(event.detail.data);
                        event.detail.id.forEach((name) => $el.permissions.addItem(name));"
                    wire:model="form.permissions"
                    id="permissions"
                    class="mt-1 w-full"
                    multiple>
                    <option></option>
                </x-tom>
            </div>
            @endrole
        </div>
    </x-slot>

    <x-slot name="footer">
        <x-secondary-button class="me-3" x-on:click="$dispatch('{{ $modal }}', { open: false })">Cancel</x-secondary-button>
        <x-button x-on:click="">Save</x-button>
    </x-slot>

</x-custom-modal>
