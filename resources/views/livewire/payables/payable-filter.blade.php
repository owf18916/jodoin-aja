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
            <div class="col-span-6">
                <x-label for="bank"  value="Filter Bank" />
                <x-tom
                    x-init="$el.bank = new Tom($el, {
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
                        valueField: 'id',
                        labelField: 'name',
                        searchField: 'name',
                    })"
                    @set-bank-options.window="$el.bank.addOptions(event.detail.data)"
                    @set-reset.window="$el.bank.clear()"
                    wire:model="form.bank"
                    id="bank-filter"
                    class="mt-1 w-full"
                    multiple>
                    <option></option>
                </x-tom>
                <x-input-error for="form.bank" class="mt-1" />
            </div>
            <div class="col-span-6"></div>
            <div class="col-span-6" x-data="{ invoiceStartDate: null, invoiceEndDate: null }">
                <x-label for="start-date" value="Invoice Date" />
                <x-input wire:model="form.invoiceStartDate" id="invoice-start-date" type="date" x-on:change="$wire.updateFilter()" x-model="invoiceStartDate" />
    
                <x-label for="end-date"  value="s/d" />
                <x-input wire:model="form.invoiceEndDate" id="invoice-end-date" type="date" x-on:change="$wire.updateFilter()" x-model="invoiceEndDate" />
                
                <x-button type="button" x-on:click="invoiceStartDate = null;invoiceEndDate = null;$wire.resetInvoiceDate()" class="text-xs mt-2">clear</x-button>
            </div>
            <div class="col-span-6" x-data="{ paymentStartDate: null, paymentEndDate: null }">
                <x-label for="start-date" value="Payment Date" />
                <x-input wire:model="form.paymentStartDate" id="payment-start-date" type="date" x-on:change="$wire.updateFilter()" x-model="paymentStartDate" />
    
                <x-label for="end-date"  value="s/d" />
                <x-input wire:model="form.paymentEndDate" id="payment-end-date" type="date" x-on:change="$wire.updateFilter()" x-model="paymentEndDate" />
                
                <x-button type="button" x-on:click="paymentStartDate = null;paymentEndDate = null;$wire.resetPaymentDate()" class="text-xs mt-2">clear</x-button>
            </div>
        </div>
    </x-slot>

    <x-slot name="footer">
        <x-secondary-button class="me-3" x-on:click="$dispatch('{{ $modal }}', { open: false })">Cancel</x-secondary-button>
        <x-button>Filter</x-button>
    </x-slot>

</x-custom-modal>