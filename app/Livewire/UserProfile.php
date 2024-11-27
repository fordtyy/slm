<?php

namespace App\Livewire;

use App\Models\Author;
use App\Models\AuthorUser;
use App\Models\Category;
use App\Models\User;
use Exception;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Auth\VerifyEmail;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Auth\Authenticatable;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Joaopaulolndev\FilamentEditProfile\Concerns\HasSort;

class UserProfile extends Component implements HasForms
{
    use InteractsWithForms;
    use HasSort;

    public ?array $userData = [];
    public ?array $userPref = [];

    protected static int $sort = 0;

    public function mount(): void
    {
        $user = $this->getUser();

        $userData = $user->attributesToArray();
        $this->profileInformationForm->fill($userData);

        $userCatPref = $user->categoryPrefs->pluck('id')->toArray();
        $userAuthPref = $user->authorPrefs->pluck('id')->toArray();
        $this->userPreferencesForm->fill(
          [
            'categoryPrefs' => $userCatPref,
            'authorPrefs' => $userAuthPref,
          ]
        );

    }

    protected function getForms(): array
    {
        return [
            'profileInformationForm',
            'userPreferencesForm',
        ];
    }

    public function profileInformationForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Profile Information')
                    ->aside()
                    ->description('Update your account profile information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Full Name')
                            ->required()
                            ->maxLength(255)
                            ->autofocus(),
                        Forms\Components\TextInput::make('email')
                            ->label('Email Address')
                            ->required()
                            ->maxLength(50)
                            ->email(),
                        Forms\Components\TextInput::make('usn')
                            ->label('USN')
                            ->required()
                            ->maxLength(50)
                            ->visible(fn() => Filament::getCurrentPanel()->getId() === 'account'),
                        Forms\Components\Select::make('course_id')
                            ->label('Course')
                            ->required()
                            ->relationship('course', 'name')
                            ->native(false)
                            ->visible(fn() => Filament::getCurrentPanel()->getId() === 'account'),
                        Forms\Components\Select::make('year_level_id')
                            ->label('Year Level')
                            ->required()
                            ->relationship('yearLevel', 'name')
                            ->native(false)
                            ->visible(fn() => Filament::getCurrentPanel()->getId() === 'account'),
                    ]),
            ])
            ->statePath('userData')
            ->model(User::class);
    }

    public function userPreferencesForm(Form $form): Form
    {
      return $form
        ->schema([
          Section::make('User Preferences')
                    ->aside()
                    ->description('Update your account preferences')
                    ->schema([
                      Forms\Components\Select::make('categoryPrefs')
                        ->relationship(name: 'categoryPrefs', titleAttribute: 'name')
                        ->createOptionForm([
                          TextInput::make('name')
                        ])
                        ->label('Category Preferences')
                        ->placeholder('Choose your category preferences')
                        ->native(false)
                        ->searchable()
                        ->multiple()
                        ->preload()
                        ->maxItems(10),
                      Forms\Components\Select::make('authorPrefs')
                        ->relationship(name: 'authorPrefs', titleAttribute: 'name')
                        ->createOptionForm([
                          TextInput::make('name')
                        ])
                        ->label('Author Preferences')
                        ->placeholder('Choose your author preferences')
                        ->native(false)
                        ->searchable()
                        ->multiple()
                        ->preload()
                        ->maxItems(10),
                    ])
        ])
        ->statePath('userPref')
        ->model(User::class);
    }

    public function saveUserInformation(): void
    {
        $userData = $this->profileInformationForm->getState();

        $updatedUser = $this->handleRecordUpdate($this->getUser(), $userData);

        Notification::make()
            ->success()
            ->title('Profile Information successfully saved!')
            ->send();

        if (!$updatedUser->hasVerifiedEmail()) {

            $notification = app(VerifyEmail::class);
            $notification->url = Filament::getVerifyEmailUrl($updatedUser);

            $updatedUser->notify($notification);

            $panel = Filament::getCurrentPanel()->getId();

            redirect()->route("filament.$panel.auth.email-verification.prompt");
        }
    }

    public function saveUserPreference() : void
    {
      $this->handleRecordUpdate($this->getUser(), $this->userPref);

      Notification::make()
        ->success()
        ->title('User Preferences successfully saved!')
        ->send();
    }

    protected function handleRecordUpdate(Model $record, array $userData): Model
    {

        if (isset($userData['email']) && $userData['email'] != $record->email) {
            $userData['email_verified_at'] = null;
        }

        if (isset($userData['categoryPrefs'])) {
          $record->categoryPrefs()->sync($userData['categoryPrefs']);
        }

        if (isset($userData['authorPrefs'])) {
          $record->authorPrefs()->sync($userData['authorPrefs']);
        }

        $record->update($userData);

        return $record;

    }

    public function getUser(): Authenticatable & Model
    {
        $user = Filament::auth()->user();

        if (!$user instanceof Model) {
            throw new Exception('The authenticated user object must be an Eloquent model to allow the profile page to update it.');
        }

        return $user;
    }

    public function render(): View
    {
        return view('livewire.user-profile');
    }
}
