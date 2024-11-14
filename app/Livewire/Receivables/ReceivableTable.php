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
            1 => 'danger',
            2 => 'warning',
            3 => 'success',
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
        $this->form->accountedStartDate = $form['accountedStartDate'];
        $this->form->accountedStartDate = $form['accountedStartDate'];
    }

    #[On('receivable-updated')]
    public function render()
    {
        return view('livewire.receivables.receivable-table', [
            'receivables' => Receivable::with(['customer', 'currency'])
                ->search($this->search)
                ->filter($this->form)
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->paginate)
        ]);
    }
}
