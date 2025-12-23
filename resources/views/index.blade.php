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
                <h2 class="text-center">Xin chào, mình là Trương Thành Công</h2>
                <p class="mt-3 text-center text-muted fs-5">
                    🌱 Chào mừng bạn đến với <strong>blog nhỏ</strong> của mình! <br>
                    <span class="d-block mt-2">Học tập • Giải trí • Thư giãn • Cờ tướng • Trồng cây</span>
                </p>

                <!-- Giới thiệu nền tảng học tập -->
                <div class="mt-4 p-3 bg-white rounded border shadow-sm">
                    <h4 class="text-success">🎓 Nền tảng học tập trực tuyến miễn phí</h4>
                    <p class="text-justify">
                        Website này là một <strong>nền tảng học tập trực tuyến</strong> được đầu tư biên soạn công phu, kỹ lưỡng.
                        Nội dung bao gồm đầy đủ các môn học quan trọng trong kỳ thi tốt nghiệp như <strong>
                            <!-- <a href="{{ url('/toan-12') }}">Toán 12</a>, -->
                         <a href="{{ url('/vat-li-12') }}">Vật lí 12</a>
                          <!-- , <a href="{{ url('/hoa-12') }}">Hóa học 12</a> -->
                      </strong>,
                        đến cả các môn học cấp 1, cấp 2 – tất cả đều được xây dựng bài bản và đồ sộ.
                    </p>
                    <p class="text-justify">
                        Mình hy vọng đây sẽ là nơi giúp các bạn học sinh học tập hiệu quả hơn, tự tin hơn trên hành trình ôn thi và tích lũy kiến thức.
                    </p>
                </div>

                <div class="mt-5 p-3 bg-white rounded border shadow-sm">
                    <h4 class="text-primary mb-4">🔥 Bài viết nổi bật</h4>
                    <div class="row collection-container pt-3">
                        @foreach($popularPosts as $post)
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    @if($post->image)
                                        <a href="{{ route('home.post.show', ['slug' => $post->collection->slug, 'post_slug' => $post->slug]) }}">
                                            <div class="square-box position-relative">
                                                <img src="{{ asset($post->image) }}" class="centered-img" alt="{{ $post->title }}">
                                                <div class="like-badge">
                                                    <span>👀 {{ $post->views_count ?? $post->countView() }}</span>
                                                    <span>❤️ {{ $post->countLikes() }}</span>
                                                </div>
                                            </div>
                                        </a>
                                    @endif
                                    <div class="card-body d-flex flex-column justify-content-between">
                                        <h5 class="card-title text-center">
                                            <a href="{{ route('home.post.show', ['slug' => $post->collection->slug, 'post_slug' => $post->slug]) }}">
                                                {{ $post->title }}
                                            </a>
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Bài viết nổi bật -->
                <div class="mt-5 p-3 bg-white rounded border shadow-sm">
                    <h4 class="text-success mb-4">🆕 Bài viết mới nhất</h4>
                    <div class="row collection-container pt-3">
                        @foreach($latestPosts as $post)
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    @if($post->image)
                                        <a href="{{ route('home.post.show', ['slug' => $post->collection->slug, 'post_slug' => $post->slug]) }}">
                                            <div class="square-box position-relative">
                                                <img src="{{ asset($post->image) }}" class="centered-img" alt="{{ $post->title }}">
                                                <div class="like-badge">
                                                    <span>👀 {{ $post->countView() }}</span>
                                                    <span>❤️ {{ $post->countLikes() }}</span>
                                                </div>
                                            </div>
                                        </a>
                                    @endif
                                    <div class="card-body d-flex flex-column justify-content-between">
                                        <h5 class="card-title text-center">
                                            <a href="{{ route('home.post.show', ['slug' => $post->collection->slug, 'post_slug' => $post->slug]) }}">
                                                {{ $post->title }}
                                            </a>
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-4 p-3 bg-white rounded border shadow-sm">
                <h4 class="mt-4">🧑‍💻 Dịch vụ thiết kế website</h4>
                    <p class="text-justify">
                        Ngoài việc dạy học, mình còn nhận thiết kế website cho cá nhân, cửa hàng, trung tâm, trường học... Bạn có thể <a href="/bang-gia-thiet-ke-website" class="fw-bold">tham khảo bảng giá tại đây</a>.  
                        Đảm bảo sản phẩm <strong>đẹp mắt, đầy đủ tính năng, chuẩn SEO</strong> mà giá cả thì <em>rất chi là "sinh viên" luôn</em> 😄.
                    </p>
                    <p>
                        👉 <a href="/bang-gia-thiet-ke-website" class="btn btn-outline-primary btn-sm">
                            Tham khảo bảng giá
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
        <div class="card shadow-sm border-0 bg-light">
            <div class="card-body">
                <h4 class="text-success fw-bold mb-3">
                    <i class="bi bi-controller"></i> Kho game
                </h4>
                <div class="d-flex flex-column gap-3">
                    <a href="{{ url('/games/hung_bong') }}" class="game-card-link text-decoration-none">
                        <div class="card h-100 border-0 shadow-sm hover-up">
                            <div class="row g-0 align-items-center">
                                <div class="col-4 p-2 text-center">
                                    <img src="{{ asset('images/thumnail.jpg') }}" 
                                         class="rounded-3 img-fluid shadow-sm" 
                                         alt="Hứng bóng">
                                </div>
                                <div class="col-8">
                                    <div class="card-body p-2">
                                        <h5 class="card-title text-dark mb-1">Game hứng bóng</h5>
                                        <p class="card-text text-muted small mb-0">Thiết kế bởi Scratch</p>
                                        <span class="badge bg-success-soft text-success mt-1">Chơi ngay</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                    </div>
            </div>
        </div>
    </div>
</div>
@endsection
