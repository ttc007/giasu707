@extends('layouts.admin')

@section('content')
    <h2>Edit Section</h2>

    <form action="{{ route('sections.update', $section->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Subject:</label>
            <select id="subject_id" class="form-control" required>
                <option value="">-- Select Subject --</option>
                @foreach ($subjects as $subject)
                    <option value="{{ $subject->id }}" {{ $subject->id == $section->lesson->chapter->subject_id ? 'selected' : '' }}>
                        {{ $subject->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Chapter:</label>
            <select id="chapter_id" class="form-control" required>
                <option value="">-- Select Chapter --</option>
                @foreach ($chapters as $chapter)
                    <option value="{{ $chapter->id }}" {{ $chapter->id == $section->lesson->chapter_id ? 'selected' : '' }}>
                        {{ $chapter->title }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Lesson:</label>
            <select name="lesson_id" id="lesson_id" class="form-control" required>
                <option value="">-- Select Lesson --</option>
                @foreach ($lessons as $lesson)
                    <option value="{{ $lesson->id }}" {{ $lesson->id == $section->lesson_id ? 'selected' : '' }}>
                        {{ $lesson->title }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Title:</label>
            <input type="text" name="title" class="form-control" value="{{ $section->title }}" required>
        </div>

        <div class="mb-3">
            <label>Content:</label>
            <textarea name="content" id="editor" class="form-control" rows="6">{{ old('content', $section->content ?? '') }}</textarea>
        </div>


        <button class="btn btn-success">Update Section</button>
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

