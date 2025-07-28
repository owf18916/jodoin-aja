<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\Receivable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ReceivableDocumentBatchDownloadImport implements ToCollection, WithStartRow, WithChunkReading, SkipsEmptyRows
{
    protected $receivables = [], $errorRows = [], $validRows = [], $term, $masterValidationService, $currentRow = 1;

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
        $this->setReceivables($rows);

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

    public function getReceivables()
    {
        return $this->receivables;
    }

    private function setReceivables($rows)
    {
        $receivables = Receivable::select('id', 'category','invoice_number', 'bl_number', 'accounted_date', 'status_invoice', 'status_bl','status')
            ->whereIn('invoice_number', array_values($rows->toArray()))
            ->get()
            ->toArray();

        foreach ($receivables as $receivable) {
            $this->receivables[$receivable['invoice_number']] = $receivable;
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
            if (!isset($this->receivables[$value])) {
                $fail('Nomor invoice tidak valid.');
            }
        }; 
    }

    private function mapData($row)
    {
        $receivable = $this->receivables[$row];
        $accountedDate = Carbon::parse($receivable['accounted_date']);
        
        // Buat informasi path dan nama file dasar
        $year = $accountedDate->format('Y');
        $month = $accountedDate->format('m');
        $cleanedInvoiceNumber = preg_replace('/[^A-Za-z0-9. ]/', '-', $receivable['invoice_number']);
        
        $invoiceFilePath = storage_path("app/public/documents/receivables/{$year}/{$month}/{$cleanedInvoiceNumber}.pdf");
        
        // Tambahkan invoice ke validRows jika statusnya 4
        if ($receivable['status'] == 4) {
            $this->validRows[] = [
                'path' => $invoiceFilePath,
                'name' => "{$cleanedInvoiceNumber}.pdf",
                'mime' => 'application/pdf',
            ];
        }

        // Jika kategori 1 dan status 4, tambahkan BL file
        if ($receivable['category'] == 1 && $receivable['status'] == 4) {
            $cleanedBlNumber = preg_replace('/[^A-Za-z0-9. ]/', '-', $receivable['bl_number']);
            $blFilePath = storage_path("app/public/documents/bl/{$year}/{$month}/{$cleanedBlNumber}.pdf");

            $this->validRows[] = [
                'path' => $blFilePath,
                'name' => "{$cleanedInvoiceNumber}-BL.pdf",
                'mime' => 'application/pdf',
            ];
        }
    }

}
