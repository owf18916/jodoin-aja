<?php

namespace App\Livewire\Receivables;

use Carbon\Carbon;
use Livewire\Component;
use App\Traits\Swalable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Livewire\Attributes\On;

class ReceivableMatch extends Component
{
    use Swalable;

    public $matchInvoiceProcess;
    public $matchBlProcess;
    public $matchAllProcess;

    #[On('match-receivable-executed')]
    public function matchReceivables()
    {
        $this->flashSuccess('Data sedang diproses, silahkan cek halaman Process Report.');

        // Inisiasi proses untuk setiap pekerjaan
        $this->matchInvoiceProcess = (new \App\Services\ActivityServices(
            jobName: 'Matching Receivable Invoice Documents',
            jobType: 3
        ))->createActivity();

        $this->matchBlProcess = (new \App\Services\ActivityServices(
            jobName: 'Matching Receivable BL Documents',
            jobType: 3
        ))->createActivity();

        // Buat batch dengan semua job
        $batch = Bus::batch([
            new \App\Jobs\Receivables\ReceivableInvoiceMatchingJob($this->matchInvoiceProcess, Auth::user()->id),
            new \App\Jobs\Receivables\ReceivableBlMatchingJob($this->matchBlProcess, Auth::user()->id)
        ])
        ->name('matching-document-receivable-' . Auth::user()->initial . Carbon::now()->format('Y-m-d H:i:s'))
        ->dispatch();

        // Simpan id batch ke setiap proses untuk referensi
        $this->matchInvoiceProcess->job_batches_id = $batch->id;
        $this->matchInvoiceProcess->save();

        $this->matchBlProcess->job_batches_id = $batch->id;
        $this->matchBlProcess->save();
    }

    public function render()
    {
        return view('livewire.receivables.receivable-match');
    }
}
