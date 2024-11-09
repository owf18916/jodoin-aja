<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use Livewire\WithFileUploads;
use App\Traits\ExcelHandleTrait;
use Livewire\Attributes\Validate;

class UploadForm extends Form
{
    use ExcelHandleTrait;
    use WithFileUploads;
    
    public $fileName, $title = 'upload', $mimes = 'xlsx', $errorMessage;
    
    #[Validate('required|file|mimes:xlsx|max:2048')]
    public $file;


    public function upload()
    {
        $this->loadSpreadsheet($this->file->getRealPath());

        if (!$this->isSheetValid($this->title)) {
            $this->errorMessage = 'Pastikan sheet "'.$this->title.'" ada.';
            
            return false;
        }

        if ($this->isSheetBlank()) {
            $this->errorMessage = 'File yang diupload kosong, periksa kembali.';
            
            return false;
        }

        $fileName = $this->fileName.'-'.microtime().'.'.$this->mimes;

        $this->file->storeAs('imports', $fileName);


        return $fileName;
    }
}
