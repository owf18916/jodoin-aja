<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Car;
use App\Models\Item;
use App\Models\Term;
use App\Models\User;
use App\Models\Budget;
use App\Models\Section;
use App\Models\Activity;
use App\Models\CarGroup;
use App\Models\Currency;
use App\Models\Department;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;

class ActivityServices
{
    public function __construct(public $jobName, public $jobType)
    {
        
    }

    public function createActivity()
    {
        try {
            $activity = Activity::create([
                'user_id' => auth()->id(),
                'type' => $this->jobType,
                'job_name' => $this->jobName,
                'started_at' => Carbon::now()
            ]);

            return $activity;
        } catch (\Exception $e) {
            Log::error('Failed to create activity', ['exception' => $e->getMessage()]);
            throw $e;
        }
    }
}