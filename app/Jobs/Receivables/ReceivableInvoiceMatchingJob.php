<?php

namespace App\Jobs\Receivables;

use Illuminate\Bus\Batchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Services\ValidationErrorService;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\ReceivableInvoiceMatchingServices;

class ReceivableInvoiceMatchingJob implements ShouldQueue
{
    use Queueable, Batchable;

    protected ReceivableInvoiceMatchingServices $matchingReceivableServices;

    /**
     * Create a new job instance.
     */
    public function __construct(public $activity, public $userId)
    {
        $this->matchingReceivableServices = new ReceivableInvoiceMatchingServices;
        $this->matchingReceivableServices->matchReceivables();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($unmatchedReceivables = $this->matchingReceivableServices->getUnmatchedReceivables()) {
            $this->handleUnmatchedReceivables($unmatchedReceivables);
        } else {
            $this->updateReceivableStatus(3);
        }
        $this->activity->finished_at = now();
        $this->activity->save();
    }

    /**
     * Handle unmatched receivables by creating a CSV and updating the activity status.
     */
    protected function handleUnmatchedReceivables(array $unmatchedReceivables): void
    {
        $validationErrorService = $this->createValidationErrorService($unmatchedReceivables);
        $validationErrorService->writeToCsv($this->activity->id);

        $this->updateReceivableStatus(4, $validationErrorService->fileName);
    }

    /**
     * Create ValidationErrorService with required fields and headers.
     */
    protected function createValidationErrorService(array $unmatchedReceivables): ValidationErrorService
    {
        $validationErrorService = new ValidationErrorService($unmatchedReceivables);
        $validationErrorService->setFields(['Invoice Number']);
        $validationErrorService->setCsvHeader(['No', 'Receivable ID', 'Invoice Number', 'Status']);
        
        return $validationErrorService;
    }

    /**
     * Update receivable status in the database and handle exceptions.
     */
    protected function updateReceivableStatus(int $activityStatus, string $fileName = null): void
    {
        try {
            DB::table('receivables')
                ->whereIn('id',array_column($this->matchingReceivableServices->getMatchedReceivables(),'id'))
                ->update(['status_invoice' => 2]);

            $this->activity->status = $activityStatus;
            if ($fileName) {
                $this->activity->file = Storage::url("exports/{$fileName}");
            }
        } catch (\Exception $e) {
            $this->activity->status = 0;
            Log::critical('Error inserting database:', [$e->getMessage()]);
        }
    }

    /**
     * Handle job failure after max retries.
     */
    public function failed(\Throwable $exception): void
    {
        Log::critical('ReceivableInvoiceMatchingJob permanently failed after max retries', [
            'activityId' => $this->activity->id,
            'userId' => $this->userId,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
