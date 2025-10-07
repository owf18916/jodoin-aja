<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IlsApiClient
{
    protected string $url;
    protected string $apiKey;
    protected int $batchSize;

    public function __construct()
    {
        $this->url = config('services.ils.url');
        $this->apiKey = config('services.ils.api_key');
        $this->batchSize = (int) config('services.ils.batch_size', 50);
    }

    public function fetchByInvoices(array $invoiceNumbers): array
    {
        $out = array_fill_keys($invoiceNumbers, null);
        if (empty($invoiceNumbers)) return $out;

        $chunks = array_chunk($invoiceNumbers, $this->batchSize);
        foreach ($chunks as $chunk) {
            try {
                $resp = Http::withHeaders([
                    'API-KEY' => $this->apiKey,
                    'Accept' => 'application/json'
                ])->timeout(30)->retry(3, 200)
                  ->post($this->url, ['invoice_number' => array_values($chunk)]);

                if (! $resp->successful()) {
                    Log::warning("ILS API returned {$resp->status()}");
                    continue;
                }

                $json = $resp->json();
                if (! isset($json['data']) || ! is_array($json['data'])) {
                    Log::warning('ILS API unexpected response shape');
                    continue;
                }

                foreach ($json['data'] as $item) {
                    if (! isset($item['invoice_number'])) continue;
                    $inv = $item['invoice_number'];
                    $out[$inv] = $item;
                }
            } catch (\Throwable $e) {
                Log::error('ILS API error: ' . $e->getMessage());
            }
        }

        return $out;
    }
}
