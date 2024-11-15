<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use Livewire\WithFileUploads;
use App\Traits\ExcelHandleTrait;
use Livewire\Attributes\Validate;

class ReceivableUploadForm extends Form
{
    use ExcelHandleTrait;
    use WithFileUploads;

    #[Validate('required|file|mimes:xlsx|max:2048')]
    public $file;

    public $errorMessage;

    public function upload()
    {
        $this->loadSpreadsheet($this->file->getRealPath());

        if (!$this->isSheetValid('upload')) {
            $this->errorMessage = 'Pastikan sheet "upload" ada.';
            
            return false;
        }

        if ($this->isSheetBlank()) {
            $this->errorMessage = 'File yang diupload kosong, periksa kembali.';
            
            return false;
        }

        $fileName = 'import-receivable-document-'.microtime().'.xlsx';

        $this->file->storeAs('imports', $fileName);

        return $fileName;
    }
}
