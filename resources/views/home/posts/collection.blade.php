@extends('layouts.app')

@section('title', $collection->title)

@section('content')
<style type="text/css">
    .card-body {
        height: 125px!important;      /* chiều cao cố định */
    }

</style>
<div class="container py-4">

    <div class="mb-4 section">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb p-2">
                <li class="breadcrumb-item">
                    <a href="/thu-vien">Thư viện</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('home.category', $collection->category->slug) }}">{{ $collection->category->name }}</a>
                </li>
            </ol>
        </nav>
        <div class="row">
            <div class="col-md-4 ">
                <div class="square-box">
                    <img src="{{ asset($collection->image) }}" class="centered-img" alt="{{ $collection->title }}">
                </div>
            </div>
            <div class="col-md-8 collection-title">
                <h2 class="text-center my-3">{{$collection->title}}</h1>

                <div class="pb-4">
                    <div class="text-center" style="font-size:20px; display: flex; justify-content: center; gap: 15px; align-items: center;">
                        <span id="view-count">👀 {{ $collection->countView() }}</span>
                        <span id="like-count">❤️ {{ $collection->countLikes() }}</span>
                    </div>
                    <div class="text-center"  style="font-size:15px">
                        <div id="like-container" class="mt-3 text-center">
                            @if($liked)
                            <button class="btn btn-secondary" id="unlike-btn">💔 Bỏ thích</button>
                            @else
                            <button class="btn btn-outline-danger" id="like-btn">❤️ Thích</button>
                            @endif
                        </div>   
                    </div>
                    
                    <div class="p-4 collection-description">
                        {!! $collection->description !!}    
                    </div>
                    <hr>
                    <div class="text-muted text-end">Cập nhật gần nhất: {{ $collection->getUpdatedDate() }}</div>
                </div>
            </div>
        </div>
        <hr>
        <h3 class="mb-4 mt-5 text-center">Danh sách bài viết trong tuyển tập</h2>
        <div class="row collection-container pt-3">
            @foreach ($posts as $post)
                <div class="col-md-3 mb-4">
                    <div class="card h-100">
                        <a href="{{ route('home.post.show', ['slug' => $post->category->slug,'post_slug' => $post->slug]) }}">
                        @if ($post->image)
                            <div class="square-box position-relative">
                                <img src="{{ asset($post->image) }}" class="centered-img" alt="{{ $post->title }}">
                                <div class="like-badge">
                                    <span>👀 {{ $post->countView() }}</span>
                                    <span>❤️ {{ $post->countLikes() }}</span>
                                </div>
                            </div>
                        @endif
                        </a>

                        <div class="card-body">
                            <h5 class="card-title text-center"><a href="{{ route('home.post.show', ['slug' => $post->category->slug,'post_slug' => $post->slug]) }}">{{ $post->title }}</a></h5>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- PHÂN TRANG --}}
        <div class="d-flex justify-content-center">
            {{ $posts->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const collectionId = '{{ $collection->id ?? '' }}';
        const clientId = localStorage.getItem('client_id');
        const container = document.getElementById('like-container');
        const likeCountSpan = document.getElementById('like-count');
        const type = 'collection';

        updateLikeButtonFunction();

        function updateLikeButton(isLiked) {
            if (isLiked) {
                container.innerHTML = `<button class="btn btn-secondary" id="unlike-btn">💔 Bỏ thích</button>`;
            } else {
                container.innerHTML = `<button class="btn btn-outline-danger" id="like-btn">❤️ Thích</button>`;
            }

            updateLikeButtonFunction();
        }

        function updateLikeCount(change) {
            const text = likeCountSpan.textContent.trim(); // ❤️123
            const number = parseInt(text.replace('❤️', '').trim());
            likeCountSpan.textContent = `❤️${number + change}`;
        }

        function updateLikeButtonFunction() {
            // Gán lại sự kiện sau khi render
            setTimeout(() => {
                document.getElementById('like-btn')?.addEventListener('click', function () {
                    fetch(`/api/${type}/${collectionId}/like`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        }
                    }).then(() => {updateLikeButton(true); updateLikeCount(1)});
                });

                document.getElementById('unlike-btn')?.addEventListener('click', function () {
                    fetch(`/api/${type}/${collectionId}/unlike`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        }
                    }).then(() => {updateLikeButton(false); updateLikeCount(-1)});
                });
            }, 10);
        }
    });
</script>
@endsection
