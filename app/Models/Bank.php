<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $fillable = ['name','initial'];

    protected function casts(): array
    {
        return [
            'status' => 'string:status_label',
        ];
    }

    public static $statusLabels = [
        0 => 'Non-Aktif',
        1 => 'Aktif',
    ];

    public function getStatusLabelAttribute()
    {
        return array_key_exists($this->status, self::$statusLabels) ? self::$statusLabels[$this->status] : 'Tidak Diketahui';
    }
}
