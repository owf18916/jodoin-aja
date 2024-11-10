<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use Livewire\Component;
use App\Traits\Swalable;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;

class CustomerEdit extends Component
{
    use Swalable;

    public $modal = 'modal-edit-customer';

    
    public $statusOptions = [
        ['id' => 0 , 'label' => 'Non-aktif'],
        ['id' => 1 , 'label' => 'Aktif'],
    ];

    public ?Customer $customer;

    #[Locked()]
    public $id;

    #[Validate('required')]
    public $name;
    
    #[Validate('required')]
    public $status;

    #[On('edit-customer')]
    public function setCustomerForm(Customer $customer)
    {
        $this->customer = $customer;
        $this->name = $customer->name;
        $this->status = $customer->status;
        $this->id = $customer->id;

        $this->dispatch($this->modal, open: true);
    }

    public function save()
    {
        $this->validate();

        Gate::authorize('manage-customer');

        try {
            $this->customer->name = $this->name;
            $this->customer->status = $this->status;
            $this->customer->save();

            $this->toastSuccess('data customer berhasil diupdate.');
            $this->dispatch('customer-updated')->to(CustomerTable::class);
            $this->dispatch('set-reset');
            $this->reset();
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }
    
    public function render()
    {
        return view('livewire.customers.customer-edit');
    }
}
