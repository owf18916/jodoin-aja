<?php

namespace App\Livewire\Banks;

use Livewire\Component;
use App\Models\Bank;
use App\Traits\Swalable;
use Livewire\Attributes\On;
use Livewire\WithPagination;

class BankTable extends Component
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

    #[On('bank-refresh')]
    public function refreshTable()
    {
        $this->dispatch('bank-updated');
        $this->toastinfo('Tabel bank berhasil diperbaharui.');
    }

    #[On('bank-updated')]
    public function render()
    {
        return view('livewire.banks.bank-table', [
            'banks' => Bank::orderBy($this->sortBy, $this->sortDirection)->paginate($this->paginate)
        ]);
    }
}
