@extends('layouts.app')

@section('title', $post->collection->title . ' | ' . $post->title)

@section('content')
<div class="container section">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb p-2">
            <li class="breadcrumb-item">
                <a href="/thu-vien">Thư viện</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('home.category', $post->collection->category->slug) }}">{{ $post->collection->category->name }}</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('home.collection', $post->collection->slug) }}">
                    {{ $post->collection->title ?? 'Không có' }}
                </a>
            </li>
        </ol>
    </nav>
    <div class="row">
        <div class="col-md-4">
            <div class="square-box">
                <img src="{{ asset($post->image) }}" class="centered-img">
            </div>
        </div>
        <div class="col-md-8 collection-title">
            <h2 class="px-3 pt-4">{{ $post->title }}</h2>
            <div class="text-center" style="font-size:20px; display: flex; justify-content: center; gap: 15px; align-items: center;">
                <span class="view-count">👀 {{ $post->countView() }}</span>
                <span id="like-count">❤️ {{ $post->countLikes() }}</span>
                <a href="#commet-div">💬 {{ $post->commentsCount() }}</a>
            </div>
            <div class="text-center" style="font-size:15px">
                <div id="like-container">
                    @if($liked)
                    <button class="btn btn-secondary" id="unlike-btn">💔 Bỏ thích</button>
                    @else
                    <button class="btn btn-outline-danger" id="like-btn">❤️ Thích</button>
                    @endif
                </div>
            </div>
            <hr style="width:100%; border-top:1px solid #111111;">

            <!-- Wrapper để override flex center -->
            <div style="align-self: flex-end; width:100%; text-align:right;">
                <div class="text-muted">Cập nhật gần nhất: {{ $post->getUpdatedDate() }}</div>
            </div>
        </div>
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

    <hr class="mt-4">

    <div class="comments mt-4" id="commet-div">
        <h5>💬 Bình luận</h5>

        <form method="POST" action="{{ route('comments.store') }}">
            @csrf
            <input type="hidden" name="commentable_type" value="Post">
            <input type="hidden" name="commentable_id" value="{{ $post->id }}">

            <div class="mb-3">
                <textarea name="content" class="form-control" rows="3" maxlength="255"
                          placeholder="Nhập bình luận của bạn..." required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Gửi bình luận</button>
        </form>
        <hr>
        <div class="comment-list mt-4">
            @forelse ($comments as $comment)
                <div class="comment-item">
                    <img src="{{ asset('images/avatar.png') }}" class="comment-avatar" alt="Ảnh đại diện">
                    <div class="comment-body">
                        <div class="comment-meta">
                            {{ $comment->registration?->name ?? 'Khách' }} · {{ $comment->getUpdatedDate() }}
                        </div>
                        <div class="comment-content">
                            {{ $comment->content }}
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-muted">Chưa có bình luận nào.</p>
            @endforelse

            <div class="mt-3">
                {{ $comments->links('vendor.pagination.bootstrap-5') }}
            </div>
        </div>

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
    document.addEventListener('DOMContentLoaded', function () {
        const collectionId = '{{ $post->id ?? '' }}';
        const clientId = localStorage.getItem('client_id');
        const container = document.getElementById('like-container');
        const likeCountSpan = document.getElementById('like-count');
        const type = 'post';

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
            likeCountSpan.textContent = `❤️ ${number + change}`;
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
