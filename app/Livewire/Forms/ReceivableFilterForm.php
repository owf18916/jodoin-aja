<?php

namespace App\Livewire\Forms;

use App\Models\Bank;
use App\Services\ReceivableServices;
use Livewire\Form;

class ReceivableFilterForm extends Form
{
    public $bank = [] ,$customer = [], $invoiceStartDate, $invoiceEndDate, $receiptStartDate, $receiptEndDate, $status = [];

    public function setBankFilterOptions(): array
    {
        return Bank::select('id','name')->get()->toArray();
    }

    public function setCustomerFilterOptions($query = null)
    {
        return (new ReceivableServices())->getCustomerOptions($query);
    }

    public function setStatusFilterOptions()
    {
        return (new ReceivableServices())->getStatusOptions();
    }
}
