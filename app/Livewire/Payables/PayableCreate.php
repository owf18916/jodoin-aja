<?php

namespace App\Livewire\Payables;

use Livewire\Component;
use App\Traits\Swalable;
use Livewire\Attributes\On;
use App\Livewire\Forms\PayableForm;
use Illuminate\Support\Facades\Gate;

class PayableCreate extends Component
{
    use Swalable;

    public PayableForm $form;

    public $modal = 'modal-create-payable';

    #[On('create-payable')]
    public function setPayableForm()
    {
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
            $this->form->store();
            $this->toastSuccess('data dokumen payable berhasil ditambahkan.');
            $this->dispatch('payable-updated')->to(PayableTable::class);
            $this->dispatch('set-reset');
            $this->form->reset();
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }
    
    public function render()
    {
        return view('livewire.payables.payable-create');
    }
}
