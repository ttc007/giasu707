@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Thêm câu hỏi mới</h2>

    <form action="{{ route('questions.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Môn học:</label>
            <select id="subject_id" class="form-control" required>
                <option value="">-- Select Subject --</option>
                @foreach ($subjects as $subject)
                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Chương:</label>
            <select id="chapter_id" class="form-control" required></select>
        </div>

        <div class="mb-3">
            <label>Bài học:</label>
            <select id="lesson_id" class="form-control" required></select>
        </div>

        <div class="mb-3">
            <label>Phần</label>
            <select name="section_id" id="section_id" class="form-control" required></select>
        </div>

        <div class="mb-3">
            <label>Loại câu hỏi</label>
            <select name="type" class="form-control" required>
                <option value="multiple_choice">Trắc nghiệm</option>
                <option value="true_false">Đúng / Sai</option>
                <option value="fill_blank">Điền kết quả</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Nội dung câu hỏi</label>
            <textarea name="content" class="form-control editor" rows="5" id="ckeditor-content"></textarea>
        </div>

        <div class="mb-3">
            <label>Bài giải</label>
            <textarea name="solution" class="form-control editor" rows="5" id="ckeditor-solution"></textarea>
        </div>

        <div class="mb-3">
            <label>Đáp án</label>
            <input type="text" name="answer" class="form-control">
        </div>

        <div class="mb-3">
            <label>Thuộc đề thi:</label>
            <div id="exam-list" class="d-flex flex-wrap gap-3"></div>
        </div>

        <button class="btn btn-success">Lưu</button>
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
                    const checkbox = `
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="exam_ids[]" value="${exam.id}" >
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
});
</script>
@endsection

