<?php

namespace App\Filament\Auth\Pages;

use App\Enums\UserType;
use App\Models\User;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Register as BaseRegister;

class Registration extends BaseRegister
{
    
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getUsnComponent(),
                        $this->getCourseYearLevelComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ])
                    ->model(User::class)
                    ->statePath('data'),
            ),
        ];
    }

    public function mutateFormDataBeforeRegister(array $data): array
    {
        $data['type'] = UserType::STUDENT->value;
        return $data;
    } 

    protected function getUsnComponent(): Component
    {
        return TextInput::make('usn')
            ->required()
            ->label('USN')
            ->unique();
    }

    protected function getCourseYearLevelComponent(): Component
    {
        return Group::make([
            $this->getCourseComponent(),
            $this->getYearLevelComponent(),
        ])
        ->columns(5);
    }

    protected function getCourseComponent(): Component
    {
        return Select::make('course_id')
            ->required()
            ->label('Course')
            ->relationship('course', 'name')
            ->native(false)
            ->columnSpan(3)
            ->searchable()
            ->preload();
    }

    public function getYearLevelComponent(): Component
    {
        return Select::make('year_level_id')
            ->required()
            ->label('Year Level')
            ->native(false)
            ->relationship('yearLevel', 'name')
            ->columnSpan(2);
    }


}
