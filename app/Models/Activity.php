<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    protected $guarded = ['id'];

    protected $appends = ['file_existence'];

    public function getFileExistenceAttribute(): bool
    {
        return fileExistsHelper($this->file);
    }

    protected function casts(): array
    {
        return [
            'status' => 'string:status_label',
            'type' => 'string:type_label',
        ];
    }

    public function getStatusLabelAttribute()
    {
        $statusLabels = [
            0 => 'Gagal',
            1 => 'Sedang Diproses',
            2 => 'Proses Valiadasi',
            3 => 'Selesai',
            4 => 'Selesai Dengan Warning'
        ];

        return array_key_exists($this->status, $statusLabels) ? $statusLabels[$this->status] : 'Tidak Diketahui';
    }

    public function getTypeLabelAttribute()
    {
        $typeLabels = [
            1 => 'Download',
            2 => 'Upload',
        ];

        return array_key_exists($this->type, $typeLabels) ? $typeLabels[$this->type] : 'Tidak Diketahui';
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jobBatch(): BelongsTo
    {
        return $this->belongsTo(JobBatch::class, 'job_batches_id', 'id');
    }

    public function scopeSearch($query, $value)
    {
        $query->where('job_batches_id','like',"%{$value}%")
            ->orWhereHas('user', function (Builder $q) use($value) {
                $q->where('name','like',"%{$value}%");
            });
    }
}
