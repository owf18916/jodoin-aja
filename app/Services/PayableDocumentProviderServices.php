<?php

namespace App\Services;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PayableDocumentBatchResultExport;
use Illuminate\Support\Facades\Log;

class PayableDocumentProviderServices {
    public $zipServices;
    public $zipName;

    public function __construct(public $mappedFiles, public $payables){}

    public function createDocument()
    {
        $this->zipName = 'payable-document-'.uniqid();
        $excelFileName = $this->zipName.'.xlsx';
        $excelFilePath = storage_path('\\app\\'.$excelFileName);

        Log::info('excel path:', [$excelFilePath]);
        
        Excel::store(new PayableDocumentBatchResultExport($this->payables), $excelFileName, 'local');

        $this->mappedFiles[] = [
            'path' => $excelFilePath,
            'name' => $excelFileName,
            'mime' => 'application/xlsx',
        ];

        $this->zipServices = new \App\Services\ZipServices($this->mappedFiles, $this->zipName);
        $this->zipServices->createZip();

        // unlink($excelFilePath);
    }

    public function getFileName()
    {
        return $this->zipName;
    }
}