<?php

use App\Livewire\BlockedPage;
use App\Livewire\BorrowBookPage;
use App\Livewire\LandingPage;
use App\Livewire\PublicBrowseBook;
use App\Models\Book;
use App\Models\Borrow;
use Carbon\Carbon;
use Flowframe\Trend\Trend;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingPage::class)->name('landing');
Route::get('/books/{record}/borrow', BorrowBookPage::class)->name('books.borrow');
Route::get('/public-browse-books', PublicBrowseBook::class)->name('public-browse-books');
Route::get('/blocked-page', BlockedPage::class)->name('blocked-page');
Route::get('/test', function () {


    $trends =  Book::query()
        ->select('title', 'id')
        ->whereHas('borrows', function ($query) {
            $query->whereYear('borrows.created_at', now()->year);
        })
        ->with('borrows')
        ->withCount('borrows')
        ->orderBy('borrows_count', 'desc')
        ->get()
        ->map(function ($data) {
            $data = [
                'label' => $data->title,
                'data' => getSalesPerMonth($data->borrows()
                    ->whereYear('borrows.created_at', now()->year)
                    ->selectRaw('DATE(borrows.created_at) as date, borrows.created_at, COUNT(*) as total')
                    ->groupBy('date')
                    ->get())

            ];

            return $data;
        })
        ->all();

    return dd($trends);
});

function getSalesPerMonth($collection)
{
    $sales = $collection->mapWithKeys(
        fn($item, int $key) => [$item->created_at->format('m') => $item]
    )->all();

    for ($month = 1; $month <= 12; $month++) {

        $monthKey = Carbon::create(null, $month)->format('M');
        $stringMonth = str($month)->padLeft(2, '0')->value();
        if (isset($sales[$stringMonth])) {
            $sales[$monthKey] = $sales[$stringMonth]->total;
            unset($sales[$stringMonth]);
        } else {
            $sales[$monthKey] = 0;
        }
    }

    return $sales;
}
