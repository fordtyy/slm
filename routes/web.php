<?php

use App\Livewire\BorrowBookPage;
use App\Livewire\LandingPage;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingPage::class);
Route::get('/books/{record}/borrow', BorrowBookPage::class)->name('books.borrow');
