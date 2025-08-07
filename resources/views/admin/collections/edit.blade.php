@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Sửa tuyển tập</h2>
    <form action="{{ route('collections.update', $collection) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label>Tên tuyển tập</label>
            <input type="text" name="title" class="form-control" value="{{ $collection->title }}" required>
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Ảnh đại diện</label>
            <input type="file" name="image" class="form-control" accept="image/*">
            
            @if(isset($collection) && $collection->image)
                <img src="{{ asset($collection->image) }}" alt="Ảnh tuyển tập" style="max-width: 200px;">
            @endif
        </div>

        <div class="mb-3">
            <label>Danh mục</label>
            <select name="category_id" class="form-control" required>
                @foreach($categories as $category)
                <option value="{{ $category->id }}" @if($category->id == $collection->category_id) selected @endif>
                    {{ $category->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Mô tả</label>
            <textarea name="description" class="form-control"  id="editor"  rows="5">{{ old('content', $collection->description ?? '') }}</textarea>
        </div>
        
        <button class="btn btn-primary">Cập nhật</button>
    </form>
</div>
@endsection
