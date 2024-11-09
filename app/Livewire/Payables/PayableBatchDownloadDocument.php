<?php

namespace App\Livewire\Payables;

use Carbon\Carbon;
use Livewire\Component;
use App\Traits\Swalable;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Bus;
use Maatwebsite\Excel\Facades\Excel;

class PayableBatchDownloadDocument extends Component
{
    use Swalable;
    use WithFileUploads;

    public \App\Livewire\Forms\UploadForm $form;
    public $activity;

    public function save()
    {
        $this->form->fileName = 'import-batch-download-payable';
        $uploadedFile = $this->form->upload();

        if(!$uploadedFile) {
            return $this->flashError($this->form->errorMessage);
        }

        $this->flashSuccess('Data sedang diproses, silahkan cek halaman Process Report.');

        $this->activity = (new \App\Services\ActivityServices(
            jobName:'Payable Document Batch Download',
            jobType: 2
        ))->createActivity();
        
        $filePath = storage_path('app/imports/' . $uploadedFile);

        $batch = Bus::batch([
            new \App\Jobs\Payables\PayableDocumentBatchDownloadJob($filePath, $this->activity, auth()->user()->id)
        ])
        ->name('document-payable-batch-download-'.auth()->user()->initial.Carbon::now()->format('Y-m-d H:i:s'))
        ->dispatch();

        $this->activity->job_batches_id = $batch->id;
        $this->activity->save();
    }

    public function importForm()
    {
        return Excel::download(new \App\Exports\PayableDocumentBatchDownloadFormExport, 'document-batch-download-form.xlsx');
    }

    public function render()
    {
        return view('livewire.payables.payable-batch-download-document');
    }
}
