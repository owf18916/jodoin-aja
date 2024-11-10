<?php

namespace App\Livewire\Currencies;

use Livewire\Component;
use App\Models\Currency;
use App\Traits\Swalable;
use Livewire\Attributes\On;
use Livewire\WithPagination;

class CurrencyTable extends Component
{
    use WithPagination;

    use Swalable;

    public 
        $paginate = 15,
        $sortBy = 'created_at',
        $sortDirection = 'desc',
        $search,
        $statusColors = [
            0 => 'warning',
            1 => 'success',
        ];

    #[On('currency-refresh')]
    public function refreshTable()
    {
        $this->dispatch('currency-updated');
        $this->toastinfo('Tabel currency berhasil diperbaharui.');
    }

    #[On('currency-updated')]
    public function render()
    {
        return view('livewire.currencies.currency-table', [
            'currencys' => Currency::orderBy($this->sortBy, $this->sortDirection)->paginate($this->paginate)
        ]);
    }
}
