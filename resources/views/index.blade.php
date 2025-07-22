@extends('layouts')

@section('title', 'Trang chủ')

@section('content')
<div class="row gy-4">
    <!-- Cột giới thiệu bản thân -->
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="text-center mb-4">
                    <img src="{{ asset('images/avata.jpg') }}" class="rounded-circle shadow" width="150" alt="Ảnh đại diện">
                </div>
                <h2 class="text-primary text-center">Xin chào, mình là Trương Thành Công</h2>
                <p class="mt-3 text-justify">
                    Mình từng là một lập trình viên. Nhưng đến một ngày mình chán cái cảnh làm 8 tiếng mỗi ngày bán mình cho tư bản...
                    Mình đã quyết định nghỉ làm để về quê. Trồng cây và dạy học online.
                </p>

                <!-- Giới thiệu Gia sư 707 -->
                <div class="mt-5 p-3 bg-light rounded border">
                    <h4 class="text-info">📚 Gia sư 707 là gì?</h4>
                    <p class="text-justify">
                        <strong>Gia sư 707</strong> là một nhóm học tập nhỏ mà mình lập ra để giúp các bạn học sinh có thể hỏi bài mọi lúc mọi nơi.
                        Các bạn có thể gửi câu hỏi qua <strong>Zalo: 0909707000</strong>, mình sẽ trả lời nhanh và dễ hiểu nhất có thể.
                    </p>
                    <p>
                        👉 <a href="https://zalo.me/0909707000" target="_blank" class="btn btn-outline-primary btn-sm">
                            Nhắn tin Zalo ngay
                        </a>
                    </p>
                </div>

                <h4 class="mt-5">🌱 Những cái cây mình trồng</h4>
                <div class="d-flex flex-wrap gap-3 justify-content-center">
                    @for ($i = 1; $i <= 11; $i++)
                        <img src="{{ asset("images/t$i.jpg") }}" alt="Cây $i" width="120" class="img-thumbnail shadow-sm">
                    @endfor
                </div>
            </div>
        </div>
    </div>

    <!-- Cột hình ảnh học sinh -->
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h4 class="text-success">📘 Thành quả học sinh</h4>
                <div class="d-flex flex-column gap-3">
                    @for ($i = 1; $i <= 3; $i++)
                        <img src="{{ asset("images/kt$i.jpg") }}" alt="Bài làm $i" class="img-fluid img-thumbnail shadow-sm">
                    @endfor
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
