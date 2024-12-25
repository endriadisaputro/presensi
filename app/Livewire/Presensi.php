<?php

namespace App\Livewire;

use App\Models\Schedule;
use Livewire\Component;
use Auth;

class Presensi extends Component
{
    public $latitude;
    public $longitude;
    public $insideRadius = false;
    public function render()
    {
        $schedule = Schedule::where('user_id', Auth::user()->id)->first();
        return view('livewire.presensi', [
            'schedule' => $schedule,
            'insideRadius' => $this->insideRadius
        ]);
    }
}
