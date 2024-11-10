<?php

namespace App\Livewire\Banks;

use Livewire\Attributes\Title;
use Livewire\Component;

class BankIndex extends Component
{
    #[Title('Master Bank')]
    public function render()
    {
        return view('livewire.banks.bank-index');
    }
}
