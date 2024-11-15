<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReceivableDocumentBatchResultExport implements FromArray, WithHeadings
{
    public function __construct(public $receivables){}

    public function headings(): array
    {
        return [
            'No', 'Nomor Invoice', 'Status Invoice', 'Status BL', 'Status Receivable'
        ];
    }

    public function array(): array
    {
        $receivableArray = [];

        $row = 1;

        foreach ($this->receivables as $receivable) {
            // Tentukan status receivable
            $statusReceivable = in_array($receivable['status'], [2, 3])
            ? ($receivable['category'] == 1 ? 'Parsial Berjodoh' : 'Berjodoh')
            : ($receivable['status'] == 1 ? 'Belum Berjodoh' : 'Berjodoh');

            // Tentukan status BL
            $statusBl = $receivable['category'] == 2
            ? '-'
            : ($receivable['status_bl'] == 1 ? 'X' : 'O');

            $receivableArray[] = [
                $row++,
                $receivable['invoice_number'],
                $receivable['status_invoice'] == 1 ? 'X' : 'O',
                $statusBl,
                $statusReceivable
            ];
        }

        return $receivableArray;
    }
}
