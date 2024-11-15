<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\Payable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class PayableDocumentBatchDownloadImport implements ToCollection, WithStartRow, WithChunkReading, SkipsEmptyRows
{
    protected $payables = [], $errorRows = [], $validRows = [], $term, $masterValidationService, $currentRow = 1;

    public function __construct(public $userId) {}

    public function startRow(): int
    {
        return 2;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        $this->setPayables($rows);

        foreach ($rows->toArray() as $index => $row) {
            $this->validateRow($index ,$row);

            if (empty($this->errorRows)) {
                $this->mapData($row[0]);
            }

            $this->currentRow++;
        }

    }

    public function getErrorRows()
    {
        return $this->errorRows;
    }

    public function getValidRows()
    {
        return $this->validRows;
    }

    public function getPayables()
    {
        return $this->payables;
    }

    private function setPayables($rows)
    {
        $payables = Payable::select('id','invoice_number','accounted_date','status')
            ->whereIn('invoice_number', array_values($rows->toArray()))
            ->get()
            ->toArray();

        foreach ($payables as $payable) {
            $this->payables[$payable['invoice_number']] = $payable;
        }
    }

    private function validateRow(int $index, array $rowData): bool
    {
        $validator = Validator::make($rowData, [
            0 => ['required', $this->validateInvoiceNumber()],
        ]);

        $validator->setAttributeNames([
            0 => 'nomor invoice',
        ]);

        if ($validator->fails()) {
            $this->errorRows[] = [
                'status' => 'failed',
                'row' => $this->currentRow,
                'errors' => $validator->errors()->messages()
            ];

            return false;
        }

        return true;
    }

    private function validateInvoiceNumber(): callable
    {
        return function ($attribute, $value, $fail) {
            if (!isset($this->payables[$value])) {
                $fail('Nomor invoice tidak valid.');
            }
        }; 
    }

    private function mapData($row)
    {
        $payable = $this->payables[$row];

        if ($payable['status'] == 2) {
            $year = Carbon::parse($payable['accounted_date'])->format('Y');
            $month = Carbon::parse($payable['accounted_date'])->format('m');
            $cleanedInvoiceNumber = preg_replace('/[^A-Za-z0-9]/', '-', $row);
            $pdfDirectory = storage_path('app/public/documents/payables/');
            $pdfFilePath = $pdfDirectory . $year .'/'. $month .'/'. $cleanedInvoiceNumber . '.pdf';
    
            $this->validRows[] = [
                'path' => $pdfFilePath,
                'name' => $cleanedInvoiceNumber.'.pdf',
                'mime' => 'application/pdf',
            ];
        }
    }
}
