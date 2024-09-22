<div class="max-w-screen-xl px-4 mx-auto lg:gap-8 xl:gap-0 lg:py-16 pt-64 mt-24">
    <div class="mb-5 bg-success">
        {{ $this->form }}
    </div>

    <div class="grid grid-cols-4 w-full gap-4">
        @foreach ($this->getFilteredBooksProperty as $book)
            <x-card wire:key="{{ $book->id }}-books" :book="$book" />
        @endforeach
    </div>

    <br>
    <div class="flex place-content-center mb-6">
        {{ $this->getFilteredBooksProperty->links() }} 
    </div>
</div>
