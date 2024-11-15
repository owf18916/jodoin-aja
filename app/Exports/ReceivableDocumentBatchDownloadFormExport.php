<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReceivableDocumentBatchDownloadFormExport implements WithHeadings, WithTitle
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
