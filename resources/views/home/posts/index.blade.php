@extends('layouts.app') {{-- hoặc layout của bạn --}}

@section('title', isset($category) ? ($category->name . ' | Gia sư 707'): 'Bài viết | Gia sư 707')

@section('content')
<div class="container py-4 section">

    {{-- DANH MỤC --}}
    <div class="mb-1">
        <h1 class="text-center my-3">THƯ VIỆN BÀI VIẾT</h1>

        <a href="{{ route('home.posts') }}"
           class="btn btn-outline-primary btn-sm m-1
                  @if (!isset($category)) active @endif">
            Tất cả
        </a>
        @foreach ($categories as $cat)
            <a href="{{ route('home.category', $cat->slug) }}"
               class="btn btn-outline-success btn-sm m-1
                      @if (isset($category) && $category->id == $cat->id) active @endif">
                {{ $cat->name }}
            </a>
        @endforeach

    </div>

    <div class="p-3">
        <h3 class="mb-3 mt-1 text-center">Danh sách tuyển tập</h3>

        <div class="row collection-container pt-3">
            @foreach ($collections as $collection)
                <div class="col-md-3 mb-4">
                    <div class="card h-100">
                        <a href="{{ route('home.collection', $collection->slug) }}">
                        @if ($collection->image)
                            <div class="square-box position-relative">
                                <img src="{{ asset($collection->image) }}" class="centered-img" alt="{{ $collection->title }}">
                                <span class="like-badge">❤️{{ $collection->favoriteCount() }}</span>

                            </div>
                        @endif
                        </a>

                        <div class="card-body">
                            <h5 class="card-title text-center"><a href="{{ route('home.collection', $collection->slug) }}">{{ $collection->title }}</a></h5>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- PHÂN TRANG --}}
        <div class="d-flex justify-content-center">
            {{ $collections->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
