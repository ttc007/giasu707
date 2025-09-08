@extends('layouts.app')

@section('title', 'Danh sách chương - ' . $subject->name . ' | Giasu707')
@section('description', 'Môn học tại Gia sư 707.')
@section('keywords', $subject->name . ', Gia sư 707, blog học tập, cờ tướng, sống chậm')

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
                <a href="{{ route('show.subject', ['subject_slug' => $subject->slug])}}">{{ $subject->name }}</a>
            </li>
        </ol>
    </nav>
    <h3 class="text-center pt-2">DANH SÁCH CHƯƠNG</h2>
    <p class="text-muted text-center mb-4">Hãy chọn 1 chương để ôn tập</p>
    <hr>

    @if ($chapters->count())
        <div class="row">
            @foreach($chapters as $chapter)
                <div class="col-md-3 mb-4">
                    <div class="card h-100">
                        <a href="{{ route('show.chapter', [
                            'subject_slug' => $subject->slug,
                            'chapter_slug' => $chapter->slug
                        ]) }}">
                            <div class="square-box position-relative">
                                <img src="{{ asset($chapter->image) }}" class="centered-img" alt="{{ $chapter->title }}">
                            </div>
                        </a>

                        <div class="card-body">
                            <h5 class="card-title text-center"><a href="{{ route('show.chapter', [
                                'subject_slug' => $subject->slug,
                                'chapter_slug' => $chapter->slug
                            ]) }}">{{ $chapter->title }}</a></h5>
                            <p class="text-muted text-center">{{ $chapter->lessons_count }} bài học</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p>Chưa có chương nào cho môn học này.</p>
    @endif
</div>
@endsection

