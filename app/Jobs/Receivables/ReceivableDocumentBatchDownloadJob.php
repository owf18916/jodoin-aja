<?php

namespace App\Jobs\Receivables;

use Illuminate\Bus\Batchable;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\ReceivableDocumentProviderServices;
use App\Imports\ReceivableDocumentBatchDownloadImport;

class ReceivableDocumentBatchDownloadJob implements ShouldQueue
{
    use Queueable, Batchable;

    /**
     * Create a new job instance.
     */
    public function __construct(public $filePath, public $activity, public $userId)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $import = new ReceivableDocumentBatchDownloadImport($this->userId);

            Excel::import($import, $this->filePath);

            // Retrieve validation errors
            $errors = $import->getErrorRows();

            if (!empty($errors)) {
                $validationErrorServices = new \App\Services\ValidationErrorService($errors);
                $validationErrorServices->setFields([
                    0 => 'Invoice Number',
                ]);

                $validationErrorServices->writeToCsv($this->activity->id);

                $this->activity->status = 4;
                $this->activity->finished_at = now();
                $this->activity->file = Storage::url("exports/{$validationErrorServices->fileName}");
                $this->activity->save();
            } else {
                try {
                    $receivableDocumentProviderServices = new ReceivableDocumentProviderServices($import->getValidRows(), $import->getReceivables());
                    $receivableDocumentProviderServices->createDocument();

                    $this->activity->file = Storage::url("zip/{$receivableDocumentProviderServices->getFileName()}.zip");
                    $this->activity->status = 3;
                } catch (\Exception $e) {
                    $this->activity->status = 0;
                    Log::critical($e->getMessage());
                }

                $this->activity->finished_at = now();
                $this->activity->save();
            }

        } catch (\Exception $e) {
            Log::error('ReceivableDocumentBatchDownloadJob failed during processing', [
                'filePath' => $this->filePath,
                'activityId' => $this->activity->id,
                'userId' => $this->userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->activity->status = 0;
            $this->activity->finished_at = now();
            $this->activity->save();

            $this->fail($e);
        }
    }

    public function failed($exception)
    {
        Log::critical('ReceivableImportJob permanently failed after max retries', [
            'filePath' => $this->filePath,
            'activityId' => $this->activity->id,
            'userId' => $this->userId,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
