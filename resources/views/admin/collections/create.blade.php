@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Thêm tuyển tập</h2>
    <form action="{{ route('collections.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label>Tiêu đề</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Ảnh đại diện</label>
            <input type="file" name="image" class="form-control" accept="image/*">
        </div>

        <div class="mb-3">
            <label>Danh mục</label>
            <select name="category_id" class="form-control" required>
                @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Mô tả</label>
            <textarea name="description" class="form-control"  id="editor"  rows="5">{{ old('content', $collection->description ?? '') }}</textarea>
        </div>
        <button class="btn btn-primary">Lưu</button>
    </form>
</div>
@endsection
