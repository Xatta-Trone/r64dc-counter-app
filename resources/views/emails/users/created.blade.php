<x-mail::message>
# Hello {{$user->name}}

Your account has been created. Your password is `{{$password}}`

<x-mail::button url="{{route('login')}}">
Login
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
