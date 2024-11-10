<?php

namespace App\Livewire\User;

use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class UserTable extends Component
{
    use WithPagination;

    public 
        $paginate = 5,
        $sortBy = 'created_at',
        $sortDirection = 'desc',
        $search,
        $statusColors = [
            0 => 'warning',
            1 => 'success'
        ];

    #[On('user-updated')]
    public function render()
    {
        return view('livewire.user.user-table', [
            'users' => User::with(['roles'])
                ->orderBy($this->sortBy, $this->sortDirection)
                ->simplePaginate($this->paginate)
        ]);
    }
}
