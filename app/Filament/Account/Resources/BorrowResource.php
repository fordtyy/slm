<?php

namespace App\Filament\Account\Resources;

use App\Filament\Account\Resources\BorrowResource\Pages;
use App\Filament\Account\Resources\BorrowResource\RelationManagers;
use App\Models\Borrow;
use App\Services\BorrowService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BorrowResource extends Resource
{
    protected static ?string $model = Borrow::class;

    protected static ?string $navigationGroup = 'Request';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('books.title')
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                  ->color('white')
                  ->formatStateUsing(function ($record) {
                    if ($record->status->value === 'Cancel') {
                      return 'Cancelled';
                    } else {
                      return $record->status->value;
                    }
                  }),
                Tables\Columns\TextColumn::make('created_at')
                    ->date()
                    ->label('Submitted At')
            ])
            ->filters([
                //
            ])
            ->actions([
              Tables\Actions\Action::make('Cancel')
              ->visible(fn ($record) => $record->status->value == 'Pending')
              ->label('Cancel')
              ->requiresConfirmation()
              ->color('danger')
              ->action( function ($record) {
                 BorrowService::updateStatus($record, 'Cancel');
                 Notification::make()
                          ->success()
                          ->title('Book request successfully cancelled!')
                          ->send();
              })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBorrows::route('/'),
            'create' => Pages\CreateBorrow::route('/create'),
            'edit' => Pages\EditBorrow::route('/{record}/edit'),
        ];
    }
}
