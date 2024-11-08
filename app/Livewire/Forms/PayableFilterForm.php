<?php

namespace App\Livewire\Forms;

use App\Models\Bank;
use App\Services\PayableServices;
use Livewire\Form;

class PayableFilterForm extends Form
{
    public $bank = [] ,$supplier = [], $invoiceStartDate, $invoiceEndDate, $paymentStartDate, $paymentEndDate, $status = [];

    public function setBankFilterOptions(): array
    {
        return Bank::select('id','name')->get()->toArray();
    }

    public function setSupplierFilterOptions($query = null)
    {
        return (new PayableServices())->getSupplierOptions($query);
    }

    public function setStatusFilterOptions()
    {
        return (new PayableServices())->getStatusOptions();
    }
}
