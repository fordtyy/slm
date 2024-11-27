<?php

namespace App\Livewire;

use App\Models\Book;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Livewire\Component;

class BorrowBookPage extends Component
{
    use InteractsWithRecord;

    public function mount(Book $record)
    {
        $this->record = $record;
    }

    public function render()
    {
        return view('livewire.borrow-book-page');
    }
}
