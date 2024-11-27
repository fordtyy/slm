<?php

namespace App\Livewire;

use App\Models\Author;
use Exception;
use Filament\Facades\Filament;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class AuthorPreferredPage extends Component implements HasForms
{
  use InteractsWithForms;

  public ?array $authors = [];
  public array $categories = [];

  public function mount()
  {
    $this->form->fill();

    $this->categories = json_decode(request()->query('data'), true);
  }

  public function render()
  {
    return view('livewire.author-preferred-page');
  }

  public function save()
  {
    $this->handleRecordUpdate($this->getUser());
  }

  public function form(Form $form): Form
  {
    return $form
      ->schema([
        ToggleButtons::make('author')
          ->label('')
          ->options(
            Author::pluck('name', 'id')
          )
          ->inline()
          ->columnSpanFull()
          ->multiple(),
      ])
      ->statePath('authors')
      ->columns(4);
  }

  protected function handleRecordUpdate(Model $record)
  {
    $userData = $this->finalizeValues([$this->categories, $this->authors]);

    if (isset($this->categories)) {
      $record->categoryPrefs()->sync($userData['categoryPrefs']);
    }

    if (isset($this->authors)) {
      $record->authorPrefs()->sync($userData['authorPrefs']);
    }

    $record->update($userData);

    return redirect()->route('filament.account.pages.browse-books');
  }

  public function getUser(): Authenticatable & Model
  {
      $user = Filament::auth()->user();

      if (!$user instanceof Model) {
          throw new Exception('The authenticated user object must be an Eloquent model to allow the profile page to update it.');
      }

      return $user;
  }

  public function finalizeValues(array $data): array
  {
    $finalValue = [];
    foreach ($data as $item) {
      if (isset($item['category'])) {
          $finalValue['categoryPrefs'] = $item['category'];
      }
      
      if (isset($item['author'])) {
          $finalValue['authorPrefs'] = $item['author'];
      }
    }

    return $finalValue;
  }
}
