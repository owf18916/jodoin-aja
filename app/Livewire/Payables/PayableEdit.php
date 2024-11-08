<?php

namespace App\Livewire\Payables;

use App\Models\Payable;
use Livewire\Component;
use App\Traits\Swalable;
use Livewire\Attributes\On;
use App\Livewire\Forms\PayableForm;
use Illuminate\Support\Facades\Gate;

class PayableEdit extends Component
{
    use Swalable;

    public PayableForm $form;

    public $modal = 'modal-edit-payable';

    #[On('edit-payable')]
    public function setPayableForm(Payable $payable)
    {
        $this->form->setPayable($payable);
        
        $this->dispatch('set-supplier-payable-edit',
            id: $this->form->supplier,
            data: ['data' => [$this->form->setSupplierOptions()] ]
        );

        $this->dispatch($this->modal, open: true);
    }

    public function getSuppliers($query)
    {
        return $this->form->setSupplierOptions($query);
    }

    public function save()
    {
        $this->validate();

        Gate::authorize('manage-payable');

        try {
            $this->form->update();
            $this->toastSuccess('data dokumen payable berhasil diupdate.');
            $this->dispatch('payable-updated')->to(PayableTable::class);
            $this->dispatch('set-reset');
            $this->form->reset();
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }
    
    public function render()
    {
        return view('livewire.payables.payable-edit');
    }
}
