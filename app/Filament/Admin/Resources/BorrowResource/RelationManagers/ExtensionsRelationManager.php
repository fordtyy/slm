<?php

namespace App\Filament\Admin\Resources\BorrowResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExtensionsRelationManager extends RelationManager
{
    protected static string $relationship = 'extensions';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('code')
            ->description('List of extensions requested.')
            ->columns([
                Tables\Columns\TextColumn::make('code'),
                Tables\Columns\TextColumn::make('number_of_days'),
                Tables\Columns\TextColumn::make('fee')->money('PHP'),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('reason'),
            ]);
    }
}
