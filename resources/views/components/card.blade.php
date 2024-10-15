<div class="max-w-sm bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
    <a href="#">
        <img class="rounded-t-lg h-56 w-full" src="{{ asset('storage/' . $book->cover) }}" alt="{{ $book->title }}" />
    </a>
    <div class="h-full">
        <div class="p-5 flex-grow flex flex-col justify-between">
            <div>
                <div class="relative group">
                    <h5 id="book-title-{{ $book->id }}"
                        class="mb-2 text-xl font-bold tracking-tight text-gray-900 dark:text-white truncate"
                        onmousemove="checkOverflow(event, '{{ $book->title }}', {{ $book->id }})"
                        onmouseleave="hideTooltip({{ $book->id }})">
                        {{ $book->title }}
                    </h5>

                    <div id="tooltip-{{ $book->id }}"
                        class="absolute hidden px-4 py-2 bg-gray-800 text-white text-sm rounded-md shadow-lg whitespace-nowrap z-50 pointer-events-none">
                        {{ $book->title }}
                    </div>
                </div>
                <div class="mb-1">Tags:</div>
                <div class="flex gap-3 mb-3">
                    @foreach ($book->tags as $tag)
                        <x-filament::badge>{{ $tag->name }}</x-filament::badge>
                    @endforeach
                </div>
            </div>
            <div class="grid grid-cols-6 gap-2">
                <x-filament::button wire:click="borrowBook({{ $book->id }})" @class([
                    'col-span-5 text-sm card-button-borrow',
                    'col-span-6 card-button-borrow' => $this->addedToWishList($book),
                ])>
                    Borrow
                </x-filament::button>
                @if (!$this->addedToWishList($book))
                    <x-filament::button color="gray" :visible="false"
                        wire:click="addToWishList({{ $book->id }})" icon-color="danger" icon="heroicon-o-heart"
                        class="col-span-1 bg-green"></x-filament::button>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    function checkOverflow(event, title, id) {
        const titleElement = document.getElementById('book-title-' + id);
        const isOverflowing = titleElement.scrollWidth > titleElement.clientWidth;
        if (isOverflowing) {
            showTooltip(event, title, id);
        }
    }

    function showTooltip(event, title, id) {
        const tooltip = document.getElementById('tooltip-' + id);
        tooltip.style.top = (document.getElementById('book-title-' + id).offsetTop - 40) + 'px';
        tooltip.classList.remove('hidden');
    }

    function hideTooltip(id) {
        const tooltip = document.getElementById(`tooltip-${id}`);
        tooltip.classList.add('hidden');
    }
</script>
