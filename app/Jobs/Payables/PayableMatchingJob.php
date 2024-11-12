<?php

namespace App\Jobs\Payables;

use Illuminate\Bus\Batchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Services\PayableMatchingServices;
use App\Services\ValidationErrorService;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class PayableMatchingJob implements ShouldQueue
{
    use Queueable, Batchable;

    protected PayableMatchingServices $matchingPayableServices;

    /**
     * Create a new job instance.
     */
    public function __construct(public $activity, public $userId) {
        $this->matchingPayableServices = new PayableMatchingServices;
        $this->matchingPayableServices->matchPayables();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($unmatchedPayables = $this->matchingPayableServices->getUnmatchedPayables()) {
            $this->handleUnmatchedPayables($unmatchedPayables);
        } else {
            $this->updatePayableStatus(3);
        }
        $this->activity->finished_at = now();
        $this->activity->save();
    }

    /**
     * Handle unmatched payables by creating a CSV and updating the activity status.
     */
    protected function handleUnmatchedPayables(array $unmatchedPayables): void
    {
        $validationErrorService = $this->createValidationErrorService($unmatchedPayables);
        $validationErrorService->writeToCsv($this->activity->id);

        $this->updatePayableStatus(4, $validationErrorService->fileName);
    }

    /**
     * Create ValidationErrorService with required fields and headers.
     */
    protected function createValidationErrorService(array $unmatchedPayables): ValidationErrorService
    {
        $validationErrorService = new ValidationErrorService($unmatchedPayables);
        $validationErrorService->setFields(['Invoice Number']);
        $validationErrorService->setCsvHeader(['No', 'Payable ID', 'Invoice Number', 'Status']);
        
        return $validationErrorService;
    }

    /**
     * Update payable status in the database and handle exceptions.
     */
    protected function updatePayableStatus(int $activityStatus, string $fileName = null): void
    {
        try {
            DB::table('payables')
                ->whereIn('id',array_column($this->matchingPayableServices->getMatchedPayables(),'id'))
                ->update(['status' => 2]);

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
        Log::critical('PayableMatchingJob permanently failed after max retries', [
            'activityId' => $this->activity->id,
            'userId' => $this->userId,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
