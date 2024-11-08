<div class="py-6">
    <div class="max-w-screen-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-slate-100 overflow-hidden shadow-xl sm:rounded-lg">
            <div class="flex flex-col mt-6">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="m-4 flex justify-between">
                            <div>
                            </div>
                            <x-search model="search" />
                        </div>
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg m-4">
                            <table class="min-w-full divide-y divide-gray-200" wire:poll.3s>
                                <thead class="bg-slate-200">
                                    <tr>
                                        <th scope="col" class="p-2 whitespace-nowrap border border-spacing-1 text-center">
                                            <x-select wire:model.live="paginate" class="mb-2 rounded-lg text-sm">
                                                <option value="5">5</option>
                                                <option value="10">10</option>
                                                <option value="15">15</option>
                                            </x-select>
                                        </th>
                                        <th
                                            class="p-2 whitespace-nowrap border border-spacing-1 text-center text-sm cursor-pointer">
                                            <span>User</span>
                                        </th>
                                        <th
                                            scope="col"
                                            class="p-2 whitespace-nowrap border border-spacing-1 text-center text-sm cursor-pointer">
                                            <span>Type</span>
                                        </th>
                                        <th
                                            scope="col"
                                            class="p-2 whitespace-nowrap border border-spacing-1 text-center text-sm cursor-pointer">
                                            <span>Job Name</span>
                                        </th>
                                        <th
                                            class="p-2 whitespace-nowrap border border-spacing-1 text-center text-sm cursor-pointer">
                                            <span>Start</span>
                                        </th>
                                        <th
                                            class="p-2 whitespace-nowrap border border-spacing-1 text-center text-sm cursor-pointer">
                                            <span>Finished</span>
                                        </th>
                                        <th
                                            class="p-2 whitespace-nowrap border border-spacing-1 text-center text-sm cursor-pointer">
                                            <span>Status</span>
                                        </th>
                                        <th
                                            scope="col"
                                            class="p-2 whitespace-nowrap border border-spacing-1 text-center text-sm cursor-pointer">
                                            <span>File</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-gray-50 divide-y divide-gray-200">
                                    @forelse ($activities as $activity)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                            {{ (($activities->currentPage() - 1) * $activities->perPage()) + $loop->iteration }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                            {{ $activity->user->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                            {{ $activity->type_label }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                            {{ $activity->job_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                            <x-soft-badge type="secondary">{{ $activity->started_at }}</x-soft-badge>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                            @if (in_array($activity->status,[0,3,4]))
                                            <x-soft-badge type="success">{{ $activity->finished_at }}</x-soft-badge>
                                            @else
                                            -
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                            <x-badge type="{{ $statusColors[$activity->status] }}">{{ $activity->status_label }}</x-badge>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                            @if (in_array($activity->status,[3,4]) && $activity->file)
                                            <a href="{{ $activity->file }}">
                                                <x-button color="green" type="button" class="cursor-pointer">
                                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="16"  height="16"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 11l5 5l5 -5" /><path d="M12 4l0 12" /></svg>
                                                </x-button>
                                            </a>
                                            @else
                                            -
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td class="px-6 py-4 text-sm text-gray-500 text-center" colspan="11">
                                            Tidak ada data.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            <div class="p-4">
                                {{ $activities->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

