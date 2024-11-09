<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class PayableDocumentBatchDownloadFormExport implements WithHeadings, WithTitle
{
    public function title(): string
    {
        return 'upload';
    }

    public function headings(): array
    {
        return [
            'Invoice Number'
        ];
    }
}
