<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PayableDocumentBatchResultExport implements FromArray, WithHeadings
{
    public function __construct(public $payables){}

    public function headings(): array
    {
        return [
            'No', 'Nomor Invoice', 'Status Dokumen'
        ];
    }

    public function array(): array
    {
        $payableArray = [];

        $row = 1;

        foreach ($this->payables as $payable) {
            $payableArray[] = [
                $row++,
                $payable['invoice_number'],
                $payable['status'] == 1 ? 'X' : 'O'
            ];
        }

        return $payableArray;
    }
}
