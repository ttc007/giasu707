@extends('layouts.app')

@section('title', $post->collection->title . ' | ' . $post->title)

@section('content')
<div class="container section">
    <div class="post-header text-center">
        <h1 class="collection-title">
            <a href="{{ route('home.collection', $post->collection->slug) }}">
                {{ $post->collection->title ?? 'Không có' }}
            </a>
        </h1>
        <h4 class="post-title">{{ $post->title }}</h4>
        <p class="text-muted category">
            <strong>Danh mục:</strong>
            <a href="{{ route('home.category', $post->category->slug) }}">
                {{ $post->category->name ?? 'Không có' }}
            </a>
        </p>
        <div class="post-stats">
            <span class="view-count">👀 {{ $post->countView() }}</span>
            <span id="like-count">❤️ {{ $post->countLikes() }}</span>
        </div>
        <div id="like-container"></div>
        <p class="text-muted text-end">Cập nhật gần nhất: {{ $post->getUpdatedDate() }}</p>
    </div>

    <hr>

    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#chapterModal">
        📚 Danh sách bài viết cùng tuyển tập
    </button>

    <div class="post-content mt-4">{!! $post->content !!}</div>

    <hr>

    <div class="d-flex justify-content-between mt-4">
        @if ($post->prev())
            <a href="{{route('home.post.show', [
                        'slug' => $post->collection->slug,
                        'post_slug' => $post->prev()->slug
                    ])}}" class="btn btn-outline-success">
                {{$post->prev()->title}}
            </a>
        @else
            <button class="btn btn-outline-success" disabled>Phần trước</button>
        @endif
        
        @if ($post->next())
            <a href="{{route('home.post.show', [
                        'slug' => $post->collection->slug,
                        'post_slug' => $post->next()->slug
                    ])}}" class="btn btn-outline-success">
                {{$post->next()->title}}
            </a>
        @else
            <button class="btn btn-outline-success" disabled>Phần sau</button>
        @endif
    </div>

    <!-- Modal Danh sách chương -->
    <div class="modal fade" id="chapterModal" tabindex="-1" aria-labelledby="chapterModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="chapterModalLabel">Danh sách chương</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
          </div>
          <div class="modal-body">
            @if ($post->collection && $post->collection->posts)
                <ul class="list-group">
                    @foreach ($post->collection->posts as $chapter)
                        <a href="{{ route('home.post.show', [
                            'slug' => $chapter->collection->slug,
                            'post_slug' => $chapter->slug
                        ]) }}" class="list-group-item list-group-item-action {{ $chapter->id == $post->id ? 'active' : '' }}">
                            {{ $chapter->title }}
                        </a>
                    @endforeach
                </ul>
            @else
                <p>Không có chương nào.</p>
            @endif
          </div>
        </div>
      </div>
    </div>
</div>
<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function() {
        let clientId = localStorage.getItem("client_id");
        fetch(`/api/post/view`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                client_id: clientId,
                model_id: {{ $post->id }}
            })
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        const collectionId = '{{ $post->id ?? '' }}';
        const clientId = localStorage.getItem('client_id');
        const container = document.getElementById('like-container');
        const likeCountSpan = document.getElementById('like-count');
        const type = 'post';

        if (collectionId && clientId) {
            fetch(`/api/${type}/${collectionId}/is-favorite?client_id=${clientId}`)
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
                        fetch(`/api/${type}/${collectionId}/like`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify({ client_id: clientId })
                        }).then(() => {updateLikeButton(true); updateLikeCount(1)});
                    });

                    document.getElementById('unlike-btn')?.addEventListener('click', function () {
                        fetch(`/api/${type}/${collectionId}/unlike`, {
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
