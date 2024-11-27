<x-mail::message>
# Borrow Request {{ $request->status->value }}

We want to inform you that your borrow request has been  {{ str($request->status->value)->lower()  }}.

Request Code: **{{ $request->code }}**

### Book(s) Details


---
<x-mail::table>
| Title | Author | Category |
| :----------------- | :----------------------- | :-------------------------- |
@foreach ($request->books as $book)
| {{ $book->title }} | {{ $book->authorsName }} | {{ $book->category->name }} |
@endforeach
</x-mail::table>

---

@if ($request->status->value == 'Released')
Please return the books by the due date **({{ $request->due_date?->format('Y-m-d') }})** to avoid any late fees.
If you have any questions or need assistance, feel free to reach out.

Happy reading!
@endif

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
