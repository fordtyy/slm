<div>
    <div class="mb-5 bg-success">
        {{ $this->form }}
    </div>
    <div class="mt-5 flex">
        <x-filament::button size="xl" class="ml-auto text-white" wire:click="next">
            Next
        </x-filament::button>
    </div>
</div>
