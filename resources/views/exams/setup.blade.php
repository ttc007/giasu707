@extends('layouts.app')

@section('title', 'Thi thử | Giasu707')

@section('content')
<div class="container card p-3">
    <h1 class="mb-4">Thi thử</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <form action="{{ route('thi-thu.start') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="subject" class="form-label">Chọn môn:</label>
            <select id="subject" name="subject_id" class="form-select w-auto d-inline-block" onchange="onSubjectChange()" required>
                <option value="">-- Chọn môn --</option>
                @foreach ($subjects as $subject)
                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="mode" class="form-label">Chế độ chọn đề:</label>
            <select id="mode" name="mode" class="form-select w-auto d-inline-block" onchange="onModeChange()">
                <option value="random" selected>Ngẫu nhiên</option>
                <option value="ordered">Theo thứ tự</option>
            </select>

            <div class="d-inline-block ms-3" id="question-number-wrapper" style="display: none!important;">
                <label for="question-number">Lựa chọn đề:</label>
                <select id="exam_id" name="exam_id" class="form-select d-inline-block w-auto"></select>
            </div>
        </div>
        <i class="text-danger">Chọn môn và chế độ thi để bắt đầu kiểm tra năng lực ngay!</i><br>

        <button type="submit" class="btn btn-primary mt-3">Bắt đầu thi</button>
    </form>
</div>

<script>
function onModeChange() {
    const mode = document.getElementById('mode').value;
    const wrapper = document.getElementById('question-number-wrapper');

    if (mode === 'ordered') {
        
        onSubjectChange(); // Gọi luôn nếu có sẵn subject
    } else {
        wrapper.style.setProperty('display', 'none', 'important');
    }
}

function onSubjectChange() {
    const subjectId = document.getElementById('subject').value;
    const mode = document.getElementById('mode').value;
    const examSelect = document.getElementById('exam_id');
    const wrapper = document.getElementById('question-number-wrapper');

    if (mode !== 'ordered' || !subjectId || subjectId === '-- Chọn môn --') {
        wrapper.style.setProperty('display', 'none', 'important');
        return
    };

    // Gọi API để lấy danh sách đề
    fetch(`/api/subject/${subjectId}/exams`)
        .then(response => response.json())
        .then(data => {
            // Làm sạch select cũ
            examSelect.innerHTML = '';
            if (data.length === 0) {
                examSelect.innerHTML = '<option>Không có đề</option>';
                return;
            }

            // Render các đề
            data.forEach(exam => {
                const opt = document.createElement('option');
                opt.value = exam.id;
                opt.textContent = exam.title ?? `Đề ${exam.id}`;
                examSelect.appendChild(opt);
            });
            wrapper.style.display = 'inline-block';
        })
        .catch(err => {
            console.error('Lỗi khi load danh sách đề:', err);
        });
}
</script>

@endsection
