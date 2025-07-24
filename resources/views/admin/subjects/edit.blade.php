@extends('layouts.admin')

@section('content')
<div class="container">
    <h2 class="mb-4">Chỉnh sửa môn học</h2>

    <form action="{{ route('subjects.update', $subject->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Tên môn học</label>
            <input type="text" name="name" id="name" class="form-control"
                   value="{{ old('name', $subject->name) }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Cập nhật</button>
        <a href="{{ route('subjects.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection
