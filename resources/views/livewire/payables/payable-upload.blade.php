<div
    x-data="{open: false}"
    x-show="open"
    x-transition
    x-cloak
    x-on:payable-upload-clicked.window="open = !open"
    class="mt-4 inline-block">
    <div
        x-data="{ uploading: false, progress: 0 }"
        x-on:livewire-upload-start="uploading = true"
        x-on:livewire-upload-finish="uploading = false; progress = 0;"
        x-on:livewire-upload-progress="progress = $event.detail.progress">
        <h4 class="mt-2 mx-4 font-semibold text-slate-600">Upload Payable Form</h4>
        <form wire:submit="save">
            <div class="inline-flex mt-2 mx-4 rounded-md shadow-lg p-4">
                <input
                    type="file"
                    wire:model="form.file"
                    id="file"
                    class="mt-2 block w-full text-sm file:mr-4 file:rounded-md file:border-0 file:bg-gray-800 file:py-2 file:px-4 file:text-sm file:font-semibold file:text-white hover:file:bg-gray-900 focus:outline-none disabled:pointer-events-none disabled:opacity-60"
                />

                <x-button
                    x-cloak
                    x-bind:disabled="uploading"
                    type="submit">
                    <svg 
                        wire:loading
                        wire:target="save"
                        class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Proses
                </x-button>
            </div>

            <x-button
                wire:click="importForm"
                wire:loading.attr="disabled"
                wire:target="importForm"
                type="button"
                class="inline-block mt-4 mx-2"
                color="blue">
                <div
                    wire:loading
                    wire:target="importForm"
                    class="flex">
                    <svg
                        class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Proses...</span>
                </div>
                <div
                    wire:loading.remove
                    wire:target="importForm"
                    class="flex">
                    <svg xmlns="http://www.w3.org/2000/svg"  width="16"  height="16"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round" class="me-2">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                        <path d="M7 11l5 5l5 -5" />
                        <path d="M12 4l0 12" />
                    </svg> <span>Upload Form</span>
                </div>
            </x-button>

            <div 
                x-cloak
                x-show="uploading"
                class="my-4 mx-4">
                <div class="h-4 w-full bg-slate-100 rounded-lg shadow-inner">
                    <div class="bg-green-500 h-4 rounded-lg" :style="{ width: `${progress}%` }"></div>
                </div>
            </div>
        </form>

        @error('form.file')
            <x-input-error class="px-4 py-2" for="form.file">{{ $message }}</x-input-error>
        @enderror
    </div>
</div>
