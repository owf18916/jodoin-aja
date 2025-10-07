<?php

namespace App\Services;

use ZipArchive;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ZipServices {
    protected $path;

    public function __construct(public $attachments, public $zipFileName){}

    public function createZip()
    {
        $zip = new ZipArchive;

        $zipDir  = storage_path('app/public/zip');
        $zipName = $this->zipFileName . '.zip';
        $this->path = $zipDir . DIRECTORY_SEPARATOR . $zipName;

        if (!File::isDirectory($zipDir)) {
            File::makeDirectory($zipDir, 0755, true);
        }

        if ($zip->open($this->path, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            foreach ($this->attachments as $a) {
                $disk = $a['disk'] ?? 'nas_fatp';
                $rel  = $a['relative'] ?? null;

                if ($rel && Storage::disk($disk)->exists($rel)) {
                    $bytes = Storage::disk($disk)->get($rel);
                    $zip->addFromString($a['name'], $bytes);
                } else {
                    Log::warning('Attachment not found on NAS disk', ['attachments' => $this->attachments,'disk'=>$disk, 'relative'=>$rel]);
                }
            }
            $zip->close();
        }
    }

    public function getPath()
    {
        return $this->path;
    }
}