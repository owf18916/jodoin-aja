<?php

namespace App\Livewire\Receivables;

use App\Models\Receivable;
use Livewire\Component;
use App\Traits\Swalable;
use Livewire\Attributes\On;
use App\Livewire\Forms\ReceivableForm;
use Illuminate\Support\Facades\Gate;

class ReceivableEdit extends Component
{
    use Swalable;

    public ReceivableForm $form;

    public $modal = 'modal-edit-receivable';

    #[On('edit-receivable')]
    public function setReceivableForm(Receivable $receivable)
    {
        $this->form->setReceivable($receivable);
        
        $this->dispatch('set-customer-receivable-edit',
            id: $this->form->customer,
            data: ['data' => [$this->form->setCustomerOptions()] ]
        );

        $this->dispatch($this->modal, open: true);
    }

    public function getCustomers($query)
    {
        return $this->form->setCustomerOptions($query);
    }

    public function save()
    {
        $this->validate();

        Gate::authorize('manage-receivable');

        try {
            $this->form->update();
            $this->toastSuccess('data dokumen receivable berhasil diupdate.');
            $this->dispatch('receivable-updated')->to(ReceivableTable::class);
            $this->dispatch('set-reset');
            $this->form->reset();
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }
    
    public function render()
    {
        return view('livewire.receivables.receivable-edit');
    }
}
