<?php

namespace App\Livewire\Receivables;

use App\Models\Receivable;
use Livewire\Component;
use App\Traits\Swalable;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Livewire\Forms\ReceivableFilterForm;

class ReceivableTable extends Component
{
    use WithPagination;

    use Swalable;

    public ReceivableFilterForm $form;

    public 
        $paginate = 15,
        $sortBy = 'created_at',
        $sortDirection = 'desc',
        $search,
        $statusColors = [
            1 => 'warning',
            2 => 'success',
        ];

    #[On('receivable-refresh')]
    public function refreshTable()
    {
        $this->dispatch('receivable-updated');
        $this->toastinfo('Tabel dokumen receivable berhasil diperbaharui.');
    }

    #[On('receivable-filtered')]
    public function setSelectedProperties($form)
    {
        $this->form->status = $form['status'];
        $this->form->customer = $form['customer'];
        $this->form->bank = $form['bank'];
        $this->form->invoiceStartDate = $form['invoiceStartDate'];
        $this->form->invoiceStartDate = $form['invoiceStartDate'];
        $this->form->receiptStartDate = $form['receiptStartDate'];
        $this->form->receiptStartDate = $form['receiptStartDate'];
    }

    #[On('receivable-updated')]
    public function render()
    {
        return view('livewire.receivables.receivable-table', [
            'receivables' => Receivable::with(['customer', 'bank', 'currency'])
                ->search($this->search)
                ->filter($this->form)
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->paginate)
        ]);
    }
}
