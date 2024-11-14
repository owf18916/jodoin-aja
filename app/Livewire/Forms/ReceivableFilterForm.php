<?php

namespace App\Livewire\Forms;

use App\Services\ReceivableServices;
use Livewire\Form;

class ReceivableFilterForm extends Form
{
    public $customer = [], $accountedStartDate, $accountedEndDate, $status = [];

    public function setCustomerFilterOptions($query = null)
    {
        return (new ReceivableServices())->getCustomerOptions($query);
    }

    public function setStatusFilterOptions()
    {
        return (new ReceivableServices())->getStatusOptions();
    }
}
