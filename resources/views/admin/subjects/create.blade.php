@extends('layouts.admin')

@section('title', 'Thêm Môn Học')

@section('content')
<div class="container mt-4">
    <h2>Thêm Môn Học Mới</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Có lỗi xảy ra!</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('subjects.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Tên môn học</label>
            <input type="text" name="name" class="form-control" placeholder="Nhập tên môn" value="{{ old('name') }}" required>
        </div>

        <a href="{{ route('subjects.index') }}" class="btn btn-secondary">Quay lại</a>
        <button type="submit" class="btn btn-primary">Lưu</button>
    </form>
</div>
@endsection
