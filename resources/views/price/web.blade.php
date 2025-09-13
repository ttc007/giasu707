@extends('layouts.app')

@section('title', 'Bảng giá thiết kế website')
@section('description', 'Bảng giá thiết kế website tại Gia sư 707')
@section('keywords', 'Bảng giá thiết kế website, Gia sư 707, blog học tập, cờ tướng, sống chậm, thiết kế web')
@section('image', asset('images/bg.jpg'))

@section('content')
<div class="container py-4 section">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb p-2">
            <li class="breadcrumb-item">
                <a href="/">
                    Trang chủ
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="/bang-gia-thiet-ke-website">Bảng giá thiết kế website</a>
            </li>
        </ol>
    </nav>
    <h3 class="text-center p-4 mb-4">📊 DANH SÁCH BẢNG GIÁ</h3>

    <div class="row g-4">
        <!-- Gói Cơ bản -->
        <div class="col-md-4">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white text-center">GÓI CƠ BẢN</div>
                <div class="card-body">
                    <h3 class="text-center text-primary">1.500.000đ</h3>
                    <ul>
                        <li>Giao diện đẹp mắt</li>
                        <li>1 trang chủ + 3 trang nội dung</li>
                        <li>Chuẩn SEO, responsive</li>
                        <li>Hỗ trợ 3 tháng</li>
                    </ul>
                    <a href="https://zalo.me/0909707000" class="btn btn-outline-primary w-100">Nhắn Zalo tư vấn</a>
                </div>
            </div>
        </div>

        <!-- Gói Nâng cao -->
        <div class="col-md-4">
            <div class="card border-success">
                <div class="card-header bg-success text-white text-center">GÓI NÂNG CAO</div>
                <div class="card-body">
                    <h3 class="text-center text-success">2.500.000đ</h3>
                    <ul>
                        <li>Giao diện theo yêu cầu</li>
                        <li>5+ trang nội dung</li>
                        <li>Tích hợp form liên hệ</li>
                        <li>Hỗ trợ 6 tháng</li>
                    </ul>
                    <a href="https://zalo.me/0909707000" class="btn btn-outline-success w-100">Tư vấn ngay</a>
                </div>
            </div>
        </div>

        <!-- Gói Đầy đủ -->
        <div class="col-md-4">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white text-center">GÓI ĐẦY ĐỦ</div>
                <div class="card-body">
                    <h3 class="text-center text-danger">3.500.000đ</h3>
                    <ul>
                        <li>Website chuyên nghiệp</li>
                        <li>Tích hợp quản lý nội dung</li>
                        <li>Đa ngôn ngữ, SEO nâng cao</li>
                        <li>Hỗ trợ 12 tháng</li>
                    </ul>
                    <a href="https://zalo.me/0909707000" class="btn btn-outline-danger w-100">Liên hệ ngay</a>
                </div>
            </div>
        </div>
    </div>

    <p class="text-muted text-center mt-4">* Giá có thể thay đổi tùy yêu cầu thêm. Hỗ trợ chỉnh sửa miễn phí sau khi bàn giao.</p>
</div>
@endsection
