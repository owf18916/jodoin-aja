<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use App\Models\Payable;
use App\Services\PayableServices;
use Carbon\Carbon;

class PayableForm extends Form
{
    public ?Payable $payable;
    public $invoiceNumber, $supplier,$currency, $amount, $accountedDate;

    public function rules(): array
    {
        return [
            'supplier' => ['required','exists:suppliers,id'],
            'currency' => ['required','exists:currencies,id'],
            'supplier' => ['required','exists:suppliers,id'],
            'invoiceNumber' => ['required', function ($attribute, $value, $fail) {
                $invoiceExists = Payable::where('supplier_id', $this->supplier)
                    ->where('invoice_number', $value)
                    ->count();

                if (!empty($invoiceExists))
                    $fail('nomor invoice tidak valid');
            }],
            'accountedDate' => ['required','date', function ($attribute, $value, $fail) {
                if (Carbon::parse($value) > Carbon::now())
                    $fail('tanggal invoice tidak valid, saat ini masih tanggal '.Carbon::now()->format('d-M-Y'));
            }],
            'amount' => ['required','numeric'],
        ];
    }

    public function setPayable(Payable $payable)
    {
        $this->fill([
            'payable' => $payable,
            'id' => $payable->id,
            'currency' => $payable->currency_id,
            'bank' => $payable->bank_id,
            'supplier' => $payable->supplier_id,
            'invoiceNumber' => $payable->invoice_number,
            'accountedDate' => $payable->accounted_date,
            'amount' => $payable->amount,
        ]);
    }

    public function setSupplierOptions($query = null): array
    {
        return (new PayableServices())->getSupplierOptions($query);
    }

    public function setCurrencyOptions()
    {
        return (new PayableServices())->getCurrencyOptions();
    }

    public function store(): void
    {
        Payable::create(array_merge(
            $this->getCommonPayableData(),
            ['created_by' => auth()->user()->id]
        ));
    }

    public function update(): void
    {
        $this->payable->update(array_merge(
            $this->getCommonPayableData()
        ));
    }

    private function getCommonPayableData(): array
    {
        return [
            'currency_id' => $this->currency,
            'supplier_id' => $this->supplier,
            'invoice_number' => $this->invoiceNumber,
            'accounted_date' => $this->accountedDate,
            'amount' => $this->amount
        ];
    }
}
