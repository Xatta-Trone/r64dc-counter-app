<x-mail::message>
# Hello,

The backup for {{$fileName}} not complete. Please check the error log. {{$error?: ''}}


Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
