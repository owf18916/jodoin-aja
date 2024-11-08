<?php

namespace App\Livewire\Payables;

use Carbon\Carbon;
use Livewire\Component;
use App\Traits\Swalable;
use Illuminate\Support\Facades\Bus;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class PayableUpload extends Component
{
    use Swalable;
    use WithFileUploads;

    public \App\Livewire\Forms\PayableUploadForm $form;
    public $activity;

    public function save()
    {
        $uploadedFile = $this->form->upload();

        if(!$uploadedFile) {
            return $this->flashError($this->form->errorMessage);
        }

        $this->flashSuccess('Data sedang diproses, silahkan cek halaman Process Report.');

        $this->activity = (new \App\Services\ActivityServices(
            jobName:'List Document Payable Import',
            jobType: 2
        ))->createActivity();
        
        $filePath = storage_path('app/imports/' . $uploadedFile);

        $batch = Bus::batch([
            new \App\Jobs\Payables\PayableImportJob($filePath, $this->activity, auth()->user()->id)
        ])
        ->name('list-document-payable-import-'.auth()->user()->initial.Carbon::now()->format('Y-m-d H:i:s'))
        ->dispatch();

        $this->activity->job_batches_id = $batch->id;
        $this->activity->save();
    }

    public function importForm()
    {
        return Excel::download(new \App\Exports\PayableUploadFormExport, 'list-document-payable-upload-form.xlsx');
    }

    public function render()
    {
        return view('livewire.payables.payable-upload');
    }
}
