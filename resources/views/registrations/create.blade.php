@extends('layouts.app')

@section('title', 'Đăng kí học online | Giasu707')

@section('content')
    <div class="card p-4">
        
        
        <h2>Cập nhật thông tin cá nhân</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('registration.store') }}">
            @csrf

            <div class="mb-3">
                <label for="name">Họ tên *</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="phone">Số điện thoại *</label>
                <input type="text" name="phone" id="phone" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="email">Email (không bắt buộc)</label>
                <input type="email" name="email" id="email" class="form-control">
            </div>

            <div class="mb-3">
                <label for="subject">Môn học muốn học *</label>
                <input type="text" name="subject" id="subject" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="note">Ghi chú thêm( Thông tin zalo, facebook,... để tiện nhắn tin trao đổi)</label>
                <textarea name="note" class="form-control" rows="3" id="note"></textarea>
            </div>
            <input type="hidden" name="client_id" id="client_id">
            <button type="submit" class="btn btn-primary">Lưu cập nhật</button>
            <a href="{{route('registration.index')}}" class='btn btn-secondary'>Quay lại</a>
        </form>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const clientId = localStorage.getItem('client_id');
            if (!clientId) {
                alert("Không tìm thấy client_id.");
                return;
            }

            fetch(`/api/registration/${clientId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error("Không tìm thấy thông tin.");
                    }
                    return response.json();
                })
                .then(data => {
                    document.getElementById("name").value = data.name;
                    document.getElementById("email").value = data.email;
                    document.getElementById("phone").value = data.phone;
                    document.getElementById("subject").value = data.subject;
                    document.getElementById("note").value = data.note;

                    document.getElementById("client_id").value = clientId;
                })
                .catch(error => {
                    alert(error.message);
                });
        });
    </script>
@endsection