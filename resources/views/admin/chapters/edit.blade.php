@extends('layouts.admin')

@section('title', 'Sửa chương')

@section('content')
    <h2>📘 Sửa chương</h2>
    <form method="POST" action="{{ route('chapters.update', $chapter) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Tên chương</label>
            <input type="text" name="name" class="form-control" value="{{ $chapter->title }}" required>
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Ảnh đại diện</label>
            <input type="file" name="image" class="form-control" accept="image/*">
            
            @if(isset($chapter) && $chapter->image)
                <img src="{{ asset($chapter->image) }}" alt="Ảnh tuyển tập" style="max-width: 200px;">
            @endif
        </div>

        <div class="mb-3">
            <label class="form-label">Thuộc môn học</label>
            <select name="subject_id" class="form-select" required>
                @foreach ($subjects as $subject)
                    <option value="{{ $subject->id }}" {{ $chapter->subject_id == $subject->id ? 'selected' : '' }}>
                        {{ $subject->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Summary:</label>
            <textarea name="summary" id="editor" class="form-control" rows="6">{{ old('content', $chapter->summary ?? '') }}</textarea>
        </div>

        <button class="btn btn-primary">Cập nhật</button>
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
