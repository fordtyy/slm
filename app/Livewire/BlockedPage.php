<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.blocked-page')]
class BlockedPage extends Component
{
    public function render()
    {
        return view('livewire.blocked-page');
    }
}
