<?php

namespace App\Livewire\Banks;

use Livewire\Component;
use App\Traits\Swalable;
use Maatwebsite\Excel\Facades\Excel;

class BankExport extends Component
{
    use Swalable;

    public $isLoading = false;

    public function export()
    {

        return Excel::download(new \App\Exports\BankExport(), 'bank-export.xlsx');
    }
    
    public function render()
    {
        return <<<'HTML'
        
        <button
            wire:click="export"
            wire:loading.attr="disabled"
            wire:target="export"
            class="flex items-center h-10 rounded-md border border-slate-300 py-2 px-4 text-center text-sm transition-all shadow-sm hover:shadow-lg text-slate-600 hover:text-white hover:bg-slate-800 hover:border-slate-800 focus:text-white focus:bg-slate-800 focus:border-slate-800 active:border-slate-800 active:text-white active:bg-slate-800 disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
            type="button">
            <div wire:loading.remove wire:target="export">
                <svg  xmlns="http://www.w3.org/2000/svg"  width="16"  height="16"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-file-spreadsheet"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M8 11h8v7h-8z" /><path d="M8 15h8" /><path d="M11 11v7" /></svg>
            </div>
            <div wire:loading wire:target="export">
                <svg class="animate-spin w-4 h-4 text-white dark:text-gray-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0116 0h-4a4 4 0 00-8 0H4z"></path>
                </svg>
            </div>
        </button>
        HTML;
    }
}
