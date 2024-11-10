<?php

namespace App\Livewire\Currencies;

use App\Models\Currency;
use Livewire\Component;
use App\Traits\Swalable;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;

class CurrencyEdit extends Component
{
    use Swalable;

    public $modal = 'modal-edit-currency';

    public ?Currency $currency;

    #[Locked()]
    public $id;

    #[Validate('required')]
    public $name;

    #[Validate('required')]
    public $description;

    #[Validate('required')]
    public $slug;

    #[On('edit-currency')]
    public function setCurrencyForm(Currency $currency)
    {
        $this->currency = $currency;
        $this->name = $currency->name;
        $this->description = $currency->description;
        $this->slug = $currency->slug;
        $this->id = $currency->id;

        $this->dispatch($this->modal, open: true);
    }

    public function save()
    {
        $this->validate();

        Gate::authorize('manage-currency');

        try {
            $this->currency->name = $this->name;
            $this->currency->description = $this->description;
            $this->currency->slug = $this->slug;
            $this->currency->save();

            $this->toastSuccess('data currency berhasil diupdate.');
            $this->dispatch('currency-updated')->to(CurrencyTable::class);
            $this->dispatch('set-reset');
            $this->reset();
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }
    
    public function render()
    {
        return view('livewire.currencies.currency-edit');
    }
}
