<?php

namespace App\Livewire\Customers;

use Livewire\Component;
use App\Models\Customer;
use App\Traits\Swalable;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Gate;

class CustomerCreate extends Component
{
    use Swalable;

    #[Validate('required|unique:customers,name')]
    public $name;

    public $modal = 'modal-create-customer';

    #[On('create-customer')]
    public function setCustomerForm()
    {
        $this->dispatch($this->modal, open: true);
    }

    public function save()
    {
        Gate::authorize('manage-customer');

        try {
            Customer::create(['name' => $this->name]);

            $this->toastSuccess('data customer berhasil ditambahkan.');
            $this->dispatch('customer-updated')->to(CustomerTable::class);
            $this->dispatch('set-reset');
            $this->reset();
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }
    
    public function render()
    {
        return view('livewire.customers.customer-create');
    }
}
