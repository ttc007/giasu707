@extends('layouts.app')

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

                <!-- Giới thiệu nền tảng học tập -->
                <div class="mt-4 p-3 bg-white rounded border shadow-sm">
                    <h4 class="text-success">🎓 Nền tảng học tập trực tuyến miễn phí</h4>
                    <p class="text-justify">
                        Website này là một <strong>nền tảng học tập trực tuyến</strong> được đầu tư biên soạn công phu, kỹ lưỡng.
                        Nội dung bao gồm đầy đủ các môn học quan trọng trong kỳ thi tốt nghiệp như <strong>Toán 12, Vật lí 12, Hóa học 12</strong>,
                        đến cả các môn học cấp 1, cấp 2 – tất cả đều được xây dựng bài bản và đồ sộ.
                    </p>
                    <p class="text-justify">
                        Mình hy vọng đây sẽ là nơi giúp các bạn học sinh học tập hiệu quả hơn, tự tin hơn trên hành trình ôn thi và tích lũy kiến thức.
                    </p>
                </div>

                <!-- Kêu gọi ủng hộ -->
                <div class="mt-4 p-3 bg-light rounded border">
                    <h4 class="text-danger">❤️ Ủng hộ tác giả</h4>
                    <p class="text-justify">
                        Nếu bạn thấy website hữu ích và muốn góp phần giúp mình duy trì và phát triển nội dung, có thể ủng hộ qua:
                    </p>
                    <ul>
                        <li><strong>Ngân hàng NCB</strong> – Trương Thành Công</li>
                        <li><strong>Số tài khoản:</strong> <span class="text-primary">100007635197</span></li>
                    </ul>
                    <p class="mb-0">Mình rất cảm ơn sự quan tâm và ủng hộ của các bạn 💖</p>
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
