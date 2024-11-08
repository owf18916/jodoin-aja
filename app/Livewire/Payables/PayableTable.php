<?php

namespace App\Livewire\Payables;

use App\Models\Payable;
use Livewire\Component;
use App\Traits\Swalable;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Livewire\Forms\PayableFilterForm;

class PayableTable extends Component
{
    use WithPagination;

    use Swalable;

    public PayableFilterForm $form;

    public 
        $paginate = 15,
        $sortBy = 'created_at',
        $sortDirection = 'desc',
        $search,
        $statusColors = [
            1 => 'warning',
            2 => 'success',
        ];

    #[On('payable-refresh')]
    public function refreshTable()
    {
        $this->dispatch('payable-updated');
        $this->toastinfo('Tabel dokumen payable berhasil diperbaharui.');
    }

    #[On('payable-filtered')]
    public function setSelectedProperties($form)
    {
        $this->form->status = $form['status'];
        $this->form->supplier = $form['supplier'];
        $this->form->bank = $form['bank'];
        $this->form->invoiceStartDate = $form['invoiceStartDate'];
        $this->form->invoiceStartDate = $form['invoiceStartDate'];
        $this->form->paymentStartDate = $form['paymentStartDate'];
        $this->form->paymentStartDate = $form['paymentStartDate'];
    }

    #[On('payable-updated')]
    public function render()
    {
        return view('livewire.payables.payable-table', [
            'payables' => Payable::with(['supplier', 'bank', 'currency'])
                ->search($this->search)
                ->filter($this->form)
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->paginate)
        ]);
    }
}
