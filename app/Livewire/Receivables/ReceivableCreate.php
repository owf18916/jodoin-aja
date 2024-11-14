<?php

namespace App\Livewire\Receivables;

use Livewire\Component;
use App\Traits\Swalable;
use Livewire\Attributes\On;
use App\Livewire\Forms\ReceivableForm;
use Illuminate\Support\Facades\Gate;

class ReceivableCreate extends Component
{
    use Swalable;

    public ReceivableForm $form;

    public $modal = 'modal-create-receivable';

    #[On('create-receivable')]
    public function setReceivableForm()
    {
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
            $this->form->store();
            $this->toastSuccess('data dokumen receivable berhasil ditambahkan.');
            $this->dispatch('receivable-updated')->to(ReceivableTable::class);
            $this->dispatch('set-reset');
            $this->form->reset();
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }
    
    public function render()
    {
        return view('livewire.receivables.receivable-create');
    }
}
