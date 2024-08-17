<?php

namespace App\Filament\Account\Resources\BorrowResource\Pages;

use App\Filament\Account\Resources\BorrowResource;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Http\Request;

class CreateBorrow extends CreateRecord
{
  protected static string $resource = BorrowResource::class;

  public $parameterId;

  public function mount(): void
  {
    parent::mount();

    $this->parameterId = request()->query('id');

    $this->form->fill([
        'books' => $this->parameterId,
    ]);
  }

  public function form(Form $form): Form
    {
        return $form->schema([
          Select::make('books')
          ->relationship(name: 'books', titleAttribute: 'title')
          ->preload()
          ->native(false)
        ]);
    }

  protected function mutateFormDataBeforeCreate(array $data): array
  {
    $data['user_id'] = auth()->id();

    return $data;
  }
}
