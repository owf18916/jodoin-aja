<?php

namespace App\Jobs\Receivables;

use Illuminate\Bus\Batchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class ReceivableDataMatchingJob implements ShouldQueue
{
    use Queueable, Batchable;

    /**
     * Create a new job instance.
     */
    public function __construct(public $activity, public $userId)
    {

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Kondisi 1: Jika status_invoice dan status_bl sama-sama bernilai 2, set kolom status menjadi 4
            DB::table('receivables')
            ->where('status_invoice', 2)
            ->where('status_bl', 2)
            ->update(['status' => 4]);
   
           // Kondisi 2: Jika status_invoice bernilai 2 dan status_bl bernilai 1, set kolom status menjadi 2
           DB::table('receivables')
               ->where('status_invoice', 2)
               ->where('status_bl', 1)
               ->update(['status' => 2]);
   
           // Kondisi 3: Jika status_invoice bernilai 1 dan status_bl bernilai 2, set kolom status menjadi 3
           DB::table('receivables')
               ->where('status_invoice', 1)
               ->where('status_bl', 2)
               ->update(['status' => 3]);

            // Kondisi 4: Jika status_invoice bernilai 2 dan category bernilai 2 atau 3, set kolom status menjadi 4
           DB::table('receivables')
               ->where('status_invoice', 2)
               ->whereIn('category', [2, 3])
               ->update(['status' => 4]);
   
            $this->activity->finished_at = now();
           $this->activity->status = 3;
           $this->activity->save();
        } catch (\Exception $e) {
            $this->activity->finished_at = now();
            $this->activity->status = 0;
            $this->activity->save();

            Log::critical('Error inserting database:', [$e->getMessage()]);
        }
    }

    /**
     * Handle job failure after max retries.
     */
    public function failed(\Throwable $exception): void
    {
        Log::critical('ReceivableDataMatchingJob permanently failed after max retries', [
            'activityId' => $this->activity->id,
            'userId' => $this->userId,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
