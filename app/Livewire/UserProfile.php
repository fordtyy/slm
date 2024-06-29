<?php

namespace App\Livewire;

use App\Models\User;
use Exception;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Section;
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

    public ?array $data = [];

    protected static int $sort = 0;

    public function mount(): void
    {
        $data = $this->getUser()->attributesToArray();

        $this->form->fill($data);
    }

    public function form(Form $form): Form
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
            ->statePath('data')
            ->model(User::class);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $updatedUser = $this->handleRecordUpdate($this->getUser(), $data);

        Notification::make()
            ->success()
            ->title('Successfully saved!')
            ->send();

        if (!$updatedUser->hasVerifiedEmail()) {

            $notification = app(VerifyEmail::class);
            $notification->url = Filament::getVerifyEmailUrl($updatedUser);

            $updatedUser->notify($notification);

            $panel = Filament::getCurrentPanel()->getId();

            redirect()->route("filament.$panel.auth.email-verification.prompt");
        }
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if ($data['email'] != $record->email) {
            $data['email_verified_at'] = null;
        }

        $record->update($data);

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
