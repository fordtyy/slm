<?php

use App\Livewire\BlockedPage;
use App\Livewire\BorrowBookPage;
use App\Livewire\LandingPage;
use App\Livewire\PublicBrowseBook;
use App\Models\Book;
use App\Models\Borrow;
use App\Models\Tag;
use Carbon\Carbon;
use Flowframe\Trend\Trend;
use GeminiAPI\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingPage::class)->name('landing');
Route::get('/books/{record}/borrow', BorrowBookPage::class)->name('books.borrow');
Route::get('/public-browse-books', PublicBrowseBook::class)->name('public-browse-books');
Route::get('/blocked-page', BlockedPage::class)->name('blocked-page');
