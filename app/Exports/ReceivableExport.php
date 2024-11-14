<?php

namespace App\Exports;

use App\Models\Receivable;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class ReceivableExport implements FromCollection, WithMapping, WithHeadings, WithTitle
{
    private $currentRow = 1;

    public function __construct(public $form){}

    public function title(): string
    {
        return 'list-receivable-document';
    }

    public function headings(): array
    {
        return [
            'No',
            'Customer',
            'Nomor Invoice',
            'Nomor BL',
            'Tanggal Catat',
            'Amount',
            'Ketersediaan Dokumen',
            'Status',
        ];
    }

    public function map($row): array
    {
        return [
            $this->currentRow++,
            $row->customer->name,
            $row->invoice_number,
            $row->bl_number,
            $row->accounted_date,
            $row->amount,
            $row->status == 1 ? 'X' : 'O',
            $row->status_label
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Receivable::with(['currency', 'customer'])->filter($this->form)->get();
    }
}
