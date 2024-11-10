<?php

namespace App\Exports;

use App\Models\Currency;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CurrencyExport implements FromCollection, WithHeadings, WithMapping
{
    private $row = 1;

    public function headings(): array
    {
        return [
            'No', 'Nama Currency', 'Deskripsi', 'Slug'
        ];
    }

    public function map($row): array
    {
        return [
            $this->row++,
            $row->name,
            $row->description,
            $row->slug
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Currency::select('name', 'description', 'slug')->get();
    }
}
