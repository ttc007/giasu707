@extends('layouts.app')

@section('title', $post->collection->title . ' | ' . $post->title)

@section('content')
    <div class="text-center">
        <h1>{{ $post->collection->title ?? 'Không có' }}</h1>
        <h4>{{ $post->title }}</h4>
        <p class="text-muted"><strong>Danh mục:</strong> <a href="{{ route('home.category', $post->category->slug) }}">{{ $post->category->name ?? 'Không có' }}</a></p>
    </div>
    
    <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#chapterModal">
        📚 Danh sách chương
    </button>

    <div class="mt-4">{!! $post->content !!}</div>
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

@endsection
