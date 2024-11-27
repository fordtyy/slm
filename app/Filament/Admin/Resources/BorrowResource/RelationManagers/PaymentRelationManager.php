<?php

namespace App\Filament\Admin\Resources\BorrowResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('code')
            ->columns([
                Tables\Columns\TextColumn::make('code')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('reference')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('source_code')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('amount'),
                Tables\Columns\TextColumn::make('method'),
                Tables\Columns\TextColumn::make('paid_at')
                    ->sortable()
                    ->date(),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
            ]);
    }
}
