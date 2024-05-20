<x-mail::message>
# Успешная регистрация

Здравствуйте, {{$user->first_name}} {{$user->last_name}}!

Спешим сообщить вам, что вы были успешно добавлены в систему по работе с экспериментами.

<x-mail::panel>
Ваш пароль: {{$password}}
</x-mail::panel>

Рекомендуем сменить его после первого входа из-за соображений безопасности.

По ссылке ниже вы можете войти в систему.

<x-mail::button :url="config('app.frontend_url') . '/login'">
Войти в систему
</x-mail::button>

С уважением,<br>
{{ config('app.name') }}
</x-mail::message>
