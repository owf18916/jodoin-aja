<?php

namespace App\Livewire\Receivables;

use Carbon\Carbon;
use Livewire\Component;
use App\Traits\Swalable;
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

        $this->matchInvoice();
        $this->matchBl();
        $this->matchReceivable();
    }

    private function matchInvoice()
    {
        $this->matchInvoiceProcess = (new \App\Services\ActivityServices(
            jobName:'Matching Receivable Invoice Documents',
            jobType: 3
        ))->createActivity();

        $batch = Bus::batch([
            new \App\Jobs\Receivables\ReceivableInvoiceMatchingJob($this->matchInvoiceProcess, auth()->user()->id)
        ])
        ->name('matching-document-receivable-invoice-'.auth()->user()->initial.Carbon::now()->format('Y-m-d H:i:s'))
        ->dispatch();

        $this->matchInvoiceProcess->job_batches_id = $batch->id;
        $this->matchInvoiceProcess->save();
    }

    private function matchBl()
    {
        $this->matchBlProcess = (new \App\Services\ActivityServices(
            jobName:'Matching Receivable BL Documents',
            jobType: 3
        ))->createActivity();

        $batch = Bus::batch([
            new \App\Jobs\Receivables\ReceivableBlMatchingJob($this->matchBlProcess, auth()->user()->id)
        ])
        ->name('matching-document-receivable-bl-'.auth()->user()->initial.Carbon::now()->format('Y-m-d H:i:s'))
        ->dispatch();

        $this->matchBlProcess->job_batches_id = $batch->id;
        $this->matchBlProcess->save();
    }

    private function matchReceivable()
    {
        $this->matchAllProcess = (new \App\Services\ActivityServices(
            jobName:'Matching All Receivable Documents',
            jobType: 3
        ))->createActivity();

        $batch = Bus::batch([
            new \App\Jobs\Receivables\ReceivableAllMatchingJob($this->matchAllProcess, auth()->user()->id)
        ])
        ->name('matching-document-receivable-all-'.auth()->user()->initial.Carbon::now()->format('Y-m-d H:i:s'))
        ->dispatch();

        $this->matchAllProcess->job_batches_id = $batch->id;
        $this->matchAllProcess->save();
    }

    public function render()
    {
        return view('livewire.receivables.receivable-match');
    }
}
