<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PersistApiMetaJob implements ShouldQueue
{
    use Dispatchable ,InteractsWithQueue, Queueable, SerializesModels;

    public array $apiMetaChunk;
    public $tries = 3;
    public $backoff = [60, 120];

    public function __construct(array $apiMetaChunk)
    {
        $this->apiMetaChunk = $apiMetaChunk;
        if (count($this->apiMetaChunk) > 2000) {
            $this->apiMetaChunk = array_chunk($this->apiMetaChunk, 500)[0];
        }
    }

    public function handle()
    {
        if (empty($this->apiMetaChunk)) return;

        $rows = [];
        $now = now()->toDateTimeString();

        foreach ($this->apiMetaChunk as $m) {
            $raw = $m['raw'] ?? [];
            $inv = $m['invoice_number'] ?? ($raw['invoice_number'] ?? null);
            $items = $raw['invoice_items'] ?? [];
            $documentableType = $m['documentable_type'] ?? \App\Models\Payable::class;

            foreach ($items as $it) {
                $map = [
                    'invoice_file_path' => 'invoice',
                    'pr_file_path' => 'pr',
                    'po_file_path' => 'po',
                    'quotation_file_path' => 'quotation',
                    'bpb_file_path' => 'bpb',
                    'invoice_support_path' => 'support',
                ];
                foreach ($map as $key => $type) {
                    if (! empty($it[$key])) {
                        $path = str_replace('\\', '/', $it[$key]);
                        $pathHash = hash('sha256', $path);
                        $fileName = basename(parse_url($path, PHP_URL_PATH) ?: $path);

                        $rows[] = [
                            'documentable_type' => $documentableType,
                            'documentable_id' => $m['documentable_id'] ?? null,
                            'invoice_number' => $inv,
                            'file_type' => $type,
                            'file_name' => $fileName,
                            'path' => $path,
                            'path_hash' => $pathHash,
                            'source' => 'ils_api',
                            'access_type' => 'url',
                            'storage_disk' => null,
                            'invoice_items_count' => count($items),
                            'raw_api' => json_encode($raw),
                            'retrieved_at' => $m['retrieved_at'] ?? $now,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }
            }
        }

        if (empty($rows)) return;

        try {
            $chunks = array_chunk($rows, 500);
            foreach ($chunks as $chunk) {
                DB::table('documents')->upsert(
                    $chunk,
                    ['path_hash'],
                    [
                        'documentable_type','documentable_id','invoice_number','file_type','file_name',
                        'source','access_type','storage_disk','invoice_items_count','raw_api','retrieved_at','updated_at','path'
                    ]
                );
            }
        } catch (\Throwable $e) {
            Log::error('PersistApiMetaJob upsert error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error('PersistApiMetaJob failed: ' . $exception->getMessage());
    }
}
