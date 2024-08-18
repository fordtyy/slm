<x-filament::section id="{{ $record->getKey() }}">
    <x-slot name="heading">
        {{ $record->code }}
    </x-slot>

    <x-slot name="headerEnd">
        <x-filament::icon-button id="{{ $record->getKey() }}-{{ $loop->index }}"
            wire:click="viewDetails({{ $loop->index }})" icon="heroicon-m-eye" size="xs" label="New label"
            tooltip="View Details" />
    </x-slot>
    <div class="flex items-center mb-2 text-gray-900 whitespace-nowrap dark:text-white">
        <img class="w-10 h-10 rounded-full" src="/images/book_logo.png" alt="Jese image">
        <div class="ps-3">
            <div class="text-base font-semibold">{{ $record->user->name }}</div>
            <div class="font-normal text-gray-500">{{ $record->user->email }}</div>
        </div>
    </div>
    <div>
        Created At: {{ $record->created_at->format('d M, Y') }}
    </div>
</x-filament::section>

<x-filament::modal id="view-borrow-details-{{ $loop->index }}" slide-over width="4xl">
    <x-slot name="heading">
        Borrow Request Details
    </x-slot>
    <x-filament::section>
        <x-slot name="heading">
            Borrower Information
        </x-slot>
        <div>
            Name : {{ $record->user->name }}
        </div>
    </x-filament::section>
    <x-filament::section>
        <x-slot name="heading">
            Books Information
        </x-slot>
        <div>
            @foreach ($record->books as $book)
                {{ $book->label }}
            @endforeach
        </div>
    </x-filament::section>
    <x-slot name="footerActions">
        {{-- Modal footer actions --}}
    </x-slot>
</x-filament::modal>
