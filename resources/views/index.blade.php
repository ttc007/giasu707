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
                <p class="mt-3 text-justify">
                    Mình từng là một lập trình viên. Nhưng đến một ngày mình chán cái cảnh làm 8 tiếng mỗi ngày bán mình cho tư bản...
                    Mình đã quyết định nghỉ làm để về quê. Trồng cây và dạy học online.
                </p>

                <!-- Giới thiệu Gia sư 707 -->
                <div class="mt-5 p-3 bg-light rounded border">
                    <h4 class="">📚 Gia sư 707 là gì?</h4>
                    <p class="text-justify">
                        <strong>Gia sư 707</strong> là một nhóm học tập nhỏ mà mình lập ra để giúp các bạn học sinh có thể hỏi bài mọi lúc mọi nơi.
                        Các bạn có thể gửi câu hỏi qua <strong>Zalo: 0909707000</strong>, mình sẽ trả lời nhanh và dễ hiểu nhất có thể.<br>
                        Hoặc follow mình trên <strong>Facebook</strong> để tiện nhắn tin nhé.
                    </p>
                    <p>
                        👉 <a href="https://zalo.me/0909707000" target="_blank" class="btn btn-outline-success btn-sm">
                            Nhắn tin Zalo ngay
                        </a> <br><br>
                        👉 <a href="https://www.facebook.com/truong.thanh.cong.201321" target="_blank" class="btn btn-outline-primary btn-sm">
                            Follow Facebook
                        </a>
                    </p>


                </div>

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

                <!-- Bài viết nổi bật -->
                <div class="mt-5 p-3 bg-white rounded border shadow-sm">
                    <h4 class="text-success mb-4">🔥 Bài viết nổi bật</h4>
                    <div class="row collection-container pt-3">
                        @foreach($featuredPosts as $post)
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    @if($post->image)
                                    <a href="{{ route('home.post.show', ['slug' => $post->collection->slug, 'post_slug' => $post->slug]) }}" >
                                        <div class="square-box position-relative">
                                            <img src="{{ asset($post->image) }}" class="centered-img" alt="{{ $post->title }}">
                                            <div class="like-badge">
                                                <span>👀 {{ $post->countView() }}</span>
                                                <span>❤️{{ $post->countLikes() }}</span>
                                            </div>
                                        </div></a>
                                    @endif
                                    <div class="card-body d-flex flex-column justify-content-between">
                                        <h5 class="card-title text-center"><a href="{{ route('home.post.show', ['slug' => $post->collection->slug, 'post_slug' => $post->slug]) }}" >{{ $post->title }}</a></h5>
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
