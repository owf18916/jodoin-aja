<?php

namespace App\Jobs\Payables;

use Illuminate\Bus\Batchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Services\PayableMatchingServices;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class PayableMatchingJob implements ShouldQueue
{
    use Queueable, Batchable;

    /**
     * Create a new job instance.
     */
    public function __construct(public $activity, public $userId) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $matchingPayableServices = new PayableMatchingServices;
        $matchingPayableServices->matchPayables();

        if (!empty($matchingPayableServices->getUnmatchedPayables())) {
            $validationErrorServices = new \App\Services\ValidationErrorService($matchingPayableServices->getUnmatchedPayables());
                $validationErrorServices->setFields([
                    0 => 'Invoice Number',
                ]);
                
                $validationErrorServices->setCsvHeader([
                    'No', 'Payable ID','Invoice Number','Status' 
                ]);

            $validationErrorServices->writeToCsv($this->activity->id);

            $this->activity->status = 4;
            $this->activity->finished_at = now();
            $this->activity->file = Storage::url("exports/{$validationErrorServices->fileName}");
            $this->activity->save();
        } else {
            try {
                DB::table('payables')->upsert($matchingPayableServices->getMatchedPayables(),'id');

                $this->activity->status = 3;
            } catch (\Exception $e) {
                $this->activity->status = 0;
                Log::critical($e->getMessage());
            }

            $this->activity->finished_at = now();
            $this->activity->save();
        }
    }

    public function failed($exception)
    {
        Log::critical('PayableMatchingJob permanently failed after max retries', [
            'activityId' => $this->activity->id,
            'userId' => $this->userId,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
