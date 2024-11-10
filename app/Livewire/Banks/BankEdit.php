<?php

namespace App\Livewire\Banks;

use App\Models\Bank;
use Livewire\Component;
use App\Traits\Swalable;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;

class BankEdit extends Component
{
    use Swalable;

    public $modal = 'modal-edit-bank';

    
    public $statusOptions = [
        ['id' => 0 , 'label' => 'Non-aktif'],
        ['id' => 1 , 'label' => 'Aktif'],
    ];

    public ?Bank $bank;

    #[Locked()]
    public $id;

    #[Validate('required')]
    public $name;

    #[Validate('required')]
    public $initial;
    
    #[Validate('required')]
    public $status;

    #[On('edit-bank')]
    public function setBankForm(Bank $bank)
    {
        $this->bank = $bank;
        $this->initial = $bank->initial;
        $this->name = $bank->name;
        $this->status = $bank->status;
        $this->id = $bank->id;

        $this->dispatch($this->modal, open: true);
    }

    public function save()
    {
        $this->validate();

        Gate::authorize('manage-bank');

        try {
            $this->bank->name = $this->name;
            $this->bank->initial = $this->initial;
            $this->bank->status = $this->status;
            $this->bank->save();

            $this->toastSuccess('data bank berhasil diupdate.');
            $this->dispatch('bank-updated')->to(BankTable::class);
            $this->dispatch('set-reset');
            $this->reset();
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }
    
    public function render()
    {
        return view('livewire.banks.bank-edit');
    }
}
