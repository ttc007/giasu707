@extends('layouts.app')

@section('title', 'Danh sách chương - ' . $subject->name . ' | Giasu707')

@section('content')
<div class="container">
    <h2>{{ $subject->name }}</h2>
    <p class="text-muted">Hãy chọn 1 chương để ôn tập:</p>

    @if ($chapters->count())
        <div class="list-group">
            @foreach($chapters as $chapter)
                <a href="{{ route('show.chapter', [
                    'subject_slug' => $subject->slug,
                    'chapter_slug' => $chapter->slug
                ]) }}" class="list-group-item list-group-item-action">
                    {{ $chapter->title }} ({{ $chapter->lessons_count }} bài học)
                </a>
            @endforeach
        </div>
    @else
        <p>Chưa có chương nào cho môn học này.</p>
    @endif
</div>
@endsection

