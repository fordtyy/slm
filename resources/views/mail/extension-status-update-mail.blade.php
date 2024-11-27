<x-mail::message>
# Extension Request {{ $request->status->value }}

We want to inform you that your extension request has been  {{ str($request->status->value)->lower()  }}.

Request Code: **{{ $request->code }}**

### Book(s) Details

---
<x-mail::table>
| Title | Author | Category |
| :----------------- | :----------------------- | :-------------------------- |
@foreach ($request->borrow->books as $book)
| {{ $book->title }} | {{ $book->authorsName }} | {{ $book->category->name }} |
@endforeach
</x-mail::table>

---
@if ($request->status->value === 'Pending')
Prepare an amount of {{ $request->fee }} to pay the {{ $request->number_of_days->getLabel() }} extension.
This request will be review after payment.
@elseif ($request->status->value === 'Payment Submitted')
This request is now ready to review by admin. Just wait for another email for the result if request is approved or rejected.
@elseif ($request->status->value === 'Approved')
The related borrow request due date will be extended with another {{ $request->number_of_days->getLabel() }}.
@endif

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
