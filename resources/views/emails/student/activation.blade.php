@component('mail::message')
# Chào {{ $student->name }}

Cảm ơn bạn đã đăng ký. Vui lòng nhấn nút bên dưới để kích hoạt tài khoản:

@component('mail::button', ['url' => route('student.activate', $student->activation_key)])
Kích hoạt tài khoản
@endcomponent

Cảm ơn,<br>
{{ config('app.name') }}
@endcomponent
