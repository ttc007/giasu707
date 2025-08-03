@extends('layouts.app')

@section('title', $chapter->title . ' - ' . $subject->name . ' | Giasu707')

@section('content')
<div class="container">
    <h3 class="text-center">{{ $chapter->subject->name }}</h3>
    <h4 class="text-center">{{ $chapter->title }}</h4>

    <h5>Danh sách bài học:</h5>
    <div class="card p-3 rounded border">
    @foreach ($chapter->lessons as $lesson)
        <div class="mb-3">
            <strong>
                <a href="{{ route('show.lesson', [
                    'subject_slug' => $subject->slug,
                    'chapter_slug' => $chapter->slug,
                    'lesson_slug' => $lesson->slug
                ]) }}">
                    {{ $loop->iteration }}. {{ $lesson->title }}
                </a>
            </strong>

            @if ($lesson->sections->count())
                <ul>
                    @foreach ($lesson->sections as $section)
                        <li>
                            <a href="{{ route('show.section', [
                                'subject_slug' => $subject->slug,
                                'chapter_slug' => $chapter->slug,
                                'section_slug' => $section->slug
                            ]) }}">
                                {{ $section->title ?? 'Phần ' . $loop->iteration }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endforeach
    </div>
    <hr>
    <a href="{{ route('review.chapter', [
        'subject_slug' => $subject->slug,
        'chapter_slug' => $chapter->slug
    ]) }}" class="btn btn-outline-success">
        🔁 Ôn tập chương
    </a>
</div>
@endsection
