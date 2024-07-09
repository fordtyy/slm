<?php

namespace App\Filament\Account\Widgets;

use App\Filament\Account\Resources\BorrowResource;
use App\Models\Borrow;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PendingBorrowRequests extends BaseWidget
{

    protected static ?int $sort = 2;
    protected static ?string $pollingInterval = '15s';
    protected int | string | array $columnSpan = 'full';


    public function table(Table $table): Table
    {
        return $table
            ->query(BorrowResource::getEloquentQuery())
            ->defaultPaginationPageOption(5)
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('books.title'),
                Tables\Columns\TextColumn::make('created_at')
                    ->date()
                    ->label('Submitted At'),

            ]);
    }
}
