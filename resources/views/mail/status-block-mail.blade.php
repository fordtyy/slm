<x-mail::message>
👋 Hi, {{ $name }}.

We want to inform you that you been {{ $status}}.

@if ($status->value === 'Blocked')
<p class="">Temporarily you will not have access to the application as you are being blocked by the administrator or your have unresolved penalties.</p>
<p>Please go to the Library or contact administrator for more information.</p>
@elseif ($status->value === 'Unblocked')
The administrator granted again your access to the system.
You may use again the system.
@endif

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
