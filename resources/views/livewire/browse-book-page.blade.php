<div>
    <div class="mb-5 bg-success">
        {{ $this->form }}
    </div>

    <div class="grid grid-cols-4 w-full gap-4">
        @foreach ($this->books as $book)
            <x-card wire:key="{{ $book->id }}-books" :book="$book" />
        @endforeach
    </div>

    {{-- <x-filament::modal id="borrow-modal">
        <x-slot name="heading">
            Confirmation
        </x-slot>

        <x-slot name="description">
            <p>Are you sure you want to borrow?</p>
        </x-slot>

        <x-slot name="footer">
            <div class="flex justify-end space-x-2">
                <x-filament::button
                  wire:click="closeModal"
                  size="sm"
                  color="gray"
                  @class([
                    'text-sm bg-grey'
                  ])>
                    Cancel
                </x-filament::button>
                <x-filament::button
                  wire:click="confirmBorrow"
                  color="primary"
                  size="sm"
                  @class([
                    'text-sm'
                  ])>
                    Confirm
                </x-filament::button>
            </div>
        </x-slot>
    </x-filament::modal> --}}

    <x-filament-actions::modals />
    <br>
    <div class="flex place-content-center">
        {{ $this->books->links() }}
    </div>
</div>
