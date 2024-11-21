<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use App\Models\Receivable;
use App\Services\ReceivableServices;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ReceivableForm extends Form
{
    public ?Receivable $receivable;
    public $category ,$invoiceNumber, $blNumber, $customer, $currency, $amount, $accountedDate;

    public function rules(): array
    {
        return [
            'category' => ['required', function ($attribute, $value, $fail) {
                if (!in_array($value, array_keys(Receivable::$categoryLabels))) {
                    $fail('kategori tidak valid');
                }
            }],
            'customer' => ['required','exists:customers,id'],
            'currency' => ['required','exists:currencies,id'],
            'invoiceNumber' => ['required', function ($attribute, $value, $fail) {
                $invoiceExists = Receivable::where('customer_id', $this->customer)
                    ->where('invoice_number', $value)
                    ->count();

                if ($invoiceExists && (empty($this->receivable) || $this->receivable->invoice_number !== $value)) {
                    $fail('nomor invoice sudah ada');
                }
            }],
            'blNumber' => ['min:3', Rule::requiredIf(fn () => $this->category == 1)],
            'accountedDate' => ['required','date', function ($attribute, $value, $fail) {
                if (Carbon::parse($value) > Carbon::now())
                    $fail('tanggal catat tidak valid, saat ini masih tanggal '.Carbon::now()->format('d-M-Y'));
            }],
            'amount' => ['required','numeric'],
        ];
    }

    public function setReceivable(Receivable $receivable)
    {
        $this->fill([
            'receivable' => $receivable,
            'id' => $receivable->id,
            'category' => $receivable->category,
            'currency' => $receivable->currency_id,
            'customer' => $receivable->customer_id,
            'invoiceNumber' => $receivable->invoice_number,
            'blNumber' => $receivable->bl_number,
            'accountedDate' => $receivable->accounted_date,
            'amount' => $receivable->amount,
        ]);
    }

    public function setCustomerOptions($query = null): array
    {
        return (new ReceivableServices())->getCustomerOptions($query);
    }

    public function setCategoryOptions($query = null): array
    {
        return (new ReceivableServices())->getCategoryOptions($query);
    }

    public function setCurrencyOptions()
    {
        return (new ReceivableServices())->getCurrencyOptions();
    }

    public function store(): void
    {
        Receivable::create(array_merge(
            $this->getCommonReceivableData(),
            ['created_by' => Auth::user()->id]
        ));
    }

    public function update(): void
    {
        $this->receivable->update(array_merge(
            $this->getCommonReceivableData()
        ));
    }

    private function getCommonReceivableData(): array
    {
        return [
            'category' => $this->category,
            'currency_id' => $this->currency,
            'customer_id' => $this->customer,
            'invoice_number' => $this->invoiceNumber,
            'bl_number' => $this->blNumber,
            'accounted_date' => $this->accountedDate,
            'amount' => $this->amount
        ];
    }
}
