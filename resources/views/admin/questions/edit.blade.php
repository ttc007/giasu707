@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Chỉnh sửa câu hỏi</h2>

    <form action="{{ route('questions.update', $question) }}" method="POST">
        @csrf @method('PUT')

        <div class="mb-3">
            <label>Subject:</label>
            <select id="subject_id" class="form-control" required>
                <option value="">-- Select Subject --</option>
                @foreach ($subjects as $subject)
                    <option value="{{ $subject->id }}" {{ $subject->id == $question->section->lesson->chapter->subject_id ? 'selected' : '' }}>
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
                    <option value="{{ $chapter->id }}" {{ $chapter->id == $question->section->lesson->chapter_id ? 'selected' : '' }}>
                        {{ $chapter->title }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Lesson:</label>
            <select id="lesson_id" class="form-control" required>
                <option value="">-- Select Lesson --</option>
                @foreach ($lessons as $lesson)
                    <option value="{{ $lesson->id }}" {{ $lesson->id == $question->section->lesson_id ? 'selected' : '' }}>
                        {{ $lesson->title }}
                    </option>
                @endforeach
            </select>
        </div>


        <div class="mb-3">
            <label>Section</label>
            <select name="section_id" class="form-control" required>
                @foreach ($sections as $section)
                    <option value="{{ $section->id }}" {{ $section->id == $question->section_id ? 'selected' : '' }}>
                        {{ $section->title }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Loại câu hỏi</label>
            <select name="type" class="form-control" required>
                <option value="multiple_choice" {{ $question->type == 'multiple_choice' ? 'selected' : '' }}>Trắc nghiệm</option>
                <option value="true_false" {{ $question->type == 'true_false' ? 'selected' : '' }}>Đúng / Sai</option>
                <option value="fill_blank" {{ $question->type == 'fill_blank' ? 'selected' : '' }}>Điền kết quả</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Cấp độ</label>
            <select name="level" class="form-control" required>
                <option value="Nhận biết" {{ $question->level == 'Nhận biết' ? 'selected' : '' }}>Nhận biết</option>
                <option value="Thông hiểu" {{ $question->level == 'Thông hiểu' ? 'selected' : '' }}>Thông hiểu</option>
                <option value="Vận dụng" {{ $question->level == 'Vận dụng' ? 'selected' : '' }}>Vận dụng</option>
                <option value="Vận dụng cao" {{ $question->level == 'Vận dụng cao' ? 'selected' : '' }}>Vận dụng cao</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Nội dung câu hỏi</label>
            <textarea name="content" class="form-control" rows="5" id="ckeditor-content">{{ $question->content }}</textarea>
        </div>

        <div class="mb-3">
            <label>Bài giải</label>
            <textarea name="solution" class="form-control" rows="5" id="ckeditor-solution">{{ $question->solution }}</textarea>
        </div>

        <div class="mb-3">
            <label>Đáp án</label>
            <input type="text" name="answer" value="{{ $question->answer }}" class="form-control">
        </div>

        <div class="mb-3">
            <label>Thuộc đề thi:</label>
            <div id="exam-list" class="d-flex flex-wrap gap-3"></div>
        </div>



        <button class="btn btn-success">Cập nhật</button>
        <a href="{{ route('questions.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
ClassicEditor
    .create(document.querySelector('#ckeditor-content'), {
        ckfinder: {
            uploadUrl: '/upload?_token={{ csrf_token() }}'
        }
    })
    .catch(error => {
        console.error(error);
    });

ClassicEditor
    .create(document.querySelector('#ckeditor-solution'), {
        ckfinder: {
            uploadUrl: '/upload?_token={{ csrf_token() }}'
        }
    })
    .catch(error => {
        console.error(error);
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const subjectSelect = document.getElementById('subject_id');
    const examListDiv = document.getElementById('exam-list');
    const selectedExamIds = @json($selectedExamIds);

    function loadExams(subjectId) {
        if (!subjectId) {
            examListDiv.innerHTML = '';
            return;
        }

        fetch(`/api/subject/${subjectId}/exams`)
            .then(response => response.json())
            .then(data => {
                examListDiv.innerHTML = '';
                data.forEach(exam => {
                    const isChecked = selectedExamIds.includes(exam.id);
                    const checkbox = `
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="exam_ids[]" value="${exam.id}" ${isChecked ? 'checked' : ''}>
                            <label class="form-check-label">${exam.title}</label>
                        </div>`;
                    examListDiv.insertAdjacentHTML('beforeend', checkbox);
                });
            });
    }

    subjectSelect.addEventListener('change', function () {
        const subjectId = this.value;
        loadExams(subjectId);
    });

    // Load lần đầu nếu có subject
    if (subjectSelect.value) {
        loadExams(subjectSelect.value);
    }
});
</script>

@endsection
