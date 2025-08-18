@extends('layouts.app')

@section('title', 'Cập nhật thông tin cá nhân| Giasu707')

@section('content')
    <div class="card p-4">
        <div class="text-center">
            <img src="{{ asset('images/avatar.png') }}" class="rounded-circle shadow mb-3" width="150" alt="Ảnh đại diện">
        </div>
        <h2 class="text-center p-4">CẬP NHẬT THÔNG TIN CÁ NHÂN</h2>
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

        <form method="POST" action="{{ route('registration.store') }}">
            @csrf

            <div class="mb-3">
                <label for="name">Họ tên <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" class="form-control" required value="{{ $registration->name }}">
            </div>

            <div class="mb-3">
                <label for="phone">Số điện thoại <span class="text-danger">*</span></label>
                <input type="text" name="phone" id="phone" class="form-control" required value="{{ $registration->phone }}">
            </div>

            <div class="mb-3">
                <label for="email">Email <span class="text-danger">*</span></label>
                <input type="email" name="email" id="email" class="form-control"  value="{{ $registration->email }}">
            </div>

            <div class="mb-3">
                <label for="subject">Môn học muốn học <span class="text-danger">*</span></label>
                <input type="text" name="subject" id="subject" class="form-control" required value="{{ $registration->subject }}">
            </div>

            <div class="mb-3">
                <label for="note">Ghi chú thêm( Thông tin zalo, facebook,... để tiện nhắn tin trao đổi)</label>
                <textarea name="note" class="form-control" rows="3" id="note">{{$registration->note}}</textarea>
            </div>
            <input type="hidden" name="client_id" id="client_id">
            <button type="submit" class="btn btn-primary">Lưu cập nhật</button>
            <a href="{{route('registration.index')}}" class='btn btn-secondary'>Quay lại</a>
        </form>
    </div>
@endsection