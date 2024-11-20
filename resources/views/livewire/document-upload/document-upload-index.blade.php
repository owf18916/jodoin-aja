<x-custom-modal maxWidth="2xl" modal="{{ $modal }}" submit="save">
    <x-slot name="title">
        <div class="flex flex-col">
            <span>
                Upload Dokumen
            </span>
        </div>
    </x-slot>

    <x-slot name="content">
        <div class="p-6 bg-white rounded-lg shadow-md space-y-4">
            @if (session()->has('error'))
                <div class="p-2 bg-red-500 text-white rounded-md">{{ session('error') }}</div>
            @endif
    
            @if (session()->has('success'))
                <div class="p-2 bg-green-500 text-white rounded-md">{{ session('success') }}</div>
            @endif

            <!-- Input untuk upload file -->
            <div class="flex flex-col mb-4">
                <label class="block text-sm font-medium text-gray-700">Pilih Kategori File:</label>
                <x-select wire:model.live="category">
                    <option value="payable">Payable Document</option>
                    <option value="receivable">Receivable Document</option>
                    <option value="bl">BL Document</option>
                </x-select>
                <x-input-error for="category" class="mt-1" />
            </div>
    
            <!-- Input untuk upload file -->
            <div class="flex flex-col">
                <label class="block text-sm font-medium text-gray-700">Pilih PDF Files:</label>
                <input type="file" wire:model="files" multiple accept=".pdf" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <x-input-error for="files" class="mt-1" />
            </div>

            <!-- Tabel untuk file yang lolos validasi -->
            @if (count($validFiles) > 0)
                <div class="mt-4">
                    <h3 class="text-sm font-medium text-gray-700">File yang Lolos Validasi:</h3>
                    <table class="min-w-full divide-y divide-gray-200 mt-2">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Kategori
                                </th>
                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nama File
                                </th>
                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($validDocuments as $document)
                                <tr>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                        {{ $document['category'] }}
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                        {{ $document['file_name'] }}
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-green-500">
                                        Valid
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
    
            <!-- Validasi per file yang gagal -->
            @if (!empty($invalidFiles))
                <div class="mt-4">
                    <h3 class="text-sm font-medium text-red-500">File yang Tidak Valid:</h3>
                    @foreach ($invalidFiles as $error)
                        <div class="text-sm text-red-500">{{ $error }}</div>
                    @endforeach
                </div>
            @endif
    
            <!-- Loading indicator saat upload -->
            <div wire:loading wire:target="files" class="flex items-center">
                <svg class="animate-spin h-5 w-5 text-blue-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span class="text-blue-500">Uploading...</span>
            </div>
        </div>        
    </x-slot>

    <x-slot name="footer">
        <x-secondary-button class="me-3" x-on:click="$dispatch('{{ $modal }}', { open: false })">Cancel</x-secondary-button>
        <x-button>Save</x-button>
    </x-slot>
</x-custom-modal>