<?php

namespace App\Livewire;

use Livewire\Component;

class HomeContentCard extends Component
{
    public $href;
    public $icon;
    public $title;
    public $description;
    public function render()
    {
        return view('livewire.home-content-card');
    }
}
