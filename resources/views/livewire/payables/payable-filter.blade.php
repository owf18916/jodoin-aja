<x-custom-modal maxWidth="2xl" modal="{{ $modal }}" submit="filter">
    <x-slot name="title">
        Filter Dokumen Payable
    </x-slot>

    <x-slot name="content">
        <div class="grid grid-cols-12 gap-2">
            <div class="col-span-6">
                <x-label for="status"  value="Filter Status" />
                <x-tom
                    x-init="$el.status = new Tom($el, {
                        plugins: {
                            clear_button:{
                                title:'Hapus semua',
                            },
                            remove_button:{
                                title:'Hapus item ini',
                            }
                        },
                        sortField: {
                            field: 'label',
                            direction: 'asc'
                        },
                        valueField: 'id',
                        labelField: 'label',
                        searchField: 'label',
                    })"
                    @set-status-options.window="$el.status.addOptions(event.detail.data)"
                    @set-reset.window="$el.status.clear()"
                    wire:model="form.status"
                    id="status-filter"
                    class="mt-1 w-full"
                    multiple>
                    <option></option>
                </x-tom>
                <x-input-error for="form.status" class="mt-1" />
            </div>
            <div class="col-span-6">
                <x-label for="supplier"  value="Nama Supplier" sub="Ketikan nama / inisial supplier"/>
                <x-tom
                    x-init="$el.supplierField = new Tom($el, {
                        plugins: {
                            clear_button:{
                                title:'Hapus semua',
                            },
                            remove_button:{
                                title:'Hapus item ini',
                            }
                        },
                        sortField: {
                            field: 'supplierLabel',
                            direction: 'asc'
                        },
                        valueField: 'id',
                        labelField: 'supplierLabel',
                        searchField: 'supplierLabel',
                        load: function(query, callback) {
                            $wire.getSuppliers(query).then(results => {
                                callback(results)
                            }).catch(() => {
                                callback()
                            })
                        },
                        loadThrottle: 500,
                    })"
                    wire:model="form.supplier"
                    @set-reset.window="$el.supplierField.clear();$el.supplierField.clearOptions()"
                    id="supplier"
                    multiple>
                </x-tom>
                <x-input-error for="form.supplier" class="mt-1" />
            </div>
            <div class="col-span-6"></div>
            <div class="col-span-6" x-data="{ accountedStartDate: null, accountedEndDate: null }">
                <x-label for="start-date" value="Accounting Date" />
                <x-input wire:model="form.accountedStartDate" id="accounted-start-date" type="date" x-on:change="$wire.updateFilter()" x-model="accountedStartDate" />
    
                <x-label for="end-date"  value="s/d" />
                <x-input wire:model="form.accountedEndDate" id="accounted-end-date" type="date" x-on:change="$wire.updateFilter()" x-model="accountedEndDate" />
                
                <x-button type="button" x-on:click="accountedStartDate = null;accountedEndDate = null;$wire.resetAccountedDate()" class="text-xs mt-2">clear</x-button>
            </div>
        </div>
    </x-slot>

    <x-slot name="footer">
        <x-secondary-button class="me-3" x-on:click="$dispatch('{{ $modal }}', { open: false })">Cancel</x-secondary-button>
        <x-button>Filter</x-button>
    </x-slot>

</x-custom-modal>