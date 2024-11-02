<x-mail::message>
# Request Due Date Reminder

Hi {{ $borrow->user->name}},

We would to inform you that your borrowed request due date is on {{ $borrow->due_date->format('Y-m-d') }}.
Kindly return the borrowed before the said due date to avoid penalties.

## Request Details

Request Code: **{{ $borrow->code }}**

### Book(s) Details

---
<x-mail::table>
| Title | Author | Category |
| :----------------- | :----------------------- | :-------------------------- |
@foreach ($borrow->books as $book)
| {{ $book->title }} | {{ $book->authorsName }} | {{ $book->category->name }} |
@endforeach
</x-mail::table>

---



Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
