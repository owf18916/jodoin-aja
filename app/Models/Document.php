<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    protected $table = 'documents';

    protected $fillable = [
        'documentable_type',
        'documentable_id',
        'invoice_number',
        'file_type',
        'file_name',
        'path',
        'path_hash',
        'source',
        'access_type',
        'storage_disk',
        'invoice_items_count',
        'raw_api',
        'retrieved_at',
    ];

    protected $casts = [
        'raw_api' => 'array',
        'retrieved_at' => 'datetime',
    ];

    /**
     * Return an accessible URL (either direct URL or via storage disk).
     *
     * This method is defensive:
     * - if access_type === 'url' returns the raw path (URL from API)
     * - if access_type === 'nas' tries Storage::disk(...)->url(...)
     * - if url() is not available or fails, it falls back to constructing a path
     *   from the disk root (useful for UNC shares / local mounts)
     *
     * The docblock below helps static analyzers (Intelephense) know the type.
     *
     * @return string|null
     */
    public function getAccessibleUrl(): ?string
    {
        if ($this->access_type === 'url' && $this->path) {
            return $this->path;
        }

        if ($this->access_type === 'nas' && $this->storage_disk) {
            try {
                /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
                $disk = Storage::disk($this->storage_disk);

                if (method_exists($disk, 'url')) {
                    $url = $disk->url($this->path);
                    if (! empty($url)) {
                        return $url;
                    }
                }

                $root = config("filesystems.disks.{$this->storage_disk}.root");

                if (! empty($root)) {
                    // make sure analyzer knows these are strings
                    $root = (string) $root;
                    $root = rtrim($root, '/\\'); // <-- use single-quoted '/\\' here

                    $p = (string) ($this->path ?? '');
                    $p = ltrim($p, '/\\');

                    if (str_starts_with($root, '\\\\') || preg_match('#^[A-Za-z]:\\\\#', $root)) {
                        // Windows style
                        return $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $p);
                    }

                    return $root . '/' . str_replace('\\', '/', $p);
                }
            } catch (\Throwable $e) {
                // ignore and fallback
            }
        }

        return $this->path;
    }
}