<div>
    @if (count($this->books) > 0)
        <div class="mb-5 bg-success">
            {{ $this->form }}
        </div>

        <div class="grid grid-cols-4 w-full gap-4">
            @foreach ($this->books as $book)
                <x-card wire:key="{{ $book->id }}-books" :book="$book" />
            @endforeach
        </div>

        <x-filament-actions::modals />

        <br>
        
        <div class="flex place-content-center">
            {{ $this->books->links() }}
        </div>
    @else
        <style>
            .fi-header { display: none; }
            section { padding-top: 0 !important; }
        </style>
        <x-blocked-page></x-blocked-page>
    @endif
</div>
