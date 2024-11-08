<?php

namespace App\Livewire\Payables;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Livewire\Forms\PayableFilterForm;

class PayableFilter extends Component
{
    public PayableFilterForm $form;

    public $modal = 'modal-filter-payable';

    #[On('filter-payable')]
    public function setFilterPayable()
    {
        $this->dispatch('set-status-options', data: $this->form->setStatusFilterOptions());
        $this->dispatch('set-bank-options', data: $this->form->setBankFilterOptions());
        $this->dispatch($this->modal, open: true);
    }

    public function getSuppliers($query)
    {
        return $this->form->setSupplierFilterOptions($query);
    }

    public function filter()
    {
        $this->dispatch('payable-filtered', form: $this->form);
        $this->dispatch($this->modal, open: false);
    }

    public function updateFilter()
    {
        $this->dispatch('payable-filtered', form: $this->form);
    }

    public function resetInvoiceDate()
    {
        $this->form->invoiceStartDate = null;
        $this->form->invoiceEndDate = null;
        $this->updateFilter();
    }

    public function resetPaymentDate()
    {
        $this->form->paymentStartDate = null;
        $this->form->paymentEndDate = null;
        $this->updateFilter();
    }


    public function render()
    {
        return view('livewire.payables.payable-filter');
    }
}
