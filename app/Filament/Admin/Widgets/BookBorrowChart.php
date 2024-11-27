<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Book;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class BookBorrowChart extends ChartWidget
{
    protected static ?string $heading = 'Book Borrow History Every Year';
    protected int | string | array $columnSpan = 12;
    protected static ?string $maxHeight = '300px';
    protected static ?string $pollingInterval = null;
    protected static ?int $sort = 2;

    protected function getFilters(): ?array
    {
        return array_reduce(range(2020, now()->year), function ($prev, $curr) {
            $prev[$curr] = $curr;
            return $prev;
        }, []);
    }

    protected function getData(): array
    {
        $this->filter = $this->filter ?:  now()->year;

        return [
            'datasets' =>
            Book::query()
                ->select('title', 'id')
                ->whereHas('borrows', function ($query) {
                    $query->whereYear('borrows.created_at', $this->filter);
                })
                ->with('borrows')
                ->withCount('borrows')
                ->orderBy('borrows_count', 'desc')
                ->get()
                ->map(function ($data) {
                    $color = '#' . str_pad(dechex(rand(0x000000, 0xFFFFFF)), 6, 0, STR_PAD_LEFT);
                    $data = [
                        'label' => $data->title,
                        'borderColor' => $color,
                        'backgroundColor' => $color,
                        'data' => $this->getSalesPerMonth($data->borrows()
                            ->whereYear('borrows.created_at', $this->filter)
                            ->selectRaw('DATE(borrows.created_at) as date, borrows.created_at, COUNT(*) as total')
                            ->groupByRaw("STRFTIME('%m-%Y', borrows.created_at)")
                            ->get())

                    ];

                    return $data;
                })
                ->all(),
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected static ?array $options = [
        'plugins' => [
            'scales' => [
                'y' => [
                    'min' => 0,
                    'max' => 30,
                    'ticks' => [
                        'minTicksLimit' => 10
                    ]
                ]
            ]
        ],
    ];

    private function getSalesPerMonth($collection)
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
}
