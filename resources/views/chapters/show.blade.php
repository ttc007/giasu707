@extends('layouts.app')

@section('title', $chapter->title . ' - ' . $subject->name . ' | Giasu707')
@section('description', 'Chương bài học thuộc môn ' . $chapter->subject->name . ' tại Gia sư 707.')
@section('keywords', $chapter->title . ', Gia sư 707, blog học tập, cờ tướng, sống chậm')
@section('image', asset($chapter->image))

@section('content')
<div class="container section">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb p-2">
            <li class="breadcrumb-item">
                <a href="/">
                    Trang chủ
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('show.subject', ['subject_slug' => $chapter->subject->slug])}}">{{ $chapter->subject->name }}</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('show.chapter', ['subject_slug' => $chapter->subject->slug, 'chapter_slug' => $chapter->slug])}}">{{ $chapter->title }}</a>
            </li>
        </ol>
    </nav>

    <h3 class="text-center p-3">DANH SÁCH BÀI HỌC</h3>
    
    <div class="row collection-container pt-3">
        @foreach ($chapter->lessons as $lesson)
            <div class="col-md-3 mb-4">
                <div class="card h-100">
                    <a href="{{ route('show.lesson', [
                        'subject_slug' => $subject->slug,
                        'chapter_slug' => $chapter->slug,
                        'lesson_slug' => $lesson->slug
                    ]) }}">
                        <div class="square-box position-relative">
                            <img src="{{ asset('images/lesson_default.jpg') }}" class="centered-img" alt="{{ $lesson->title }}">
                            <div class="like-badge">
                                <span>👀 {{ $lesson->countView() }}</span>
                                <span>❤️ {{ $lesson->countLikes() }}</span>
                            </div>
                        </div>
                    </a>

                    <div class="card-body">
                        <h5 class="card-title text-center"><a href="{{ route('show.lesson', [
                                'subject_slug' => $subject->slug,
                                'chapter_slug' => $chapter->slug,
                                'lesson_slug' => $lesson->slug
                            ]) }}">{{ $lesson->title }}</a></h5>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <hr>
    <a href="{{ route('review.chapter', [
        'subject_slug' => $subject->slug,
        'chapter_slug' => $chapter->slug
    ]) }}" class="btn btn-success">
        🔁 Ôn tập chương
    </a>
</div>
@endsection
