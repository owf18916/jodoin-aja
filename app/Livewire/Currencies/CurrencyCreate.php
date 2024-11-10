<?php

namespace App\Livewire\Currencies;

use Livewire\Component;
use App\Models\Currency;
use App\Traits\Swalable;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Gate;

class CurrencyCreate extends Component
{
    use Swalable;

    #[Validate('required|unique:currencies,name')]
    public $name;

    #[Validate('required|unique:currencies,description')]
    public $description;

    #[Validate('required')]
    public $slug;

    public $modal = 'modal-create-currency';

    #[On('create-currency')]
    public function setCurrencyForm()
    {
        $this->dispatch($this->modal, open: true);
    }

    public function save()
    {
        Gate::authorize('manage-currency');

        try {
            Currency::create([
                'name' => $this->name,
                'description' => $this->description,
                'slug' => $this->slug,
            ]);

            $this->toastSuccess('data currency berhasil ditambahkan.');
            $this->dispatch('currency-updated')->to(CurrencyTable::class);
            $this->dispatch('set-reset');
            $this->reset();
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }
    
    public function render()
    {
        return view('livewire.currencies.currency-create');
    }
}
