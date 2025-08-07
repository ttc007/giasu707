@extends('layouts.app') {{-- hoặc layout của bạn --}}

@section('title', isset($category) ? ($category->name . ' | Gia sư 707'): 'Bài viết | Gia sư 707')

@section('content')
<div class="container py-4">

    {{-- DANH MỤC --}}
    <div class="mb-4">
        <h1 class="text-center my-3">Thư viện bài viết</h1>

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

    <div>
        <h4 class="mb-5 mt-4 text-center">Danh sách tuyển tập</h2>
        <div class="row">
            @foreach ($collections as $collection)
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <a href="{{ route('home.collection', $collection->slug) }}">
                        @if ($collection->image)
                            <img src="{{ asset($collection->image) }}" class="card-img-top" alt="{{ $collection->title }}">
                        @endif
                        </a>

                        <div class="card-body">
                            <h4 class="card-title">{{ $collection->title }}</h4>
                            <h5 class="card-text">
                                Danh mục: {{ $collection->category->name ?? 'Không có' }}
                            </h5>
                            <p class="text-muted">{!! $collection->description !!}</p>
                            <a href="{{ route('home.collection', $collection->slug) }}" class="btn btn-sm btn-primary">Xem chi tiết</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- PHÂN TRANG --}}
        <div class="d-flex justify-content-center">
            {{ $collections->links() }}
        </div>
    </div>
</div>
@endsection
