<?php

namespace App\Jobs\Payables;

use App\Imports\PayableDocumentBatchDownloadImport;
use App\Services\PayableDocumentProviderServices;
use Illuminate\Bus\Batchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class PayableDocumentBatchDownloadJob implements ShouldQueue
{
    use Queueable, Batchable;

    /**
     * Create a new job instance.
     */
    public function __construct(public $filePath, public $activity, public $userId)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $import = new PayableDocumentBatchDownloadImport($this->userId);

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
                    $payableDocumentProviderServices = new PayableDocumentProviderServices($import->getValidRows(), $import->getPayables());
                    $payableDocumentProviderServices->createDocument();

                    $this->activity->status = 3;
                } catch (\Exception $e) {
                    $this->activity->status = 0;
                    Log::critical($e->getMessage());
                }

                $this->activity->finished_at = now();
                $this->activity->save();
            }

        } catch (\Exception $e) {
            Log::error('PayableImportJob failed during processing', [
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
        Log::critical('PayableImportJob permanently failed after max retries', [
            'filePath' => $this->filePath,
            'activityId' => $this->activity->id,
            'userId' => $this->userId,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
