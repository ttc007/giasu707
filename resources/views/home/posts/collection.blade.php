@extends('layouts.app')

@section('title', $collection->title)

@section('content')
<style type="text/css">
    .card-body {
        height: 125px!important;      /* chiều cao cố định */
    }

</style>
<div class="container py-4">

    <div class="mb-4">
        <div class="row">
            <div class="col-md-6">
                <div class="square-box">
                    <img src="{{ asset($collection->image) }}" class="centered-img" alt="{{ $collection->title }}">
                </div>
            </div>
            <div class="col-md-6">
                <h1 class="text-center my-3">{{$collection->title}}</h1>
                <h5 class="text-center text-muted">Thể loại: <a href="{{ route('home.category', $collection->category->slug) }}">{{ $collection->category->name }}</a></h5>

                <div class="pb-4">
                    <div class="text-center" style="font-size:25px; display: flex; justify-content: center; gap: 30px; align-items: center;">
                        <span id="view-count">👀 {{ $collection->countView() }}</span>
                        <span id="like-count">❤️{{ $collection->countLikes() }}</span>
                    </div>
                    <div class="text-center"  style="font-size:25px">
                        <div id="like-container" class="mt-3 text-center">
                            <!-- Nút sẽ được hiển thị ở đây -->
                        </div>   
                    </div>
                    
                    <div class="mt-4">
                        {!! $collection->description !!}    
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

    <div class="card p-5">
        <h2 class="mb-5 text-center">Danh sách bài viết</h2>
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
                                    <span>❤️{{ $post->countLikes() }}</span>
                                </div>
                            </div>
                        @endif
                        </a>

                        <div class="card-body">
                            <p class="card-title text-center"><a href="{{ route('home.post.show', ['slug' => $post->category->slug,'post_slug' => $post->slug]) }}">{{ $post->title }}</a></p>
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

        if (collectionId && clientId) {
            fetch(`/api/collection/${collectionId}/is-favorite?client_id=${clientId}`)
                .then(response => response.json())
                .then(data => {
                    updateLikeButton(data.liked);
                });

            function updateLikeButton(isLiked) {
                if (isLiked) {
                    container.innerHTML = `<button class="btn btn-secondary" id="unlike-btn">💔 Bỏ thích</button>`;
                } else {
                    container.innerHTML = `<button class="btn btn-outline-danger" id="like-btn">❤️ Thích</button>`;
                }

                // Gán lại sự kiện sau khi render
                setTimeout(() => {
                    document.getElementById('like-btn')?.addEventListener('click', function () {
                        fetch(`/api/collection/${collectionId}/like`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify({ client_id: clientId })
                        }).then(() => {updateLikeButton(true); updateLikeCount(1)});
                    });

                    document.getElementById('unlike-btn')?.addEventListener('click', function () {
                        fetch(`/api/collection/${collectionId}/unlike`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify({ client_id: clientId })
                        }).then(() => {updateLikeButton(false); updateLikeCount(-1)});
                    });
                }, 10);
            }

            function updateLikeCount(change) {
                const text = likeCountSpan.textContent.trim(); // ❤️123
                const number = parseInt(text.replace('❤️', '').trim());
                likeCountSpan.textContent = `❤️${number + change}`;
            }
        }
    });

document.addEventListener("DOMContentLoaded", function() {
    let clientId = localStorage.getItem("client_id");
    fetch(`/api/collection/view`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({
            client_id: clientId,
            model_id: {{ $collection->id }}
        })
    });
});
</script>
@endsection
