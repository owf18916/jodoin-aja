<?php

namespace App\Livewire\Banks;

use Livewire\Component;
use App\Models\Bank;
use App\Traits\Swalable;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Gate;

class BankCreate extends Component
{
    use Swalable;

    #[Validate('required|unique:banks,name')]
    public $name;

    #[Validate('required|unique:banks,initial')]
    public $initial;

    public $modal = 'modal-create-bank';

    #[On('create-bank')]
    public function setBankForm()
    {
        $this->dispatch($this->modal, open: true);
    }

    public function save()
    {
        Gate::authorize('manage-bank');

        try {
            Bank::create([
                'name' => $this->name,
                'initial' => $this->initial,
            ]);

            $this->toastSuccess('data bank berhasil ditambahkan.');
            $this->dispatch('bank-updated')->to(BankTable::class);
            $this->dispatch('set-reset');
            $this->reset();
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }
    
    public function render()
    {
        return view('livewire.banks.bank-create');
    }
}
