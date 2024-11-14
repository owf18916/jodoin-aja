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
            'Kategori AR',
            'Customer',
            'Nomor Invoice',
            'Nomor BL',
            'Tanggal Catat',
            'Amount',
            'Ketersediaan Dokumen Invoice',
            'Ketersediaan Dokumen BL',
            'Status',
        ];
    }

    public function map($row): array
    {
        if ($row->category == 2) {
            $statusBl = '-';
        } else {
            $row->status_bl == 1 ? $statusBl = 'X' : $statusBl = 'O';
        }

        return [
            $this->currentRow++,
            $row->category_label,
            $row->customer->name,
            $row->invoice_number,
            $row->bl_number,
            $row->accounted_date,
            $row->amount,
            $row->status_invoice == 1 ? 'X' : 'O',
            $statusBl,
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
