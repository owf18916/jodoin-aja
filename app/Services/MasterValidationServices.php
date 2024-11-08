<?php

namespace App\Services;

use App\Models\Bank;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\Supplier;

class MasterValidationServices
{
    public $banks, $suppliers, $customers, $currencies;
    
    public function __construct(public array $masterExceptions = []){

        if (!in_array('banks', $this->masterExceptions)) {
            $banks = Bank::select('id', 'name')->get();
            foreach ($banks as $bank) {
                $this->banks[$bank->name] = [
                    'id' => $bank->id,
                    'name' => $bank->name,
                ];
            };
        }

        if (!in_array('suppliers', $this->masterExceptions)) {
            $suppliers = Supplier::select('id', 'name')->where('status', 1)->get();
            foreach ($suppliers as $supplier) {
                $this->suppliers[$supplier->name] = [
                    'id' => $supplier->id,
                    'name' => $bank->name,
                ];
            };
        }

        if (!in_array('customers', $this->masterExceptions)) {
            $customer = Customer::select('id', 'name')->where('status', 1)->get();
            foreach ($customer as $customer) {
                $this->customers[$customer->name] = [
                    'id' => $customer->id,
                    'name' => $customer->name,
                ];
            };
        }

        if (!in_array('currencies', $this->masterExceptions)) {
            $currencies = Currency::select('id','name')->get();
            foreach ($currencies as $currency) {
                $this->currencies[$currency->name] = [
                    'id' => $currency->id,
                    'name' => $currency->name
                ];
            };
        }
    }

    public function validateBank(): callable
    {
        return function ($attribute, $value, $fail) {
            if (!isset($this->banks[$value])) {
                $fail('Nama bank tidak valid.');
            }
        };
    }

    public function validateSupplier(): callable
    {
        return function ($attribute, $value, $fail) {
            if (!isset($this->suppliers[$value])) {
                $fail('Nama supplier tidak valid.');
            }
        };
    }

    public function validateCustomer(): callable
    {
        return function ($attribute, $value, $fail) {
            if (!isset($this->customers[$value])) {
                $fail('Nama customer tidak valid.');
            }
        };
    }

    public function validateCurrency(): callable
    {
        return function ($attribute, $value, $fail) {
            if (!isset($this->currencies[$value])) {
                $fail('Currency tidak valid, periksa kembali sheet master.');
                return;
            }
        };
    }
}