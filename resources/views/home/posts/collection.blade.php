@extends('layouts.app')

@section('title', $collection->title)

@section('content')
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
                    <div class="text-center"  style="font-size:25px">
                        <span id="like-count">❤️{{ $collection->favoriteCount() }}</span>
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
        <h2 class="mb-5 text-center">Danh sách chương</h2>
        <div class="row">
            <ul>
                @foreach ($posts as $index => $post)
                    <li><a href="{{route('home.post.show', [
                        'slug' => $collection->slug,
                        'post_slug' => $post->slug
                    ])}}" class="text-primary">{{$post->title}}</a></li>
                @endforeach
            </ul>
            
        </div>

        {{-- PHÂN TRANG --}}
        <div class="d-flex justify-content-center">
            {{ $posts->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const collectionSlug = '{{ $collection->slug ?? '' }}';
        const clientId = localStorage.getItem('client_id');
        const container = document.getElementById('like-container');
        const likeCountSpan = document.getElementById('like-count');
        console.log(collectionSlug, clientId)

        if (collectionSlug && clientId) {
            fetch(`/api/collection/${collectionSlug}/is-favorite?client_id=${clientId}`)
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
                        fetch(`/api/collection/${collectionSlug}/like`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify({ client_id: clientId })
                        }).then(() => {updateLikeButton(true); updateLikeCount(1)});
                    });

                    document.getElementById('unlike-btn')?.addEventListener('click', function () {
                        fetch(`/api/collection/${collectionSlug}/unlike`, {
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
</script>
@endsection
