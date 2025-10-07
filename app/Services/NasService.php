<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class NasService
{
    // make disk public for usage elsewhere
    public string $disk;
    protected string $incoming;
    protected string $destBase;

    public function __construct(string $disk = 'nas', string $incoming = 'copy-payables-here', string $destBase = 'payables')
    {
        $this->disk = $disk;
        $this->incoming = trim($incoming, '/');
        $this->destBase = trim($destBase, '/');
    }

    public function incomingPath(string $fileName): string
    {
        return "{$this->incoming}/{$fileName}";
    }

    public function existsInIncoming(string $fileName): bool
    {
        return Storage::disk($this->disk)->exists($this->incomingPath($fileName));
    }

    public function moveToPayables(string $fileName, string $accountedDate): string
    {
        $year = Carbon::parse($accountedDate)->format('Y');
        $month = Carbon::parse($accountedDate)->format('m');

        $source = $this->incomingPath($fileName);
        $destDir = "{$this->destBase}/{$year}/{$month}";
        $dest = "{$destDir}/{$fileName}";

        if (! Storage::disk($this->disk)->exists($destDir)) {
            Storage::disk($this->disk)->makeDirectory($destDir);
        }

        if (Storage::disk($this->disk)->exists($dest)) {
            $ext = pathinfo($fileName, PATHINFO_EXTENSION);
            $base = pathinfo($fileName, PATHINFO_FILENAME);
            $dest = "{$destDir}/{$base}_" . now()->format('YmdHis') . ($ext ? ".{$ext}" : '');
        }

        Storage::disk($this->disk)->move($source, $dest);

        return $dest;
    }
}
