<?php

namespace App\Jobs\Receivables;

use Illuminate\Bus\Batchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Services\ValidationErrorService;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\ReceivableBlMatchingServices;

class ReceivableBlMatchingJob implements ShouldQueue
{
    use Queueable, Batchable;

    protected ReceivableBlMatchingServices $matchingReceivableServices;

    /**
     * Create a new job instance.
     */
    public function __construct(public $activity, public $userId)
    {
        $this->matchingReceivableServices = new ReceivableBlMatchingServices;
        $this->matchingReceivableServices->matchBl();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($unmatchedBl = $this->matchingReceivableServices->getUnmatchedBl()) {
            $this->handleUnmatchedBl($unmatchedBl);
        } else {
            $this->updateReceivableStatus(3);
        }
        $this->activity->finished_at = now();
        $this->activity->save();
    }

    /**
     * Handle unmatched receivables by creating a CSV and updating the activity status.
     */
    protected function handleUnmatchedBl(array $unmatchedBl): void
    {
        $validationErrorService = $this->createValidationErrorService($unmatchedBl);
        $validationErrorService->writeToCsv($this->activity->id);

        $this->updateReceivableStatus(4, $validationErrorService->fileName);
    }

    /**
     * Create ValidationErrorService with required fields and headers.
     */
    protected function createValidationErrorService(array $unmatchedBl): ValidationErrorService
    {
        $validationErrorService = new ValidationErrorService($unmatchedBl);
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
                ->whereIn('id',array_column($this->matchingReceivableServices->getMatchedBl(),'id'))
                ->update(['status_bl' => 2]);

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
        Log::critical('ReceivableBlMatchingJob permanently failed after max retries', [
            'activityId' => $this->activity->id,
            'userId' => $this->userId,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
