<?php

namespace App\Livewire\DocumentUpload;

use Livewire\Component;
use App\Traits\Swalable;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class DocumentUploadIndex extends Component
{
    use WithFileUploads;
    use Swalable;

    public $modal = 'modal-upload-doument';

    protected
    $categories = [
        'payable' => 'Payable Document',
        'receivable' => 'Receivable Document',
        'bl' => 'BL Document',
    ],
    $pattern = '/([a-zA-Z0-9\-]+)\.pdf$/i',
    $pdfPath;

    public $category = 'payable', $files = [], $validFiles = [], $validDocuments, $invalidFiles = [];

    #[On('upload-document')]
    public function setModal()
    {
        $this->fill([
            'files' => [],
            'validFiles' => [],
            'validDocuments' => [],
        ]);

        $this->dispatch($this->modal, open: true);
    }

    protected $rules = [
        'files.*' => 'required|mimes:pdf|max:5000', // Validasi tipe dan ukuran file
    ];

    // Hook yang dijalankan ketika file di-upload
    public function updatedFiles()
    {
        if (count($this->files) > 500) {
            return $this->flashError('Maksimal 500 dokumen yang diupload.');
        }

        $this->invalidFiles = [];

        foreach ($this->files as $index => $file) {
            $fileName = $file->getClientOriginalName();
            
            // Validasi nama file harus memenuhi format `xxxxx.pdf`
            if (preg_match($this->pattern, $fileName, $matches)) {
                $this->validFiles[] = $file;

                $this->validDocuments[$fileName] = [
                    'category' => $this->categories[$this->category],
                    'file_name' => $fileName
                ];
            } else {
                // Jika format nama file salah
                $this->invalidFiles[$index] = "Nama file harus sesuai format: xxxxxx.pdf";
            }
        }
    }

    // Hanya file yang lolos validasi diimport
    public function save()
    {
        $this->validate();

        $storagePath = 'public/documents/';

        if ($this->category == 'payable') {
            $this->pdfPath = $storagePath.'payables/copy-payables-here/';
        } elseif ($this->category == 'receivable') {
            $this->pdfPath = $storagePath.'receivables/copy-receivables-here/';
        } elseif ($this->category == 'bl') {
            $this->pdfPath = $storagePath.'bl/copy-bl-here/';
        }
   
        // Pastikan hanya file yang valid yang di-upload
        foreach ($this->validFiles as $file) {
            $fileName = $file->getClientOriginalName();

            $this->checkFileExistence($this->pdfPath.$fileName);
            
            Storage::disk('local')->putFileAs($this->pdfPath, $file, $fileName);
        }

        $this->flashSuccess('File berhasil diupload.');
        $this->dispatch($this->modal, open: false);
    }

    private function checkFileExistence($filePath)
    {
        // Cek apakah file dengan nama yang sama sudah ada di NAS
        if (Storage::disk('local')->exists($filePath)) {
            // Jika file ada, lakukan replace (dengan storeAs yang akan overwrite file yang lama)
            Storage::disk('local')->delete($filePath);
        }
    }
    
    public function render()
    {
        return view('livewire.document-upload.document-upload-index');
    }
}
