<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomerExport implements FromCollection, WithHeadings, WithMapping
{
    private $row = 1;

    public function headings(): array
    {
        return [
            'No', 'Nama Customer', 'Status'
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
        return Customer::select('name', 'status')->get();
    }
}
