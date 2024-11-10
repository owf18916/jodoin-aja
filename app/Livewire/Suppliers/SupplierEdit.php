<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use Livewire\Component;
use App\Traits\Swalable;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;

class SupplierEdit extends Component
{
    use Swalable;

    public $modal = 'modal-edit-supplier';

    
    public $statusOptions = [
        ['id' => 0 , 'label' => 'Non-aktif'],
        ['id' => 1 , 'label' => 'Aktif'],
    ];

    public ?Supplier $supplier;

    #[Locked()]
    public $id;

    #[Validate('required')]
    public $name;
    
    #[Validate('required')]
    public $status;

    #[On('edit-supplier')]
    public function setSupplierForm(Supplier $supplier)
    {
        $this->supplier = $supplier;
        $this->name = $supplier->name;
        $this->status = $supplier->status;
        $this->id = $supplier->id;

        $this->dispatch($this->modal, open: true);
    }

    public function save()
    {
        $this->validate();

        Gate::authorize('manage-supplier');

        try {
            $this->supplier->name = $this->name;
            $this->supplier->status = $this->status;
            $this->supplier->save();

            $this->toastSuccess('data supplier berhasil diupdate.');
            $this->dispatch('supplier-updated')->to(SupplierTable::class);
            $this->dispatch('set-reset');
            $this->reset();
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }
    
    public function render()
    {
        return view('livewire.suppliers.supplier-edit');
    }
}
