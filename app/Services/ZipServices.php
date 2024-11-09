<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use ZipArchive;

class ZipServices {
    protected $path;

    public function __construct(public $attachments, public $zipFileName){}

    public function createZip()
    {
        $zip = new ZipArchive;

        $this->path = storage_path('app/public/zip/'.$this->zipFileName.'.zip');

        try {
            if ($zip->open($this->path, ZipArchive::CREATE) === TRUE) {
    
                foreach ($this->attachments as $attachment) {
                    if (file_exists($attachment['path'])) {
                        $zip->addFile($attachment['path'], basename($attachment['name']));
                    }
                }
    
                $zip->close();
            }
        } catch (\Exception $e) {
            Log::critical($e->getMessage());
        }
    }

    public function getPath()
    {
        return $this->path;
    }
}