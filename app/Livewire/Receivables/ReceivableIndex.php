<?php

namespace App\Livewire\Receivables;

use Livewire\Attributes\Title;
use Livewire\Component;

class ReceivableIndex extends Component
{
    #[Title('Receivable Document')]
    public function render()
    {
        return view('livewire.receivables.receivable-index');
    }
}
