@extends('layouts.app')

@section('title', 'Đăng kí học online | Giasu707')

@section('content')
    <div class="card p-5">
        <div class="text-center">
        <img src="{{ asset('images/avatar.png') }}" class="rounded-circle shadow" width="150" alt="Ảnh đại diện">
        </div>
        <h1 class="mb-5 mt-3 text-center">Thông Tin Cá Nhân</h1>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <p><strong>Họ tên:</strong> <span id="name"></span></p>
        <p><strong>Email:</strong> <span id="email"></span></p>
        <p><strong>Số điện thoại:</strong> <span id="phone"></span></p>
        <p><strong>Môn học:</strong> <span id="subject"></span></p>
        <p><strong>Ghi chú:</strong><span id="note"></span></p>
        <a href="{{route('registration.create')}}" class="btn btn-primary">Cập nhật</a>

        <h2 class="text-center mt-4 mb-2">Tuyển tập yêu thích</h2>
        <div class="row" id="favorite-collections-container">
            <!-- JS sẽ render vào đây -->
        </div>

    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const clientId = localStorage.getItem('client_id');
            const container = document.getElementById('favorite-collections-container');
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

                    const collections = data.favorite_collections;

                    if (!collections || collections.length === 0) {
                        container.innerHTML = '<p class="text-muted text-center">Chưa có tuyển tập nào được yêu thích.</p>';
                        return;
                    }

                    container.innerHTML = ''; // Xóa nếu có cũ
                    collections.forEach(col => {
                        const url = `/tuyen-tap/${col.slug}`;
                        const image = col.image ? `<div class="square-box">
                            <img src="/${col.image}" class="centered-img" alt="${col.title}">
                        </div>` : '';

                        const cardHTML = `
                        <div class="col-md-3 mb-4">
                            <div class="card h-100">
                                <a href="${url}">
                                    ${image}
                                </a>
                                <div class="card-body">
                                    <h4 class="card-title text-center">
                                        <a href="${url}">${col.title}</a>
                                    </h4>
                                </div>
                            </div>
                        </div>`;
                        
                        container.innerHTML += cardHTML;
                    });
                })
                .catch(error => {
                    alert(error.message);
                });
        });
    </script>
@endsection