@extends('layouts.admin')

@section('content')
    <h1>Sửa bài viết</h1>
    <form action="{{ route('posts.update', $post) }}" method="POST" enctype="multipart/form-data">
        @method('PUT')
        @include('admin.posts.form')
    </form>
@endsection
