<?php

namespace App\Services;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReceivableDocumentBatchResultExport;

class ReceivableDocumentProviderServices {
    public $zipServices;
    public $zipName;

    public function __construct(public $mappedFiles, public $receivables){}

    public function createDocument()
    {
        $this->zipName = 'receivable-document-'.uniqid();
        $excelFileName = $this->zipName.'.xlsx';
        $excelFilePath = storage_path('\\app\\'.$excelFileName);
        
        Excel::store(new ReceivableDocumentBatchResultExport($this->receivables), $excelFileName, 'local');

        $this->mappedFiles[] = [
            'path' => $excelFilePath,
            'name' => $excelFileName,
            'mime' => 'application/xlsx',
        ];

        $this->zipServices = new \App\Services\ZipServices($this->mappedFiles, $this->zipName);
        $this->zipServices->createZip();
    }

    public function getFileName()
    {
        return $this->zipName;
    }
}