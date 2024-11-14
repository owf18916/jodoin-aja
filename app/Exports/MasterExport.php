<?php

namespace App\Exports;

use App\Models\Bank;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;


class MasterExport implements FromArray, WithTitle, WithHeadings
{
    protected $suppliers, $banks, $customers, $currencies, $receivableCategories;
    
    public function __construct()
    {
        $this->suppliers = Supplier::select('id','name')->get();
        $this->customers = Customer::select('id','name')->get();
        $this->currencies = Currency::select('id','name')->get();
        $this->banks = Bank::select('id','name')->get();
    }

    public function title(): string
    {
        return 'master';
    }

    public function headings(): array
    {
        return [
            'Supplier ID', 'Supplier','',
            'Customer ID', 'Customer', '',
            'Bank ID', 'Bank','',
            'Currency ID', 'Currency',''
        ];
    }

    public function array(): array
    {
        $data = [];

        $maxRows = max(count($this->suppliers), count($this->customers), count($this->banks), count($this->currencies));

        for ($i = 1; $i <= $maxRows; $i++) {
            $row = [];

            // Supplier
            $row[] = isset($this->suppliers[$i]) ? $i : '';
            $row[] = $this->suppliers[$i]->name ?? '';

            // Empty column (C)
            $row[] = '';

            // Customer (D to E)
            $row[] = isset($this->customers[$i]) ? $i : '';
            $row[] = $this->customers[$i]->name ?? '';

            // Empty column (F)
            $row[] = '';

            // Bank (G to H)
            $row[] = isset($this->banks[$i]) ? $i : '';
            $row[] = $this->banks[$i]->name ?? '';

            // Empty column (I)
            $row[] = '';

            // Currency (J to K)
            $row[] = isset($this->currencies[$i]) ? $i : '';
            $row[] = $this->currencies[$i]->name ?? '';

            // Empty column (L)
            $row[] = '';

            // Currency (M to N)
            $row[] = isset($this->receivableCategories[$i]) ? $i : '';
            $row[] = $this->receivableCategories[$i] ?? '';

            $data[] = $row;
        }

        return $data;
    }
}
