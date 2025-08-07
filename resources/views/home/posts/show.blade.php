@extends('layouts.app')

@section('content')
    <h1>{{ $post->title }}</h1>
    <p><strong>Danh mục:</strong> {{ $post->category->name ?? 'Không có' }}</p>
    <p><strong>Tuyển tập:</strong> {{ $post->collection->name ?? 'Không có' }}</p>

    @if($post->image)
        <img src="{{ asset('images/posts/' . $post->image) }}" alt="{{ $post->title }}" width="400">
    @endif

    <div>{!! $post->content !!}</div>
@endsection
