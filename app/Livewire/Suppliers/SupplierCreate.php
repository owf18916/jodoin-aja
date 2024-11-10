<?php

namespace App\Livewire\Suppliers;

use Livewire\Component;
use App\Models\Supplier;
use App\Traits\Swalable;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Gate;

class SupplierCreate extends Component
{
    use Swalable;

    #[Validate('required|unique:suppliers,name')]
    public $name;

    public $modal = 'modal-create-supplier';

    #[On('create-supplier')]
    public function setSupplierForm()
    {
        $this->dispatch($this->modal, open: true);
    }

    public function save()
    {
        Gate::authorize('manage-supplier');

        try {
            Supplier::create(['name' => $this->name]);

            $this->toastSuccess('data supplier berhasil ditambahkan.');
            $this->dispatch('supplier-updated')->to(SupplierTable::class);
            $this->dispatch('set-reset');
            $this->reset();
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }
    
    public function render()
    {
        return view('livewire.suppliers.supplier-create');
    }
}
