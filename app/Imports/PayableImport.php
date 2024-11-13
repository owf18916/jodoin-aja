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
            0 => ['required', $this->masterValidationService->validateSupplier()], // supplier
            1 => ['required'], // invoice number
            2 => ['required', 'date'], // accounting date
            3 => ['required', $this->masterValidationService->validateCurrency()], // currency
            4 => ['required', 'numeric'], // amount
        ]);

        $validator->setAttributeNames([
            0 => 'supplier',
            1 => 'invoice number',
            2 => 'accounting date',
            3 => 'currency',
            4 => 'amount',
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
            'supplier_id' => $this->masterValidationService->suppliers[$row[0]]['id'],
            'invoice_number' => $row[1],
            'accounted_date' => $row[2],
            'currency_id' => $this->masterValidationService->currencies[$row[3]]['id'],
            'amount' => $row[4],
            'created_by' => $this->userId,
            'created_at' => now()
        ];
    }
}
