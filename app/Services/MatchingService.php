<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Jobs\PersistApiMetaJob;

class MatchingService
{
    protected NasService $nas;
    protected IlsApiClient $ils;
    protected string $documentableClass;
    protected bool $markApiFound;
    protected bool $dispatchMetaToQueue;
    protected bool $enableApi;

    protected array $matched = [];
    protected array $unmatched = [];
    protected array $apiMeta = [];

    public function __construct(
        NasService $nasService,
        IlsApiClient $ilsClient,
        string $documentableClass = \App\Models\Payable::class,
        bool $markApiFound = false,
        bool $dispatchMetaToQueue = true,
        ?bool $enableApi = null
    ) {
        $this->nas = $nasService;
        $this->ils = $ilsClient;
        $this->documentableClass = $documentableClass;
        $this->markApiFound = $markApiFound;
        $this->dispatchMetaToQueue = $dispatchMetaToQueue;
        $this->enableApi = is_null($enableApi) ? (bool) config('services.ils.enabled', false) : (bool) $enableApi;
    }

    public function run(int $chunkSize = 200): void
    {
        $modelClass = $this->documentableClass;

        $modelClass::select('id','invoice_number','accounted_date')
            ->where('status', 1)
            ->chunk($chunkSize, function ($rows) use ($modelClass) {
                $toApi = [];
                $map = [];

                foreach ($rows as $item) {
                    $fileName = $this->invoiceToFilename($item->invoice_number) . '.pdf';

                    if ($this->nas->existsInIncoming($fileName)) {
                        try {
                            $dest = $this->nas->moveToPayables($fileName, $item->accounted_date);

                            $pathHash = hash('sha256', $dest);

                            DB::table('documents')->updateOrInsert(
                                ['path_hash' => $pathHash],
                                [
                                    'documentable_type' => $this->documentableClass,
                                    'documentable_id' => $item->id,
                                    'invoice_number' => $item->invoice_number,
                                    'file_type' => 'invoice',
                                    'file_name' => basename($dest),
                                    'path' => $dest,
                                    'path_hash' => $pathHash,
                                    'source' => 'nas',
                                    'access_type' => 'nas',
                                    'storage_disk' => $this->nas->disk ?? 'nas',
                                    'retrieved_at' => now(),
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]
                            );

                            $item->status = 2;
                            $item->save();

                            $this->matched[] = ['id'=>$item->id,'invoice'=>$item->invoice_number,'path'=>$dest,'source'=>'nas'];
                        } catch (\Throwable $e) {
                            Log::error("NAS move error for {$this->documentableClass} {$item->id}: {$e->getMessage()}");
                            $this->unmatched[] = $this->makeUnmatched($item, 'NAS move error: '.$e->getMessage());
                        }
                        continue;
                    }

                    if (! $this->enableApi) {
                        $this->unmatched[] = $this->makeUnmatched($item, 'Tidak ditemukan di NAS (API disabled)');
                        continue;
                    }

                    $toApi[] = $item->invoice_number;
                    $map[$item->invoice_number][] = $item;
                }

                if ($this->enableApi && ! empty($toApi)) {
                    $apiResults = $this->ils->fetchByInvoices(array_values($toApi));

                    foreach ($toApi as $inv) {
                        $res = $apiResults[$inv] ?? null;
                        if ($res !== null) {
                            foreach ($map[$inv] ?? [] as $documentable) {
                                $this->apiMeta[] = [
                                    'documentable_type' => $this->documentableClass,
                                    'documentable_id' => $documentable->id,
                                    'invoice_number' => $documentable->invoice_number,
                                    'raw' => $res,
                                    'retrieved_at' => now()->toDateTimeString(),
                                ];

                                if ($this->markApiFound) {
                                    $documentable->status = 2;
                                    $documentable->save();
                                }

                                $this->matched[] = ['id'=>$documentable->id,'invoice'=>$documentable->invoice_number,'source'=>'api'];
                            }
                        } else {
                            foreach ($map[$inv] ?? [] as $documentable) {
                                $this->unmatched[] = $this->makeUnmatched($documentable, 'Tidak ditemukan di NAS maupun API');
                            }
                        }
                    }
                }
            });

        if (! empty($this->apiMeta)) {
            if ($this->dispatchMetaToQueue) {
                $chunks = array_chunk($this->apiMeta, 500);
                foreach ($chunks as $chunk) {
                    PersistApiMetaJob::dispatch($chunk)->onQueue('documents');
                }
            } else {
                $this->persistApiMetadata();
            }
        }
    }

    protected function invoiceToFilename(string $raw): string
    {
        $clean = preg_replace('/[\\\\\/:\*\?"<>|]/', '-', $raw);
        $clean = Str::limit($clean, 180, '');
        $clean = Str::slug($clean, '-');
        return $clean ?: 'invoice-'.Str::random(6);
    }

    protected function persistApiMetadata(): int
    {
        if (empty($this->apiMeta)) return 0;

        $rows = [];
        $now = now()->toDateTimeString();

        foreach ($this->apiMeta as $m) {
            $raw = $m['raw'] ?? [];
            $inv = $m['invoice_number'] ?? ($raw['invoice_number'] ?? null);
            $items = $raw['invoice_items'] ?? [];
            $documentableType = $m['documentable_type'] ?? $this->documentableClass;

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

        if (empty($rows)) return 0;

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

        return count($rows);
    }

    protected function makeUnmatched($item, string $message)
    {
        return [
            'row' => $item->id,
            'errors' => [
                [$message]
            ]
        ];
    }

    public function getMatched(): array { return $this->matched; }
    public function getUnmatched(): array { return $this->unmatched; }
    public function getApiMeta(): array { return $this->apiMeta; }
}
