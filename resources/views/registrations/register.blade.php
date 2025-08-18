@extends('layouts.app')

@section('title', 'Đăng kí học sinh | Giasu707')

@section('content')
<div class="section p-4 t d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <form method="POST" action="{{ route('student.register.post') }}" style="max-width:450px; width: 100%;" class="card p-3 shadow">
        <div class="text-center">
            <img src="{{ asset('images/avatar.png') }}" class="rounded-circle shadow mb-3" width="150" alt="Ảnh đại diện">
        </div>
        <h2 class="text-center p-4">ĐĂNG KÝ</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Họ tên</label>
            <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Mật khẩu</label>
            <input type="password" class="form-control" name="password" required>
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Xác nhận mật khẩu</label>
            <input type="password" class="form-control" name="password_confirmation" required>
        </div>

        <button type="submit" class="btn btn-success w-100">Đăng ký</button>
        <a href="{{ route('student.login') }}" class="btn btn-outline-primary w-100 mt-2">Đăng nhập</a>
    </form>
</div>
@endsection
