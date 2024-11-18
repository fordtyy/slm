<div>
    <section class="bg-white dark:bg-gray-900 pt-24 lg:pt-48 pb-16">
        <div class="grid max-w-screen-xl px-4 py-8 mx-auto lg:gap-8 xl:gap-0 lg:py-16 lg:grid-cols-12">
            <div class="mr-auto place-self-center lg:col-span-7">
                <h1
                    class="max-w-2xl mb-4 text-4xl font-extrabold tracking-tight leading-none md:text-5xl xl:text-6xl dark:text-white">
                    Streamlining Your Library Experience
                </h1>
                <p class="max-w-2xl mb-6 font-light text-gray lg:mb-8 md:text-lg lg:text-xl dark:text-gray-400">
                    Streamlining Your Library Experience”, offers an intuitive interface with smart search and digital
                    access to resources. It provides automated notifications, data analytics for librarians, and
                    supports sustainability, enhancing the overall library experience.
                </p>
                <a href="#books"
                    class="inline-flex items-center justify-center px-5 py-3 mr-3 text-base font-medium text-center text-white rounded-lg bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 dark:focus:ring-primary-900">
                    Get started
                    <svg class="w-5 h-5 ml-2 -mr-1" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                            clip-rule="evenodd"></path>
                    </svg>
                </a>
            </div>
            <div class="hidden lg:mt-0 lg:col-span-5 lg:flex">
                <img src="{{ asset('/images/book.svg') }}" alt="mockup" />
            </div>
        </div>
    </section>

    <section id="books" class="bg-white dark:bg-gray-900 py-16 lg:py-24">
        <div id="child-books" class="py-8 px-4 mx-auto max-w-screen-xl lg:py-16 lg:px-6">
            <div class="mx-auto max-w-screen-md text-center mb-8 lg:mb-12">
                <h2 class="mb-4 text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white">
                    Explore books that fits to your needs
                </h2>
                <p class="mb-5 font-light text-gray-500 sm:text-xl dark:text-gray-400">
                    We show you what most interest you and helps you in getting better.
                </p>
            </div>
            <div class="container">
                @if (count($this->trending) > 0)
                    <div class="my-16">
                        <div>
                            <h3 class="text-2xl font-bold">Trending Books</h3>
                        </div>
                        <div>
                            <div
                                class="flex overflow-x-auto overflow-y-hidden space-x-6 scrollbar-thin scrollbar-thumb-blue-500 scrollbar-track-gray-200 py-16">
                                @foreach ($this->trending as $book)
                                    <div
                                        class="flex-shrink-0 w-full sm:w-1/2 md:w-1/3 lg:w-1/4 max-w-sm rounded-xl overflow-hidden shadow-xl bg-white transform transition-all duration-300 hover:scale-105">
                                        <x-card wire:key="{{ $book->id }}-books" :book="$book" />
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
                @if (count($this->books) > 0)
                    <div class="my-16">
                        <div>
                            <h3 class="text-2xl font-bold">Most Borrowed Books</h3>
                        </div>
                        <div>
                            <div
                                class="flex overflow-x-auto overflow-y-hidden space-x-6 scrollbar-thin scrollbar-thumb-blue-500 scrollbar-track-gray-200 py-16">
                                @foreach ($this->books as $book)
                                    <div
                                        class="flex-shrink-0 w-full sm:w-1/2 md:w-1/3 lg:w-1/4 max-w-sm rounded-xl overflow-hidden shadow-xl bg-white transform transition-all duration-300 hover:scale-105">
                                        <x-card wire:key="{{ $book->id }}-books" :book="$book" />
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
                @if (count($this->youMayLike) > 0)
                    <div class="mt-16">
                        <div>
                            <h3 class="text-2xl font-bold">Books You May Like</h3>
                        </div>
                        <div>
                            <div
                                class="flex overflow-x-auto overflow-y-hidden space-x-6 scrollbar-thin scrollbar-thumb-blue-500 scrollbar-track-gray-200 py-16">
                                @foreach ($this->youMayLike as $book)
                                    <div
                                        class="flex-shrink-0 w-full sm:w-1/2 md:w-1/3 lg:w-1/4 max-w-sm rounded-xl overflow-hidden shadow-xl bg-white transform transition-all duration-300 hover:scale-105">
                                        <x-card wire:key="{{ $book->id }}-books" :book="$book" />
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
    <section class="mt-8 lg:mt-16 px-8 lg:px-24 py-16 lg:py-32 bg-gray-50 rounded-lg shadow-md">
        <div class="flex flex-col md:flex-row items-center md:items-center md:space-x-8">
            <!-- Image Placeholder -->
            <div class="mt-6 md:mt-0 md:w-1/3 hidden lg:flex justify-center">
                <img src="{{ asset('images/browsing.svg') }}" alt="Explore more books"
                    class="w-full h-auto rounded-full object-cover">
            </div>
            <!-- Text Content Centered Vertically -->
            <div class="lg:text-center  md:text-left space-y-6 md:w-2/3">
                <div class="space-y-2">
                    <h2 class="text-2xl font-bold text-gray-800">Not finding what you're looking for?</h2>
                    <p class="text-gray-600">
                        If the suggested books don’t meet your needs, explore more options to find the perfect read for
                        you.
                    </p>
                </div>
                <x-filament::button size="xl" href="{{ route('public-browse-books') }}" tag="a"
                    icon="heroicon-o-arrow-right" icon-position="after">
                    Discover More Books
                </x-filament::button>
            </div>

        </div>
    </section>
</div>
