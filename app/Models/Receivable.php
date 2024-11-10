<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receivable extends Model
{
    protected $fillable = ['bank_id', 'customer_id', 'invoice_number', 'bl_number','invoice_date', 'bl_date', 'receipt_date', 'amount'];

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    protected function casts(): array
    {
        return [
            'status' => 'string:status_label',
        ];
    }

    public static $statusLabels = [
        1 => 'Single',
        2 => 'Berjodoh',
    ];

    public function getStatusLabelAttribute()
    {
        return array_key_exists($this->status, self::$statusLabels) ? self::$statusLabels[$this->status] : 'Tidak Diketahui';
    }

    public function scopeSearch($query, $value)
    {
        $query->where('invoice_number','like',"%{$value}%")
            ->orWhereHas('customer', function ($q) use($value) {
                $q->where('name', 'like',"%{$value}%");
            })
            ->orWhereHas('bank', function ($q) use($value) {
                $q->where('name', 'like',"%{$value}%");
            });
    }

    public function getTotalAmount()
    {
        return $this->items->sum(function($item) {
            return $item->qty * $item->price;
        });
    }

    private $filterForm;
    public function scopeFilter($query, $form)
    {
       $this->filterForm = $form;
        
        $query->when(count($this->filterForm->status) > 0, function ($secondQuery) {
            $secondQuery->whereIn('status',$this->filterForm->status);
        })
        ->when(count($this->filterForm->customer) > 0, function ($secondQuery) {
            $secondQuery->whereHas('customer', fn ($q) => $q->whereIn('id',$this->filterForm->customer));
        })
        ->when(count($this->filterForm->bank) > 0, function ($secondQuery) {
            $secondQuery->whereHas('bank', fn ($q) => $q->whereIn('id',$this->filterForm->bank));
        })
        ->when(!is_null($this->filterForm->invoiceStartDate) && !is_null($this->filterForm->invoiceEndDate), function ($secondQuery) {
            $secondQuery->whereBetween('invoice_date', [$this->filterForm->invoiceStartDate, $this->filterForm->invoiceEndDate]);
        })
        ->when(!is_null($this->filterForm->receiptStartDate) && !is_null($this->filterForm->receiptEndDate), function ($secondQuery) {
            $secondQuery->whereBetween('receipt_date', [$this->filterForm->receiptStartDate, $this->filterForm->receiptEndDate]);
        });
    }
}
