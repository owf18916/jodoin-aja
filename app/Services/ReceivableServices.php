<?php

namespace App\Services;

use App\Models\Bank;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\Receivable;
use Illuminate\Database\Eloquent\Collection;

class ReceivableServices {
    public function getCustomerOptions($query): array
    {
        return Customer::select('id', 'name')
            ->orWhere('name', 'like', '%' . $query . '%')
            ->get()
            ->map(function ($customer) {
                return [
                    'id' => $customer->id,
                    'customerLabel' => $customer->name,
                ];
            })
            ->toArray();
    }

    public function getStatusOptions(): array
    {
        return [
            ['id' => 1 , 'label' => 'Single'],
            ['id' => 2 , 'label' => 'Berjodoh'],
        ];
    }

    public function getCategoryOptions(): array
    {
        return Receivable::$categoryLabels;
    }

    public function getCurrencyOptions(): Collection
    {
        return Currency::select('id', 'name', 'description')->get();
    }

    public function getBankOptions(): Collection
    {
        return Bank::select('id', 'name', 'initial')->get();
    }
}