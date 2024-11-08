<?php

namespace App\Livewire\Payables;

use Livewire\Attributes\Title;
use Livewire\Component;

class PayableIndex extends Component
{
    #[Title('Payable Document')]
    public function render()
    {
        return view('livewire.payables.payable-index');
    }
}
