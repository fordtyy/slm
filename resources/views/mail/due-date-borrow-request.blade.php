<x-mall::message>
# Borrow Request Status

We want to inform you that your borrowed book is now past due date and will need to pay for the penalty.

Request Code: **{{ $request->code }}**

Amount to be paid: ** {{ $request->extension->amount }} **


### Book(s) Details


---
<x-mail::table>
| Title | Author | Category |
| :----------------- | :----------------------- | :-------------------------- |
@foreach ($request->books as $book)
| {{ $book->title }} | {{ $book->authorsName }} | {{ $book->category->name }} |
@endforeach
</x-mail::table>

You can pay the fine using Gcash:

![gcash](./public/images/gcash.jpg)


**Note:** Fail to comply may result in blocking the access of the system.

Regards,<br>
{{ config('app.name') }}
</x-mall::message>
