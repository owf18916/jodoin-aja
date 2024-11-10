<?php

namespace App\Livewire\Customers;

use Livewire\Component;
use App\Models\Customer;
use App\Traits\Swalable;
use Livewire\Attributes\On;
use Livewire\WithPagination;

class CustomerTable extends Component
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

    #[On('customer-refresh')]
    public function refreshTable()
    {
        $this->dispatch('customer-updated');
        $this->toastinfo('Tabel customer berhasil diperbaharui.');
    }

    #[On('customer-updated')]
    public function render()
    {
        return view('livewire.customers.customer-table', [
            'customers' => Customer::orderBy($this->sortBy, $this->sortDirection)->paginate($this->paginate)
        ]);
    }
}
