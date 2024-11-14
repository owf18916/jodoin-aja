<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receivable extends Model
{
    protected $fillable = ['customer_id', 'category', 'invoice_number', 'bl_number','accounted_date', 'currency_id','amount', 'created_by'];

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    protected function casts(): array
    {
        return [
            'category' => 'string:category_label',
            'status' => 'string:status_label',
        ];
    }

    public static $statusLabels = [
        1 => 'Single',
        2 => 'Invoice Berjodoh',
        3 => 'BL Berjodoh',
        4 => 'Semua Berjodoh'
    ];

    public static $categoryLabels = [
        1 => 'Sales AR',
        2 => 'Other AR'
    ];

    public function getStatusLabelAttribute()
    {
        return array_key_exists($this->status, self::$statusLabels) ? self::$statusLabels[$this->status] : 'Tidak Diketahui';
    }

    public function getCategoryLabelAttribute()
    {
        return array_key_exists($this->category, self::$categoryLabels) ? self::$categoryLabels[$this->category] : 'Tidak Diketahui';
    }

    public function scopeSearch($query, $value)
    {
        $query->where('invoice_number','like',"%{$value}%")
            ->orWhereHas('customer', function ($q) use($value) {
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
        ->when(!is_null($this->filterForm->accountedStartDate) && !is_null($this->filterForm->accountedEndDate), function ($secondQuery) {
            $secondQuery->whereBetween('accounted_date', [$this->filterForm->accountedStartDate, $this->filterForm->accountedEndDate]);
        });
    }
}
