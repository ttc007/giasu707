@extends('layouts.admin')

@section('content')
    <h1>Tạo bài viết</h1>
    <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
        @include('admin.posts.form')
    </form>
@endsection
