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

                <div class="p-4">
                    {!! $collection->description !!}
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
@endsection
