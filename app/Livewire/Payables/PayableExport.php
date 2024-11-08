<?php

namespace App\Livewire\Payables;

use stdClass;
use Carbon\Carbon;
use Livewire\Component;
use App\Traits\Swalable;
use Livewire\Attributes\On;
use Maatwebsite\Excel\Facades\Excel;

class PayableExport extends Component
{
    use Swalable;

    public $form;
    public $isLoading = false;

    public function mount()
    {
        $this->form = new stdClass();
        $this->form->status = [];
        $this->form->supplier = [];
        $this->form->bank = [];
        $this->form->invoiceStartDate = null;
        $this->form->invoiceEndDate = null;
        $this->form->paymentStartDate = null;
        $this->form->paymentEndDate = null;
    }

    #[On('payable-filtered')]
    public function setSelectedProperties($form)
    {
        $this->form->status = $form['status'];
        $this->form->supplier = $form['supplier'];
        $this->form->bank = $form['bank'];
        $this->form->invoiceStartDate = $form['invoiceStartDate'];
        $this->form->invoiceEndDate = $form['invoiceEndDate'];
        $this->form->paymentStartDate = $form['paymentStartDate'];
        $this->form->paymentEndDate = $form['paymentEndDate'];
    }

    public function export()
    {
        if(empty(($this->form->invoiceStartDate) || empty($this->form->invoiceEndDate)) || empty(($this->form->paymentStartDate) || empty($this->form->paymentEndDate))) {
            $this->flashError('Pilih range tanggal dulu dengan menggunakan filter.');

            return;
        }

        $invoiceStartDate = Carbon::parse($this->form->invoiceStartDate);
        $invoiceEndDate = Carbon::parse($this->form->invoiceEndDate);

        if($invoiceStartDate->diffInDays($invoiceEndDate) > 365) {
            $this->flashError('Range tanggal invoice maksimal 1 tahun (365 hari).');

            return;
        }

        $paymentStartDate = Carbon::parse($this->form->paymentStartDate);
        $paymentEndDate = Carbon::parse($this->form->paymentEndDate);

        if($paymentStartDate->diffInDays($paymentEndDate) > 365) {
            $this->flashError('Range tanggal payment maksimal 1 tahun (365 hari).');

            return;
        }

        return Excel::download(new \App\Exports\PayableExport($this->form), 'payable-export.xlsx');
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
