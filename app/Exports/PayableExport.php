<?php

namespace App\Exports;

use App\Models\Payable;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class PayableExport implements FromCollection, WithMapping, WithHeadings, WithTitle
{
    private $currentRow = 1;

    public function __construct(public $form){}

    public function title(): string
    {
        return 'list-payable-document';
    }

    public function headings(): array
    {
        return [
            'No',
            'Bank',
            'Supplier',
            'Nomor Invoice',
            'Tanggal Invoice',
            'Tanggal Payment',
            'Amount',
            'Ketersediaan Dokumen',
            'Status',
        ];
    }

    public function map($row): array
    {
        return [
            $this->currentRow++,
            $row->bank->name,
            $row->supplier->name,
            $row->invoice_number,
            $row->invoice_date,
            $row->payment_date,
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
        return Payable::with(['currency', 'bank', 'supplier'])->filter($this->form)->get();
    }
}
