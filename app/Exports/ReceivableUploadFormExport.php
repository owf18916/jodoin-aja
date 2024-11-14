<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;

class ReceivableUploadFormExport implements WithHeadings, WithMultipleSheets, WithTitle
{
    public function title(): string
    {
        return 'upload';
    }

    public function headings(): array
    {
        return [
            'AR Category',
            'Customer',
            'Invoice Number',
            'BL Number',
            'Accounting Date',
            'Currency',
            'Amount'
        ];
    }

    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new \App\Exports\ReceivableUploadFormExport;
        $sheets[] = new \App\Exports\MasterExport;

        return $sheets;
    }
}
