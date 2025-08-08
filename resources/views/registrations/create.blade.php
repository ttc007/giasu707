@extends('layouts.app')

@section('title', 'Đăng kí học online | Giasu707')

@section('content')
    <div class="card p-4">
        
        
        <h2>Đăng ký học online</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('registration.store') }}">
            @csrf

            <div class="mb-3">
                <label for="name">Họ tên *</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="phone">Số điện thoại *</label>
                <input type="text" name="phone" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="email">Email (không bắt buộc)</label>
                <input type="email" name="email" class="form-control">
            </div>

            <div class="mb-3">
                <label for="subject">Môn học muốn học *</label>
                <input type="text" name="subject" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="note">Ghi chú thêm( Thông tin zalo, facebook,... để tiện nhắn tin trao đổi)</label>
                <textarea name="note" class="form-control" rows="3"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Gửi đăng ký</button>
        </form>
    </div>
    
@endsection