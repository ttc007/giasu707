@extends('layouts.admin')

@section('content')
    <h2>Create Section</h2>

    <form action="{{ route('sections.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Subject:</label>
            <select id="subject_id" class="form-control" required>
                <option value="">-- Select Subject --</option>
                @foreach ($subjects as $subject)
                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Chapter:</label>
            <select id="chapter_id" class="form-control" required></select>
        </div>

        <div class="mb-3">
            <label>Lesson:</label>
            <select name="lesson_id" id="lesson_id" class="form-control" required></select>
        </div>

        <div class="mb-3">
            <label>Title:</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Content:</label>
            <textarea name="content" id="editor" class="form-control" rows="6">{{ old('content', $section->content ?? '') }}</textarea>
        </div>


        <button class="btn btn-primary">Tạo Section</button>
        <a href="{{ route('sections.index') }}" class="btn btn-secondary">Quay lại</a>
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
