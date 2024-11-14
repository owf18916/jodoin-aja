<?php

namespace App\Livewire\Receivables;

use Carbon\Carbon;
use Livewire\Component;
use App\Traits\Swalable;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ReceivableBatchDownloadDocument extends Component
{
    use Swalable;
    use WithFileUploads;

    public \App\Livewire\Forms\UploadForm $form;
    public $activity;

    public function save()
    {
        $this->form->fileName = 'import-batch-download-receivable';
        $uploadedFile = $this->form->upload();

        if(!$uploadedFile) {
            return $this->flashError($this->form->errorMessage);
        }

        Log::info('total rows:', [$this->form->getTotalRows()]);

        if($this->form->getTotalRows() > 101) {
            $this->flashError('Maksimal data yang bisa dicari adalah 100 invoice number');
            return;
        }

        $this->flashSuccess('Data sedang diproses, silahkan cek halaman Process Report.');

        $this->activity = (new \App\Services\ActivityServices(
            jobName:'Receivable Document Batch Download',
            jobType: 1
        ))->createActivity();
        
        $filePath = storage_path('app/imports/' . $uploadedFile);

        $batch = Bus::batch([
            new \App\Jobs\Receivables\ReceivableDocumentBatchDownloadJob($filePath, $this->activity, auth()->user()->id)
        ])
        ->name('document-receivable-batch-download-'.auth()->user()->initial.Carbon::now()->format('Y-m-d H:i:s'))
        ->dispatch();

        $this->activity->job_batches_id = $batch->id;
        $this->activity->save();
    }

    public function importForm()
    {
        return Excel::download(new \App\Exports\ReceivableDocumentBatchDownloadFormExport, 'document-batch-download-form.xlsx');
    }

    public function render()
    {
        return view('livewire.receivables.receivable-batch-download-document');
    }
}
