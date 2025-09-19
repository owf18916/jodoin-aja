<?php

namespace App\Livewire\Activities;

use Livewire\Component;
use App\Models\Activity;
use App\Traits\WithSorting;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class ActivityTable extends Component
{
    use WithSorting, WithPagination;

    public 
        $paginate = 5,
        $sortBy = 'created_at',
        $sortDirection = 'desc',
        $search,
        $statusColors = [
            0 => 'danger',
            1 => 'secondary',
            2 => 'warning',
            3 => 'success',
            4 => 'warning',
        ];

    public function placeholder()
    {
        return <<<'HTML'
        <div class="w-full h-full flex justify-center items-center bg-gray-100 opacity-75">
            <span class="ml-4 text-lg text-blue-500 font-medium">Loading data...</span>
        </div>
        HTML;
    }
    public function render()
    {
        return view('livewire.activities.activity-table', [
            'activities' => Activity::with('user')
                ->when(Auth::user()->id != 1, fn ($q) => $q->where('user_id',Auth::user()->id))
                ->search($this->search)
                ->orderBy($this->sortBy, $this->sortDirection)
                ->simplePaginate($this->paginate)
        ]);
    }
}
