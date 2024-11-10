<?php

namespace App\Livewire\Customers;

use Livewire\Attributes\Title;
use Livewire\Component;

class CustomerIndex extends Component
{
    #[Title('Master Customer')]
    public function render()
    {
        return view('livewire.customers.customer-index');
    }
}
