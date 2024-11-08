<?php

namespace App\Jobs\Payables;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use App\Imports\PayableImport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PayableImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    public $filePath;
    public $activity;
    public $userId;

    /**
     * Create a new job instance.
     */
    public function __construct($filePath, $activity, $userId)
    {
        $this->filePath = $filePath;
        $this->activity = $activity;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            $import = new PayableImport($this->userId);

            Excel::import($import, $this->filePath);

            // Retrieve validation errors
            $errors = $import->getErrorRows();

            if (!empty($errors)) {
                $validationErrorServices = new \App\Services\ValidationErrorService($errors);
                $validationErrorServices->setFields([
                    0 => 'Bank',
                    1 => 'Supplier',
                    2 => 'Invoice Number',
                    3 => 'Invoice Date',
                    4 => 'Payment Date',
                    5 => 'Currency',
                    6 => 'Amount',
                ]);

                $validationErrorServices->writeToCsv($this->activity->id);

                $this->activity->status = 4;
                $this->activity->finished_at = now();
                $this->activity->file = Storage::url("exports/{$validationErrorServices->fileName}");
                $this->activity->save();
            } else {
                try {
                    DB::table('payables')->insert($import->getValidRows());

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
