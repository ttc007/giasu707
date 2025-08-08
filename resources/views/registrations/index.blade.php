@extends('layouts.app')

@section('title', 'Đăng kí học online | Giasu707')

@section('content')
    <div class="card p-5">
        <h1 class="mb-5 text-center">Thông Tin Cá Nhân</h1>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <p><strong>Họ tên:</strong> <span id="name"></span></p>
        <p><strong>Email:</strong> <span id="email"></span></p>
        <p><strong>Số điện thoại:</strong> <span id="phone"></span></p>
        <p><strong>Môn học:</strong> <span id="subject"></span></p>
        <p><strong>Ghi chú:</strong><span id="note"></span></p>
        <a href="{{route('registration.create')}}" class="btn btn-primary">Cập nhật</a>
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
                    document.getElementById("name").textContent = data.name;
                    document.getElementById("email").textContent = data.email;
                    document.getElementById("phone").textContent = data.phone;
                    document.getElementById("subject").textContent = data.subject;
                    document.getElementById("note").textContent = data.note;
                })
                .catch(error => {
                    alert(error.message);
                });
        });
    </script>
@endsection