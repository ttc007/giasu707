@extends('layouts.admin')

@section('title', 'Tạo chương')

@section('content')
    <h2>📘 Tạo chương mới</h2>
    <form method="POST" action="{{ route('chapters.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label class="form-label">Tên chương</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Ảnh đại diện</label>
            <input type="file" name="image" class="form-control" accept="image/*">
        </div>

        <div class="mb-3">
            <label class="form-label">Thuộc môn học</label>
            <select name="subject_id" class="form-select" required>
                <option value="">-- Chọn môn học --</option>
                @foreach ($subjects as $subject)
                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Summary:</label>
            <textarea name="summary" id="editor" class="form-control" rows="6"></textarea>
        </div>

        <button class="btn btn-success">Lưu</button>
        <a href="{{ route('chapters.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>

    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <script>
    ClassicEditor
        .create(document.querySelector('#editor'), {
            ckfinder: {
                uploadUrl: '/upload?_token={{ csrf_token() }}'
            }
        })
        .catch(error => {
            console.error(error);
        });
    </script>
@endsection
