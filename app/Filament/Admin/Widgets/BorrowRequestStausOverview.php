<?php

namespace App\Filament\Admin\Widgets;

use App\Enums\BorrowStatus;
use App\Models\Borrow;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BorrowRequestStausOverview extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 4;

    protected function getColumns(): int
    {
        return 1;
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
        $icon =   $isIncreasing ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down';
        return [
            Stat::make('Pending Borrowed Requests Today', Borrow::whereDate('created_at', now()->today())
                ->whereStatus(BorrowStatus::PENDING->value)
                ->count())
                ->icon('heroicon-o-bookmark')
                ->color($isIncreasing ? 'success' : 'danger')
                ->description($isIncreasing ? 'Increasing' : 'Decreasing')
                ->descriptionIcon($icon)
                ->chart([2, 4, 3, 8, 6, $isIncreasing ? 2 : 1]),
            Stat::make('Due Borrowed Requests Today', Borrow::whereDate('due_date', now()->today())
                ->count())
                ->color($this->isIncreasing([
                    Borrow::whereDate('due_date', now()->subDay()->day())
                        ->count(),
                    Borrow::whereDate('due_date', now()->today())
                        ->count()
                ]) ? 'success' : 'danger')
                ->icon('heroicon-o-clock')
                ->description($this->isIncreasing([
                    Borrow::whereDate('due_date', now()->subDay()->day())
                        ->count(),
                    Borrow::whereDate('due_date', now()->today())
                        ->count()
                ]) ? 'Increasing' : 'Decreasing')
                ->descriptionIcon($icon)
                ->chart([2, 4, 3, 8, 6, Borrow::whereDate('due_date', now()->subDay()->day())
                    ->count(), Borrow::whereDate('due_date', now()->today())
                    ->count()]),
        ];
    }

    private function isIncreasing(array $data): bool
    {
        if(empty($data) || sizeof($data) == 1) {
            return false;
        }

        [$first, $second] =  array_slice($data, -2);

        if ((!$first && !$second) || ($first && !$second)) {
            return false;
        }

        return $second > $first;
    }
}
