<?php

use App\Livewire\BorrowBookPage;
use App\Livewire\LandingPage;
use App\Livewire\PublicBrowseBook;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingPage::class)->name('landing');
Route::get('/books/{record}/borrow', BorrowBookPage::class)->name('books.borrow');
Route::get('/public-browse-books', PublicBrowseBook::class)->name('public-browse-books');