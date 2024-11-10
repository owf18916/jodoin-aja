<?php

namespace App\Livewire\Suppliers;

use Livewire\Component;
use App\Models\Supplier;
use App\Traits\Swalable;
use Livewire\Attributes\On;
use Livewire\WithPagination;

class SupplierTable extends Component
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

    #[On('supplier-refresh')]
    public function refreshTable()
    {
        $this->dispatch('supplier-updated');
        $this->toastinfo('Tabel supplier berhasil diperbaharui.');
    }

    #[On('supplier-updated')]
    public function render()
    {
        return view('livewire.suppliers.supplier-table', [
            'suppliers' => Supplier::orderBy($this->sortBy, $this->sortDirection)->paginate($this->paginate)
        ]);
    }
}
