<?php

namespace App\Filament\Account\Widgets;

use App\Enums\BorrowStatus;
use App\Models\Borrow;
use Filament\Forms\Components\Group;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class DatesOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected static ?string $pollingInterval = '15s';

    protected function getColumns(): int
    {
        return 2;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Today is', date("d M, Y"))
                ->color('success'),
            Stat::make('Total Due Request', Borrow::where('status', BorrowStatus::DUE)
                ->where('user_id', Auth::id())
                ->whereDate('due_date', '>=', now())->count())
                ->color('success')
        ];
    }
}
