<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PayableImport implements ToCollection, WithStartRow, WithChunkReading, SkipsEmptyRows, WithMultipleSheets
{
    protected $errorRows = [], $validRows = [], $term, $masterValidationService, $currentRow = 1;

    public function __construct(public $userId)
    {
        $this->masterValidationService = new \App\Services\MasterValidationServices(masterExceptions: ['customers']);
    }

    public function sheets(): array
    {
        return [
            'upload' => $this
        ];
    }

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
        foreach ($rows->toArray() as $index => $row) {
            $validatedRow = $this->validateRow($index ,$row);

            if ($validatedRow) {
                $this->mapData($row);
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

    private function validateRow(int $index, array $rowData): bool
    {
        $validator = Validator::make($rowData, [
            0 => ['required', $this->masterValidationService->validateBank()], // bank
            1 => ['required', $this->masterValidationService->validateSupplier()], // supplier
            2 => ['required'], // invoice number
            3 => ['required', 'date'], // invoice date
            4 => ['required', 'date'], // payment date
            5 => ['required', $this->masterValidationService->validateCurrency()], // currency
            6 => ['required', 'numeric'], // amount
        ]);

        $validator->setAttributeNames([
            0 => 'bank',
            1 => 'supplier',
            2 => 'invoice number',
            3 => 'invoice date',
            4 => 'payment date',
            5 => 'currency',
            6 => 'amount',
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

    private function mapData($row)
    {
        $this->validRows[] = [
            'bank_id' => $this->masterValidationService->banks[$row[0]]['id'],
            'supplier_id' => $this->masterValidationService->suppliers[$row[1]]['id'],
            'invoice_number' => $row[2],
            'invoice_date' => $row[3],
            'payment_date' => $row[4],
            'currency_id' => $this->masterValidationService->currencies[$row[5]]['id'],
            'amount' => $row[6],
            'created_by' => $this->userId,
            'created_at' => now()
        ];
    }
}
