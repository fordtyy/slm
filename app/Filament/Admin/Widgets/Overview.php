<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Borrow;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class Overview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected static ?string $pollingInterval = '15s';
    protected int | string | array $columnSpan = 'full';

    protected function getColumns(): int
    {
        return 2;
    }

    protected function getStats(): array
    {
        $borrowData = Borrow::query()
            ->selectRaw("DATE(created_at) as date, COUNT(*) as total")
            ->whereBetween('date', [
                now()->startOfMonth(),
                now()->endOfMonth()
            ])
            ->groupBy('date')
            ->get()
            ->map(fn($data) => $data->total)
            ->all();
        $isIncreasing = $this->isIncreasing($borrowData);
        $icon =   $isIncreasing ? 'heroicon-m-arrow-trending-up': 'heroicon-m-arrow-trending-down';
        return [
            Stat::make('Welcome, have a nice day!', Auth::user()->name)
                ->color('success'),
            Stat::make('Today is', date("d M, Y"))
                ->color('success')
        ];
    }

    private function isIncreasing(array $data): bool
    {
        if(empty($data) || sizeof($data) == 1) {
            return false;
        }

        [$first, $second] =  array_slice($data, -2);

        if (!$first || ($first && !$second)) {
            return false;
        }

        return $second > $first;
    }
}
