<?php

namespace App\Livewire\Receivables;

use App\Models\Receivable;
use Livewire\Component;
use App\Traits\Swalable;
use Livewire\Attributes\On;

class ReceivableDelete extends Component
{
    use Swalable;
    
    #[On('receivable-deleted')]
    public function delete($id)
    {
        $receivable = Receivable::find($id);

        try {
            $receivable->delete();
            $this->dispatch('receivable-updated')->to(ReceivableTable::class);
            $this->toastSuccess('dokumen receivable berhasil dihapus.');
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
