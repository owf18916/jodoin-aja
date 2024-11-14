<?php

namespace App\Livewire\Receivables;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Livewire\Forms\ReceivableFilterForm;

class ReceivableFilter extends Component
{
    public ReceivableFilterForm $form;

    public $modal = 'modal-filter-receivable';

    #[On('filter-receivable')]
    public function setFilterReceivable()
    {
        $this->dispatch('set-status-options', data: $this->form->setStatusFilterOptions());
        $this->dispatch($this->modal, open: true);
    }

    public function getCustomers($query)
    {
        return $this->form->setCustomerFilterOptions($query);
    }

    public function filter()
    {
        $this->dispatch('receivable-filtered', form: $this->form);
        $this->dispatch($this->modal, open: false);
    }

    public function updateFilter()
    {
        $this->dispatch('receivable-filtered', form: $this->form);
    }

    public function resetAccountedDate()
    {
        $this->form->accountedStartDate = null;
        $this->form->accountedEndDate = null;
        $this->updateFilter();
    }


    public function render()
    {
        return view('livewire.receivables.receivable-filter');
    }
}
