<?php

namespace App\Livewire\Currencies;

use Livewire\Attributes\Title;
use Livewire\Component;

class CurrencyIndex extends Component
{
    #[Title('Master Currency')]
    public function render()
    {
        return view('livewire.currencies.currency-index');
    }
}
