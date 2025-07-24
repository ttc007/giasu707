@extends('layouts.admin')

@section('content')
    <div class="container">
        <h2>Create Lesson</h2>
        <form action="{{ route('lessons.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="title">Title:</label>
                <input type="text" name="title" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="subject_id">Subject:</label>
                <select name="subject_id" id="subject_id" class="form-control" required>
                    <option value="">-- Select Subject --</option>
                    @foreach ($subjects as $subject)
                        <option value="{{ $subject->id }}" 
                            {{ old('subject_id', $lesson->chapter->subject_id ?? '') == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="chapter_id">Chapter:</label>
                <select name="chapter_id" id="chapter_id" class="form-control" required>
                    <option value="">-- Select Chapter --</option>
                    @foreach ($chapters as $chapter)
                        <option value="{{ $chapter->id }}"
                            {{ old('chapter_id', $lesson->chapter_id ?? '') == $chapter->id ? 'selected' : '' }}>
                            {{ $chapter->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label>Summary:</label>
                <textarea name="summary" id="editor" class="form-control" rows="6"></textarea>
            </div>

            <button class="btn btn-success">Create</button>
            <a href="{{ route('lessons.index') }}" class="btn btn-secondary">Quay lại</a>
        </form>
    </div>

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
