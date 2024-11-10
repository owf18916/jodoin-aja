<div class="max-w-[1920px] mx-auto">
    <div class="relative flex flex-col w-full h-full text-slate-700 bg-white shadow-md rounded-xl bg-clip-border">
        <div class="relative mx-4 mt-4 overflow-hidden text-slate-700 bg-white rounded-none bg-clip-border">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-slate-800">Master Currency</h3>
                </div>
                <div class="flex flex-col gap-2 shrink-0 sm:flex-row">
                    <x-search model="search" />
                    <x-select wire:model.live="paginate" id="pagination" class="mb-2 rounded-lg text-sm cursor-pointer">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </x-select>
                    <button
                        x-on:click="$dispatch('currency-refresh')"
                        class="flex items-center h-10 rounded-md border border-slate-300 py-2 px-4 text-center text-sm transition-all shadow-sm hover:shadow-lg text-slate-600 hover:text-white hover:bg-slate-800 hover:border-slate-800 focus:text-white focus:bg-slate-800 focus:border-slate-800 active:border-slate-800 active:text-white active:bg-slate-800 disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                        type="button">
                        <x-icons type="refresh" />
                    </button>
                    @can('manage-currency')                        
                    <button
                        x-on:click="$dispatch('create-currency')"
                        class="flex items-center h-10 rounded-md border border-slate-300 py-2 px-4 text-center text-sm transition-all shadow-sm hover:shadow-lg text-slate-600 hover:text-white hover:bg-slate-800 hover:border-slate-800 focus:text-white focus:bg-slate-800 focus:border-slate-800 active:border-slate-800 active:text-white active:bg-slate-800 disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                        type="button">
                        <svg  xmlns="http://www.w3.org/2000/svg"  width="16"  height="16"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                    </button>
                    @endcan

                    <livewire:currencies.currency-export />
                </div>
            </div>
        </div>
        <div class="p-0 overflow-scroll">
            <table class="w-full mt-4 text-left table-auto min-w-max">
                <thead>
                    <tr>
                        <th
                            class="p-4 transition-colors cursor-pointer border-y border-slate-200 bg-slate-50 hover:bg-slate-100">
                            <p
                                class="flex items-center gap-2 font-sans text-sm font-normal leading-none text-slate-500">
                                #
                            </p>
                        </th>
                        <th
							x-on:click="$wire.sortField('currency_id')"
                            class="p-4 transition-colors cursor-pointer border-y border-slate-200 bg-slate-50 hover:bg-slate-100">
                            <p
                                class="flex items-center justify-between gap-2 font-sans text-sm font-normal leading-none text-slate-500">
                                Nama Currency
                                <x-sort :$sortDirection :$sortBy field="currency_id" />
                            </p>
                        </th>
                        <th
							x-on:click="$wire.sortField('currency_id')"
                            class="p-4 transition-colors cursor-pointer border-y border-slate-200 bg-slate-50 hover:bg-slate-100">
                            <p
                                class="flex items-center justify-between gap-2 font-sans text-sm font-normal leading-none text-slate-500">
                                Deskripsi
                                <x-sort :$sortDirection :$sortBy field="currency_id" />
                            </p>
                        </th>
                        <th
							x-on:click="$wire.sortField('currency_id')"
                            class="p-4 transition-colors cursor-pointer border-y border-slate-200 bg-slate-50 hover:bg-slate-100">
                            <p
                                class="flex items-center justify-between gap-2 font-sans text-sm font-normal leading-none text-slate-500">
                                Slug
                                <x-sort :$sortDirection :$sortBy field="currency_id" />
                            </p>
                        </th>
						<th
							class="p-4 transition-colors cursor-pointer border-y border-slate-200 bg-slate-50 hover:bg-slate-100">
							<p
							class="flex items-center justify-between gap-2 font-sans text-sm  font-normal leading-none text-slate-500">
							</p>
						</th>
                    </tr>
                </thead>
                <tbody>
					@forelse ($currencys as $currency)						
                    <tr>
                        <td class="p-4 border-b border-slate-200">
                            {{ (($currencys->currentPage() - 1) * $currencys->perPage()) + $loop->iteration }}
                        </td>
                        <td class="p-4 border-b border-slate-200">
							<p class="text-sm text-slate-500">
								{{ $currency->name }}
                            </p>
                        </td>
                        <td class="p-4 border-b border-slate-200">
							<p class="text-sm text-slate-500">
								{{ $currency->description }}
                            </p>
                        </td>
                        <td class="p-4 border-b border-slate-200">
							<p class="text-sm text-slate-500">
								{{ $currency->slug }}
                            </p>
                        </td>
                        <td class="p-4 border-b border-slate-200">
                            @can('manage-currency')
                                <x-plain-button
                                    x-on:click="$dispatch('edit-currency', { currency: {{ $currency->id }}})">
                                    <x-icons type="edit-alt" />
                                </x-plain-button>
                            @endcan
                        </td>
                    </tr>
					@empty
					<tr>
						<td class="px-6 py-4 text-sm text-gray-500 text-center" colspan="9">
							Tidak ada data.
						</td>
					</tr>	
					@endforelse
                </tbody>
            </table>
            <div class="p-4">
                {{ $currencys->onEachSide(2)->links() }}
            </div>
        </div>
    </div>
</div>
