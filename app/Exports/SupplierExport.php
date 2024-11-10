<?php

namespace App\Exports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SupplierExport implements FromCollection, WithHeadings, WithMapping
{
    private $row = 1;

    public function headings(): array
    {
        return [
            'No', 'Nama Supplier', 'Status'
        ];
    }

    public function map($row): array
    {
        return [
            $this->row++,
            $row->name,
            $row->status_label
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Supplier::select('name', 'status')->get();
    }
}
