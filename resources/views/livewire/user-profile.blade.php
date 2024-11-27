<div>
  <div class="pb-6">
    <x-filament-panels::form wire:submit="saveUserInformation">
        {{ $this->profileInformationForm }}

        <div class="fi-form-actions">
            <div class="flex flex-row-reverse flex-wrap items-center gap-3 fi-ac">
                <x-filament::button type="submit">
                    {{ __('filament-edit-profile::default.save') }}
                </x-filament::button>
            </div>
        </div>
    </x-filament-panels::form>
  </div>

  <div class="">
    <x-filament-panels::form wire:submit="saveUserPreference">
        {{ $this->userPreferencesForm }}

        <div class="fi-form-actions">
            <div class="flex flex-row-reverse flex-wrap items-center gap-3 fi-ac">
                <x-filament::button type="submit">
                    {{ __('filament-edit-profile::default.save') }}
                </x-filament::button>
            </div>
        </div>
    </x-filament-panels::form>
  </div>
    

    <x-filament-actions::modals />
</div>
