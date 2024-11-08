<?php

namespace App\Livewire\Payables;

use Carbon\Carbon;
use Livewire\Component;
use App\Traits\Swalable;
use Illuminate\Support\Facades\Bus;

class PayableMatch extends Component
{
    use Swalable;

    public $activity;

    public function matchPayables()
    {
        $this->flashSuccess('Data sedang diproses, silahkan cek halaman Process Report.');

        $this->activity = (new \App\Services\ActivityServices(
            jobName:'Matching Payable Documents',
            jobType: 2
        ))->createActivity();

        $batch = Bus::batch([
            new \App\Jobs\Payables\PayableMatchingJob($this->activity, auth()->user()->id)
        ])
        ->name('list-document-payable-import-'.auth()->user()->initial.Carbon::now()->format('Y-m-d H:i:s'))
        ->dispatch();

        $this->activity->job_batches_id = $batch->id;
        $this->activity->save();
    }

    public function render()
    {
        return view('livewire.payables.payable-match');
    }
}
