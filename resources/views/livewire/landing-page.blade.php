<div>
    <section class="bg-white dark:bg-gray-900 py-24">
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
                <img src="/images/book.svg" alt="mockup" />
            </div>
        </div>
    </section>
    <section id="books" class="bg-white dark:bg-gray-900">
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
              <div>
                <div>
                  <h3 class="text-2xl font-bold">Most Borrowed Books</h3>
                </div>
                <div>
                    <div class="scroll-container py-4 ">
                      @foreach ($this->books as $book)
                        <div class="scroll-card p-4 mt-4">
                          <x-card wire:key="{{ $book->id }}-books" :book="$book" />
                      </div>
                    @endforeach
                    </div>
                </div>
              </div>
              <div class="mt-5">
                <div>
                  <h3 class="text-2xl font-bold">Books You May Like</h3>
                </div>
                <div>
                    <div class="scroll-container py-4">
                      @foreach ($this->youMayLike as $book)
                        <div class="scroll-card p-4 mt-1">
                          <x-card wire:key="{{ $book->id }}-books" :book="$book" />
                      </div>
                    @endforeach
                    </div>
                </div>
              </div>
            </div>
        </div>
    </section>
</div>
