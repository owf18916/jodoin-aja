<?php

namespace App\Livewire\Suppliers;

use Livewire\Attributes\Title;
use Livewire\Component;

class SupplierIndex extends Component
{
    #[Title('Master Supplier')]
    public function render()
    {
        return view('livewire.suppliers.supplier-index');
    }
}
