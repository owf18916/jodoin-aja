<?php

namespace App\Livewire\Receivables;

use Carbon\Carbon;
use Livewire\Component;
use App\Traits\Swalable;
use Illuminate\Support\Facades\Bus;
use Livewire\Attributes\On;

class ReceivableMatchData extends Component
{
    use Swalable;

    public $matchReceivableData;

    #[On('match-receivable-data-executed')]
    public function matchReceivables()
    {
        $this->flashSuccess('Data sedang diproses, silahkan cek halaman Process Report.');

        $this->matchReceivableData = (new \App\Services\ActivityServices(
            jobName:'Matching Data Receivable Documents',
            jobType: 3
        ))->createActivity();

        $batch = Bus::batch([
            new \App\Jobs\Receivables\ReceivableDataMatchingJob($this->matchReceivableData, auth()->user()->id)
        ])
        ->name('matching-document-receivable-data-'.auth()->user()->initial.Carbon::now()->format('Y-m-d H:i:s'))
        ->dispatch();

        $this->matchReceivableData->job_batches_id = $batch->id;
        $this->matchReceivableData->save();
    }

    public function render()
    {
        return view('livewire.receivables.receivable-match-data');
    }
}
