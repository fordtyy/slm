<?php

namespace App\Filament\Admin\Resources;

use App\Enums\BlockStatus;
use App\Enums\UserType;
use App\Filament\Admin\Resources\StudentResource\Pages;
use App\Filament\Admin\Resources\StudentResource\RelationManagers;
use App\Mail\StatusBlockMail;
use App\Models\Student;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class StudentResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $modelLabel = 'Student';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->where('type', UserType::STUDENT))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->description(fn($record) => $record->email),
                Tables\Columns\TextColumn::make('yearLevel.name'),
                Tables\Columns\TextColumn::make('course.name'),
                Tables\Columns\TextColumn::make('usn')
                    ->label('USN'),
                Tables\Columns\TextColumn::make('blocked_at')
                    ->date()
            ])
            ->filters([])
            ->actions([
                Tables\Actions\Action::make('block')
                    ->label('Block')
                    ->requiresConfirmation()
                    ->visible(fn($record) => is_null($record->blocked_at))
                    ->action(function (User $record) {
                        $record->update([
                            'blocked_at' => now()
                        ]);

                        Mail::to($record->email)->send(new StatusBlockMail($record, BlockStatus::BLOCKED));

                        Notification::make()
                            ->success()
                            ->title('Blocked Success')
                            ->body($record->name . ' cannot use the system anymore!')
                            ->sendToDatabase(Auth::user())
                            ->send();
                        Notification::make()
                            ->danger()
                            ->title('You been Blocked')
                            ->body('Admin blocked your access to the system')
                            ->sendToDatabase($record);
                    }),
                Tables\Actions\Action::make('unblock')
                    ->label('Unblock')
                    ->visible(fn($record) => !is_null($record->blocked_at))
                    ->requiresConfirmation()
                    ->action(function (User $record) {
                        $record->update([
                            'blocked_at' => null
                        ]);

                        Mail::to($record->email)->send(new StatusBlockMail($record, BlockStatus::UNBLOCKED));

                        Notification::make()
                            ->success()
                            ->title('Unblocked Success')
                            ->body($record->name . ' can use the system again!')
                            ->sendToDatabase(Auth::user())
                            ->send();
                        Notification::make()
                            ->info()
                            ->title('You been Unblocked')
                            ->body('Admin give back your access to the system')
                            ->sendToDatabase($record);
                    }),
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
            'index' => Pages\ListStudents::route('/'),
        ];
    }
}
