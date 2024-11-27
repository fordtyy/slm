<div>
  <div class="mb-5 bg-success">
      {{ $this->form }}
  </div>
  <div class="mt-5 flex">
      <x-filament::button size="xl" class="ml-auto text-white" wire:click="save">
          Start Browsing
      </x-filament::button>
  </div>
</div>
