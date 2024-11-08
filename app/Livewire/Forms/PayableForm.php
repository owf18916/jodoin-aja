<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use App\Models\Payable;
use App\Services\PayableServices;

class PayableForm extends Form
{
    public ?Payable $payable;
    public $invoiceNumber, $bank, $supplier,$currency, $amount, $invoiceDate, $paymentDate;

    public function rules(): array
    {
        return [
            'supplier' => ['required','exists:suppliers,id'],
            'currency' => ['required','exists:currencies,id'],
            'bank' => ['required','exists:banks,id'],
            'supplier' => ['required','exists:suppliers,id'],
            'invoiceNumber' => ['required', function ($attribute, $value, $fail) {
                $invoiceExists = Payable::where('suppier_id', $this->supplier)
                    ->where('invoice_number', $value)
                    ->count();

                if (!empty($invoiceExists))
                    $fail('nomor invoice tidak valid');
            }],
            'invoiceDate' => ['required','date'],
            'paymentDate' => ['required','date'],
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
            'invoiceDate' => $payable->invoice_date,
            'paymentDate' => $payable->payment_date,
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

    public function setBankOptions()
    {
        return (new PayableServices())->getBankOptions();
    }

    public function store(): void
    {
        Payable::create(array_merge(
            $this->getCommonPayableData()
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
            'bank_id' => $this->bank,
            'supplier_id' => $this->supplier,
            'invoice_number' => $this->invoiceNumber,
            'invoice_date' => $this->invoiceDate,
            'payment_date' => $this->paymentDate,
            'amount' => $this->amount
        ];
    }
}
