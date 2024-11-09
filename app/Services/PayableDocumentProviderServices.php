<?php

namespace App\Services;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PayableDocumentBatchResultExport;
use Illuminate\Support\Facades\Log;

class PayableDocumentProviderServices {
    public $zipServices;

    public function __construct(public $mappedFiles, public $payables){}

    public function createDocument()
    {
        $zipName = 'payable-document-'.uniqid();
        $excelFileName = $zipName.'.xlsx';
        $excelFilePath = storage_path('app/'.$excelFileName);

        Log::info('excel path:', [$excelFilePath]);
        
        Excel::store(new PayableDocumentBatchResultExport($this->payables), $excelFileName, 'local');

        $this->mappedFiles[] = [
            'path' => $excelFilePath,
            'name' => $excelFileName,
            'mime' => 'application/xlsx',
        ];

        Log::info('files:', [$this->mappedFiles]);

        $this->zipServices = new \App\Services\ZipServices($this->mappedFiles, $zipName);
        $this->zipServices->createZip();

        unlink($excelFilePath);
    }
}