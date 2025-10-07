<?php

namespace App\Jobs;

use App\Services\MatchingService;
use App\Services\NasService;
use App\Services\IlsApiClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StartMatchingJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public string $documentableClass;
    public int $chunkSize;
    public bool $markApiFound;
    public bool $dispatchMetaToQueue;
    public ?bool $enableApi;

    public $tries = 1;
    public $timeout = 1800;

    /**
     * @param string $documentableClass e.g. App\Models\Payable::class
     */
    public function __construct(string $documentableClass = \App\Models\Payable::class, int $chunkSize = 200, bool $markApiFound = false, bool $dispatchMetaToQueue = true, ?bool $enableApi = null)
    {
        $this->documentableClass = $documentableClass;
        $this->chunkSize = $chunkSize;
        $this->markApiFound = $markApiFound;
        $this->dispatchMetaToQueue = $dispatchMetaToQueue;
        $this->enableApi = $enableApi;
    }

    public function handle()
    {
        $nas = new NasService();
        $ils = new IlsApiClient();

        $svc = new MatchingService(
            $nas,
            $ils,
            $this->documentableClass,
            $this->markApiFound,
            $this->dispatchMetaToQueue,
            $this->enableApi
        );

        $svc->run($this->chunkSize);
    }
}