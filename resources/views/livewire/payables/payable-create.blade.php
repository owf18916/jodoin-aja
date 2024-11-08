<x-custom-modal maxWidth="2xl" modal="{{ $modal }}" submit="save">
    <x-slot name="title">
        <div class="flex flex-col">
            <span>
                Create Payable Document
            </span>
            <p class="text-xs">
                (*) <span class="text-red-500">Wajib diisi</span>
            </p>
        </div>
    </x-slot>

    <x-slot name="content">
        <div class="grid grid-cols-12 gap-2">
            <div class="col-span-12">
                <x-label for="supplier"  value="Nama Supplier (*)" sub="Ketikan nama / inisial supplier"/>
                <x-tom
                    x-init="$el.supplierField = new Tom($el, {
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
                    id="supplier">
                </x-tom>
                <x-input-error for="form.supplier" class="mt-1" />
            </div>
            <div class="col-span-6">
                <x-label for="bank"  value="Bank (*)" />
                <x-select
                    wire:model="form.bank"
                    id="bank"
                    class="mt-1 w-full text-sm">
                    <option></option>
                    @foreach ($form->setBankOptions() as $bank)
                        <option class="text-sm" value="{{ $bank->id }}">{{ $bank->initial.' - '.$bank->name }}</option>
                    @endforeach
                </x-select>
                <x-input-error for="form.bank" class="mt-1" />
            </div>
            <div class="col-span-6">
                <x-label for="currency"  value="Currency (*)" />
                <x-select
                    wire:model="form.currency"
                    id="currency"
                    class="mt-1 w-full text-sm">
                    <option></option>
                    @foreach ($form->setCurrencyOptions() as $currency)
                        <option class="text-sm" value="{{ $currency->id }}">{{ $currency->name.' - '.$currency->description }}</option>
                    @endforeach
                </x-select>
                <x-input-error for="form.currency" class="mt-1" />
            </div>
            <div class="col-span-6">
                <x-label for="form.invoiceNumber"  value="Invoice Number(*)" />
                <x-input wire:model="form.invoiceNumber" id="form.invoiceNumber" type="text" class="mt-1 w-full"/>
                <x-input-error for="form.invoiceNumber" class="mt-1" />
            </div>
            <div class="col-span-6">
                <x-label for="form.amount"  value="Amount(*)" />
                <x-input wire:model.live="form.amount" id="form.amount" type="text" class="mt-1 w-full"/>
                <x-input-error for="form.amount" class="mt-1" />
            </div>
            <div class="col-span-4">
                <x-label for="form.invoiceDate"  value="Invoice Date(*)" />
                <x-input wire:model="form.invoiceDate" id="form.invoiceDate" type="date" class="mt-1 w-full"/>
                <x-input-error for="form.invoiceDate" class="mt-1" />
            </div>
            <div class="col-span-4">
                <x-label for="form.paymentDate"  value="Payment Date(*)" />
                <x-input wire:model="form.paymentDate" id="form.paymentDate" type="date" class="mt-1 w-full"/>
                <x-input-error for="form.paymentDate" class="mt-1" />
            </div>
        </div>
    </x-slot>

    <x-slot name="footer">
        <x-secondary-button class="me-3" x-on:click="$dispatch('{{ $modal }}', { open: false })">Cancel</x-secondary-button>
        <x-button>Save</x-button>
    </x-slot>

</x-custom-modal>