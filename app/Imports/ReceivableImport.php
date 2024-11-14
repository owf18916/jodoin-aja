<?php

namespace App\Imports;

use App\Models\Receivable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ReceivableImport implements ToCollection, WithStartRow, WithChunkReading, SkipsEmptyRows, WithMultipleSheets
{
    protected $errorRows = [], $validRows = [], $term, $masterValidationService, $currentRow = 1, $existingReceivables = [], $categories, $categoryWithLabels;

    public function __construct(public $userId)
    {
        $this->masterValidationService = new \App\Services\MasterValidationServices(masterExceptions: ['suppliers','banks']);
        $this->categories = Receivable::$categoryLabels;

        foreach ($this->categories as $id => $name) {
            $this->categoryWithLabels[$name] = [
                'id' => $id,
                'name' => $name
            ];
        };
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
        Log::info('categroy labels :', [array_values($this->categories)]);

        $existingReceivables = Receivable::with('customer')->whereHas('customer', function ($q) use($rows) {
            $q->whereIn('name', $rows->pluck(1));
        })
        ->get()
        ->toArray();

        foreach ($existingReceivables as $receivable) {
            $this->existingReceivables[$receivable['customer']['name'].$receivable['invoice_number']] = [
                $receivable
            ];
        }

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
            0 => ['required', function ($attribute, $value, $fail) {
                    if (!in_array($value, array_values(Receivable::$categoryLabels))) {
                        $fail('kategori tidak valid');
                    }
                }], // category
            1 => ['required', $this->masterValidationService->validateCustomer()], // customer
            2 => ['required', function ($attribute, $value, $fail) use($rowData) {
                if (isset($this->existingReceivables[$rowData[1].$value])) {
                    $fail('Nomor invoice sudah digunakan.');
                }
            }], // invoice number JAED40236B-24
            3 => [Rule::requiredIf(fn () => $rowData[3] == 1)], // bl number
            4 => ['required', 'date'], // accounting date
            5 => ['required', $this->masterValidationService->validateCurrency()], // currency
            6 => ['required', 'numeric'], // amount
        ]);

        $validator->setAttributeNames([
            0 => 'categroy',
            1 => 'customer',
            2 => 'invoice number',
            3 => 'bl number',
            4 => 'accounting date',
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
            'category' => $this->categoryWithLabels[$row[0]]['id'],
            'customer_id' => $this->masterValidationService->customers[$row[1]]['id'],
            'invoice_number' => $row[2],
            'bl_number' => $row[3],
            'accounted_date' => $row[4],
            'currency_id' => $this->masterValidationService->currencies[$row[5]]['id'],
            'amount' => $row[6],
            'created_by' => $this->userId,
            'created_at' => now()
        ];
    }
}
