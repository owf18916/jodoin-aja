<?php

namespace App\Exports;

use App\Models\Bank;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BankExport implements FromCollection, WithHeadings, WithMapping
{
    private $row = 1;

    public function headings(): array
    {
        return [
            'No', 'Inisial Bank', 'Nama Bank', 'Status'
        ];
    }

    public function map($row): array
    {
        return [
            $this->row++,
            $row->initial,
            $row->name,
            $row->status_label
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Bank::select('name', 'initial','status')->get();
    }
}
