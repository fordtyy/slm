<div
    class="rounded-xl overflow-hidden shadow-xl bg-white dark:bg-gray-800 transform transition-all duration-300 hover:scale-105">
    <img class="w-full h-64 object-cover" src="{{ asset('storage/' . $book->cover) }}" alt="{{ $book->title }}">
    <div class="p-6">
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white hover:text-primary-600 transition-colors duration-300 relative overflow-hidden whitespace-nowrap text-ellipsis"
            title="{{ $book->title }}">
            {{ $book->title }}
        </h2>
        <p class="text-sm text-gray-500 mt-1">Category: {{ $book->category->name }}</p>
        <p class="text-sm text-gray-500 mt-2">Authors: {{ $book->authorsName }}</p>
        <div class="flex my-3 space-x-2">
            @foreach ($book->tags as $tag)
                <x-filament::badge>{{ $tag->name }}</x-filament::badge>
            @endforeach
        </div>
        <div class="flex space-x-2">
            {{ ($this->borrowAction)(['book' => $book->id]) }}
            @if (!$this->addedToWishList($book->id))
                {{ ($this->addToWishListAction)(['book' => $book->id]) }}
            @endif
        </div>
    </div>
</div>

<script>
    function checkOverflow(event, title, id, isBook) {
        const titleElement = isBook ? document.getElementById('book-title-' + id) : document.getElementById(
            'author-name-' + id);
        const isOverflowing = titleElement.scrollWidth > titleElement.clientWidth;
        if (!isBook) {
            showTooltip(event, title, id, isBook);
        } else {
            if (isOverflowing) {
                showTooltip(event, title, id, isBook);
            }
        }
    }

    function showTooltip(event, title, id, isBook) {
        const tooltip = isBook ? document.getElementById('tooltip-book-' + id) : document.getElementById(
            'tooltip-author-' + id);
        tooltip.style.top = isBook ? (document.getElementById('book-title-' + id).offsetTop - 40) + 'px' : (document
            .getElementById('author-name-' + id).offsetTop - 40) + 'px';
        tooltip.classList.remove('hidden');
    }

    function hideTooltip(id, isBook) {
        const tooltip = isBook ? document.getElementById('tooltip-book-' + id) : document.getElementById(
            'tooltip-author-' + id);
        tooltip.classList.add('hidden');
    }
</script>
