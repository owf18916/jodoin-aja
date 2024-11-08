<?php

namespace App\Livewire\Payables;

use App\Models\Payable;
use Livewire\Component;
use App\Traits\Swalable;
use Livewire\Attributes\On;

class PayableDelete extends Component
{
    use Swalable;
    
    #[On('payable-deleted')]
    public function delete($id)
    {
        $payable = Payable::find($id);

        try {
            $payable->delete();
            $this->dispatch('payable-updated')->to(PayableTable::class);
            $this->toastSuccess('dokumen payable berhasil dihapus.');
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    public function render()
    {
        return <<<'HTML'
        <div>
            {{-- Do your work, then step back. --}}
        </div>
        HTML;
    }
}
