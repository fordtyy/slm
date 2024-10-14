<?php

namespace App\Livewire;

use App\Models\Category;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;

class CategoryPreferredPage extends Component implements HasForms
{

  use InteractsWithForms;


  public ?array $data = [];

  public function mount()
  {
    $this->form->fill();
  }

  public function render()
  {
    return view('livewire.category-preferred-page');
  }

  public function next() {
    return redirect()->route('filament.account.pages.author-preferred', ['data' => json_encode($this->data)]);
  }

  public function form(Form $form): Form
  {
    return $form
      ->schema([
        ToggleButtons::make('category')
          ->label('')
          ->options(
            Category::pluck('name', 'id')
          )
          ->inline()
          ->columnSpanFull()
          ->multiple(),
      ])
      ->statePath('data')
      ->columns(4);
  }
}
