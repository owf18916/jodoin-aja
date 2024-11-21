<div class="max-w-[1920px] mx-auto">
    <div class="relative flex flex-col w-full h-full text-slate-700 bg-white shadow-md rounded-xl bg-clip-border">
        <div class="relative mx-4 mt-4 overflow-hidden text-slate-700 bg-white rounded-none bg-clip-border">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-slate-800">Daftar Dokumen Receivable</h3>
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
                        x-on:click="$dispatch('receivable-refresh')"
                        class="flex items-center h-10 rounded-md border border-slate-300 py-2 px-4 text-center text-sm transition-all shadow-sm hover:shadow-lg text-slate-600 hover:text-white hover:bg-slate-800 hover:border-slate-800 focus:text-white focus:bg-slate-800 focus:border-slate-800 active:border-slate-800 active:text-white active:bg-slate-800 disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                        type="button">
                        <x-icons type="refresh" />
                    </button>
                    @can('manage-receivable')                        
                    <button
                        x-on:click="$dispatch('create-receivable')"
                        class="flex items-center h-10 rounded-md border border-slate-300 py-2 px-4 text-center text-sm transition-all shadow-sm hover:shadow-lg text-slate-600 hover:text-white hover:bg-slate-800 hover:border-slate-800 focus:text-white focus:bg-slate-800 focus:border-slate-800 active:border-slate-800 active:text-white active:bg-slate-800 disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                        type="button">
                        <svg  xmlns="http://www.w3.org/2000/svg"  width="16"  height="16"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                    </button>
                    <button
                        x-on:click="$dispatch('receivable-upload-clicked')"
                        class="flex items-center h-10 rounded-md border border-slate-300 py-2 px-4 text-center text-sm transition-all shadow-sm hover:shadow-lg text-slate-600 hover:text-white hover:bg-slate-800 hover:border-slate-800 focus:text-white focus:bg-slate-800 focus:border-slate-800 active:border-slate-800 active:text-white active:bg-slate-800 disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                        type="button">
                        <x-icons type="upload" />
                    </button>
                    <livewire:receivables.receivable-match />
                    <livewire:receivables.receivable-match-data />
                    @endcan

                    <button
                        x-on:click="$dispatch('filter-receivable')"
                        class="flex items-center h-10 rounded-md border border-slate-300 py-2 px-4 text-center text-sm transition-all shadow-sm hover:shadow-lg text-slate-600 hover:text-white hover:bg-slate-800 hover:border-slate-800 focus:text-white focus:bg-slate-800 focus:border-slate-800 active:border-slate-800 active:text-white active:bg-slate-800 disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                        type="button">
                        <svg  xmlns="http://www.w3.org/2000/svg"  width="16"  height="16"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 20l-3 1v-8.5l-4.48 -4.928a2 2 0 0 1 -.52 -1.345v-2.227h16v2.172a2 2 0 0 1 -.586 1.414l-4.414 4.414v3" /><path d="M16 19h6" /><path d="M19 16v6" /></svg>
                    </button>
                    <button
                        x-on:click="$dispatch('receivable-batch-download-clicked')"
                        class="flex items-center h-10 rounded-md border border-slate-300 py-2 px-4 text-center text-sm transition-all shadow-sm hover:shadow-lg text-slate-600 hover:text-white hover:bg-slate-800 hover:border-slate-800 focus:text-white focus:bg-slate-800 focus:border-slate-800 active:border-slate-800 active:text-white active:bg-slate-800 disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                        type="button">
                        <svg  xmlns="http://www.w3.org/2000/svg"  width="16"  height="16"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-folder-search"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M11 19h-6a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 2 -2h4l3 3h7a2 2 0 0 1 2 2v2.5" /><path d="M18 18m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /><path d="M20.2 20.2l1.8 1.8" /></svg>
                    </button>
                    <livewire:receivables.receivable-export />
                </div>
            </div>
            <livewire:receivables.receivable-upload />
            <livewire:receivables.receivable-batch-download-document />
        </div>
        <div class="p-0 overflow-scroll">
            <table class="w-full mt-4 text-left table-auto min-w-max">
                <thead>
                    <tr>
                        <th
                            rowspan="2"
                            class="p-4 transition-colors cursor-pointer border-y border-slate-200 bg-slate-50 hover:bg-slate-100">
                            <p
                                class="flex items-center gap-2 font-sans text-sm font-normal leading-none text-slate-500">
                                #
                            </p>
                        </th>
                        <th
                            rowspan="2"
                            class="p-4 transition-colors cursor-pointer border-y border-slate-200 bg-slate-50 hover:bg-slate-100">
                            <p
                                class="flex items-center gap-2 font-sans text-sm font-normal leading-none text-slate-500">
                                Kategori
                            </p>
                        </th>
                        <th
                            rowspan="2"
							x-on:click="$wire.sortField('customer_id')"
                            class="p-4 transition-colors cursor-pointer border-y border-slate-200 bg-slate-50 hover:bg-slate-100">
                            <p
                                class="flex items-center justify-between gap-2 font-sans text-sm font-normal leading-none text-slate-500">
                                Nama Customer
                                <x-sort :$sortDirection :$sortBy field="customer_id" />
                            </p>
                        </th>
                        <th
                            rowspan="2"
							x-on:click="$wire.sortField('invoice_number')"
                            class="p-4 transition-colors cursor-pointer border-y border-slate-200 bg-slate-50 hover:bg-slate-100">
                            <p
                                class="flex items-center justify-between gap-2 font-sans text-sm font-normal leading-none text-slate-500">
                                Nomor Invoice | BL
                                <x-sort :$sortDirection :$sortBy field="invoice_number" />
                            </p>
                        </th>
                        <th
                            rowspan="2"
							x-on:click="$wire.sortField('accounted_date')"
                            class="p-4 transition-colors cursor-pointer border-y border-slate-200 bg-slate-50 hover:bg-slate-100">
                            <p
                                class="flex items-center justify-between gap-2 font-sans text-sm font-normal leading-none text-slate-500">
                                Tanggal Catat
                                <x-sort :$sortDirection :$sortBy field="accounted_date" />
                            </p>
                        </th>
                        <th
                            rowspan="2"
							x-on:click="$wire.sortField('invoice_date')"
                            class="p-4 transition-colors cursor-pointer border-y border-slate-200 bg-slate-50 hover:bg-slate-100">
                            <p
                                class="flex items-center gap-2 font-sans text-sm font-normal leading-none text-slate-500">
                                Amount
                            </p>
                        </th>
                        <th colspan="2" class="p-4 transition-colors cursor-pointer border-y border-slate-200 bg-slate-50 hover:bg-slate-100">
                            <p
                                class="text-center font-sans text-sm font-normal leading-none text-slate-500">
                                Documents
                            </p>
                        </th>
                        <th
                            rowspan="2"
							x-on:click="$wire.sortField('status')"
                            class="p-4 transition-colors cursor-pointer border-y border-slate-200 bg-slate-50 hover:bg-slate-100">
                            <p
                                class="flex items-center justify-between gap-2 font-sans text-sm  font-normal leading-none text-slate-500">
                                Status
                                <x-sort :$sortDirection :$sortBy field="status" />
                            </p>
                        </th>
						<th
                            rowspan="2"
							class="p-4 transition-colors cursor-pointer border-y border-slate-200 bg-slate-50 hover:bg-slate-100">
							<p
							class="flex items-center justify-between gap-2 font-sans text-sm  font-normal leading-none text-slate-500">
							</p>
						</th>
                    </tr>
                    <tr>
                        <th
							class="p-4 transition-colors cursor-pointer border-y border-slate-200 bg-slate-50 hover:bg-slate-100">
							<p
							class="flex items-center justify-between gap-2 font-sans text-sm  font-normal leading-none text-slate-500">
                                Invoice
							</p>
						</th>
                        <th
							class="p-4 transition-colors cursor-pointer border-y border-slate-200 bg-slate-50 hover:bg-slate-100">
							<p
							class="flex items-center justify-between gap-2 font-sans text-sm  font-normal leading-none text-slate-500">
                                BL
							</p>
						</th>
                    </tr>
                </thead>
                <tbody>
					@forelse ($receivables as $receivable)						
                    <tr>
                        <td class="p-4 border-b border-slate-200">
                            {{ (($receivables->currentPage() - 1) * $receivables->perPage()) + $loop->iteration }}
                        </td>
                        <td class="p-4 border-b border-slate-200">
                            <p class="text-sm text-slate-500">
								{{ $receivable->category_label }}
                            </p>
                        </td>
                        <td class="p-4 border-b border-slate-200">
							<p class="text-sm text-slate-500">
								{{ $receivable->customer->name }}
                            </p>
                        </td>
                        <td class="p-4 border-b border-slate-200">
                            <div class="flex flex-col">
                                <p class="text-sm font-semibold text-slate-700">
                                    {{ $receivable->invoice_number }}
                                </p>
                                <p class="text-sm text-slate-500">
                                    {{ $receivable->bl_number }}
                                </p>
                            </div>
                        </td>
                        <td class="p-4 border-b border-slate-200">
							<p class="text-sm text-slate-500">
								{{ $receivable->accounted_date }}
                            </p>
                        </td>
                        <td class="p-4 border-b border-slate-200">
                            <div class="flex items-center gap-3">
                                <div class="flex flex-col">
                                    <p class="text-sm font-semibold text-slate-700">
                                        {{ $receivable->currency->name }}
                                    </p>
                                    <p class="text-sm text-slate-500">
                                        {{ number_format($receivable->amount,2) }}
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 border-b border-slate-200">
                            @if ($receivable->status_invoice == 2)
                                <x-plain-button
                                    download
                                    type="button"
                                    color="red"
                                    x-on:click="$dispatch('download-receivable-pdf-clicked', { receivable: {{ $receivable->id }}})">
                                    <x-icons type="pdf-alt" class="text-center" />
                                </x-plain-button>
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="p-4 border-b border-slate-200">
                            @if (in_array($receivable->category, [2, 3]))
                                -
                            @else
                                @if ($receivable->status_bl == 2)
                                <x-plain-button
                                    download
                                    type="button"
                                    color="red"
                                    x-on:click="$dispatch('download-receivable-bl-pdf-clicked', { receivable: {{ $receivable->id }}})">
                                    <x-icons type="pdf-alt" class="text-center" />
                                </x-plain-button>
                                @else
                                    N/A
                                @endif
                            @endif
                        </td>
                        <td class="p-4 border-b border-slate-200">
                            <p class="text-sm text-slate-500">
								<x-badge type="{{ $statusColors[$receivable->status] }}">{{ $receivable->status_label }}</x-badge>
                            </p>
                        </td>
                        <td class="p-4 border-b border-slate-200">
                            @can('manage-receivable')
                                <x-plain-button
                                    x-on:click="$dispatch('edit-receivable', { receivable: {{ $receivable->id }}})">
                                    <x-icons type="edit-alt" />
                                </x-plain-button>

                                <x-plain-button
                                    x-on:click="$dispatch('confirmation-fired', {
                                        eventName: `{{ 'receivable-deleted' }}`,
                                        rowId: {{ $receivable->id }},
                                        message: 'Apakah yakin hapus data ini ?'
                                    })">
                                    <x-icons type="delete" size="24" />
                                </x-plain-button>
                                @endif
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
                {{ $receivables->onEachSide(2)->links() }}
            </div>
        </div>
    </div>
</div>
