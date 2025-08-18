@extends('layouts.app')

@section('title', 'Đăng nhập | Giasu707')

@section('content')
    <div class="section p-4 t d-flex justify-content-center align-items-center" style="min-height: 80vh;">
         <form method="POST" action="{{ route('student.login.post') }}" style="max-width:450px; width: 100%;" class="card p-3">
            <div class="text-center">
                <img src="{{ asset('images/avatar.png') }}" class="rounded-circle shadow mb-3" width="150" alt="Ảnh đại diện">
            </div>
            <h2 class="text-center p-4">ĐĂNG NHẬP</h2>
            
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                @error('email')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Mật khẩu</label>
                <input type="password" class="form-control" name="password" required>
                @error('password')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary">Đăng nhập</button>
                <a href="{{ route('student.register') }}" class="btn btn-outline-secondary">Đăng ký nhanh</a>
            </div>
        </form>
    </div>
@endsection
