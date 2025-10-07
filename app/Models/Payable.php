<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class Payable extends Model
{
    protected $fillable = ['supplier_id', 'invoice_number', 'accounted_date', 'currency_id','amount', 'status', 'document_type','created_by'];

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
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
        ->when(!is_null($this->filterForm->accountedStartDate) && !is_null($this->filterForm->accountedEndDate), function ($secondQuery) {
            $secondQuery->whereBetween('accounted_date', [$this->filterForm->accountedStartDate, $this->filterForm->accountedEndDate]);
        });
    }
}
