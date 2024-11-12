<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payable extends Model
{
    protected $fillable = ['bank_id', 'supplier_id', 'invoice_number', 'invoice_date', 'payment_date', 'amount', 'status'];

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
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
            ->orWhereHas('supplier', function ($q) use($value) {
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
        ->when(count($this->filterForm->supplier) > 0, function ($secondQuery) {
            $secondQuery->whereHas('supplier', fn ($q) => $q->whereIn('id',$this->filterForm->supplier));
        })
        ->when(count($this->filterForm->bank) > 0, function ($secondQuery) {
            $secondQuery->whereHas('bank', fn ($q) => $q->whereIn('id',$this->filterForm->bank));
        })
        ->when(!is_null($this->filterForm->invoiceStartDate) && !is_null($this->filterForm->invoiceEndDate), function ($secondQuery) {
            $secondQuery->whereBetween('invoice_date', [$this->filterForm->invoiceStartDate, $this->filterForm->invoiceEndDate]);
        })
        ->when(!is_null($this->filterForm->paymentStartDate) && !is_null($this->filterForm->paymentEndDate), function ($secondQuery) {
            $secondQuery->whereBetween('payment_date', [$this->filterForm->paymentStartDate, $this->filterForm->paymentEndDate]);
        });
    }
}
