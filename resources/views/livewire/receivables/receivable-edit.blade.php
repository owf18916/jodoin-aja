<x-custom-modal maxWidth="2xl" modal="{{ $modal }}" submit="save">
    <x-slot name="title">
        <div class="flex flex-col">
            <span>
                Edit Receivable Document
            </span>
            <p class="text-xs">
                (*) <span class="text-red-500">Wajib diisi</span>
            </p>
        </div>
    </x-slot>

    <x-slot name="content">
        <div class="grid grid-cols-12 gap-2">
            <div class="col-span-4">
                <x-label for="category"  value="Kategori (*)" />
                <x-select
                    wire:model="form.category"
                    id="category"
                    class="mt-1 w-full text-sm">
                    <option></option>
                    @foreach ($form->setCategoryOptions() as $key => $category)
                        <option class="text-sm" value="{{ $key }}" {{ $key == $form->category ? ' selected' : '' }}>{{ $category }}</option>
                    @endforeach
                </x-select>
                <x-input-error for="form.category" class="mt-1" />
            </div>
            <div class="col-span-8">
                <x-label for="customer"  value="Nama Customer (*)" sub="Ketikan nama / inisial customer"/>
                <x-tom
                    x-init="$el.customerField = new Tom($el, {
                        sortField: {
                            field: 'customerLabel',
                            direction: 'asc'
                        },
                        valueField: 'id',
                        labelField: 'customerLabel',
                        searchField: 'customerLabel',
                        load: function(query, callback) {
                            $wire.getCustomers(query).then(results => {
                                callback(results)
                            }).catch(() => {
                                callback()
                            })
                        },
                        loadThrottle: 500,
                    })"
                    @set-customer-receivable-edit.window="
                        $el.customerField.clear();
                        $el.customerField.clearOptions();
                        $el.customerField.addOptions(event.detail.data);
                        $el.customerField.addItem(event.detail.id);"
                    wire:model="form.customer"
                    @set-reset.window="$el.customerField.clear();$el.customerField.clearOptions()"
                    id="customer">
                </x-tom>
                <x-input-error for="form.customer" class="mt-1" />
            </div>
            <div class="col-span-6">
                <x-label for="form.invoiceNumber"  value="Invoice Number(*)" />
                <x-input wire:model="form.invoiceNumber" id="form.invoiceNumber" type="text" class="mt-1 w-full"/>
                <x-input-error for="form.invoiceNumber" class="mt-1" />
            </div>
            <div class="col-span-6">
                <x-label for="form.blNumber"  value="BL Number" />
                <x-input wire:model="form.blNumber" id="form.blNumber" type="text" class="mt-1 w-full"/>
                <x-input-error for="form.blNumber" class="mt-1" />
            </div>
            <div class="col-span-6">
                <x-label for="currency"  value="Currency (*)" />
                <x-select
                    wire:model="form.currency"
                    id="currency"
                    class="mt-1 w-full text-sm">
                    <option></option>
                    @foreach ($form->setCurrencyOptions() as $currency)
                        <option class="text-sm" value="{{ $currency->id }}" {{ $currency->id == $form->currency ? ' selected' : '' }}>{{ $currency->name.' - '.$currency->description }}</option>
                    @endforeach
                </x-select>
                <x-input-error for="form.currency" class="mt-1" />
            </div>
            <div class="col-span-6">
                <x-label for="form.amount"  value="Amount(*)" />
                <x-input wire:model="form.amount" id="form.amount" type="text" class="mt-1 w-full"/>
                <x-input-error for="form.amount" class="mt-1" />
            </div>
            <div class="col-span-4">
                <x-label for="form.accountedDate"  value="Accounting Date(*)" />
                <x-input wire:model="form.accountedDate" id="form.accountedDate" type="date" class="mt-1 w-full"/>
                <x-input-error for="form.accountedDate" class="mt-1" />
            </div>
        </div>
    </x-slot>

    <x-slot name="footer">
        <x-secondary-button class="me-3" x-on:click="$dispatch('{{ $modal }}', { open: false })">Cancel</x-secondary-button>
        <x-button>Save</x-button>
    </x-slot>

</x-custom-modal>