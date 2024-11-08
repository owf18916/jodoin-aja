<?php

namespace App\Livewire\Activities;

use Livewire\Component;
use Livewire\Attributes\Title;

class ActivityIndex extends Component
{
    #[Title('Process Report')]
    public function render()
    {
        return view('livewire.activities.activity-index');
    }
}
