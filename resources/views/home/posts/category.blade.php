@extends('layouts.app')

@section('content')
    <h1>Bài viết thuộc danh mục: {{ $collection->tilte }}</h1>
    @foreach($posts as $post)
        <div>
            <h2><a href="{{ route('home.posts.show', $post->slug) }}">{{ $post->title }}</a></h2>
            <p>{{ \Str::limit(strip_tags($post->content), 100) }}</p>
        </div>
    @endforeach

    {{ $posts->links() }}
@endsection
