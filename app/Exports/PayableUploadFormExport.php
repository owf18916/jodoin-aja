<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PayableUploadFormExport implements WithHeadings, WithMultipleSheets, WithTitle
{
    public function title(): string
    {
        return 'upload';
    }

    public function headings(): array
    {
        return [
            // 'Bank',
            'Supplier',
            'Invoice Number',
            'Accounting Date',
            // 'Invoice Date',
            // 'Payment Date',
            'Currency',
            'Amount'
        ];
    }

    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new \App\Exports\PayableUploadFormExport;
        $sheets[] = new \App\Exports\MasterExport;

        return $sheets;
    }
}
