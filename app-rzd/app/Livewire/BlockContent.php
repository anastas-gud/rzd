<?php

namespace App\Livewire;

use Livewire\Component;

class BlockContent extends Component
{
    public $href = "";
    public $icon = "";
    public $title = "";
    public $description = "";

    public function mount($href, $icon, $title, $description)
    {
        $this->href = $href;
        $this->icon = $icon;
        $this->title = $title;
        $this->description = $description;
    }
    public function render()
    {
        return view('livewire.block-content');
    }
}
