<?php

use App\Filament\Account\Resources\BorrowResource;
use App\Livewire\BorrowBookPage;
use App\Livewire\BrowseBookPage;
use App\Livewire\LandingPage;
use App\Models\Borrow;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingPage::class);
Route::get('/books/{record}/borrow', BorrowBookPage::class)->name('books.borrow');
