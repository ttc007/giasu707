@extends('layouts.admin')

@section('content')
<h2>Cập nhật đề thi</h2>

<form method="POST" action="{{ route('exams.update', $exam) }}">
    @csrf @method('PUT')
    <div class="mb-3">
        <label>Tiêu đề</label>
        <input type="text" name="title" value="{{ $exam->title }}" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Mô tả</label>
        <textarea name="description" class="form-control" rows="3">{{ $exam->description }}</textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Thuộc môn học</label>
        <select name="subject_id" class="form-select" required>
            <option value="">-- Chọn môn học --</option>
            @foreach ($subjects as $subject)
                <option value="{{ $subject->id }}" {{ $exam->subject_id == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
            @endforeach
        </select>
    </div>

    <button class="btn btn-success">Cập nhật</button>
    <a href="{{ route('exams.index') }}" class="btn btn-secondary">Quay lại</a>
</form>

<h3 class="pt-5">Cấu trúc đề thi {{$exam->title}}</h3>
<!-- Ba div chia theo loại câu hỏi -->
<div class="mb-4">
    <h4>Trắc nghiệm</h4>
    <div id="mcq-list">
        @foreach($exam->questions->where('type', 'multiple_choice') as $index => $question)
            <div class="card p-3 mb-2">
                <div><b>Câu {{$index + 1}}:</b>{!! $question->content !!}</div>
                <button class="btn btn-danger mt-2" onclick="removeFromExam({{ $exam->id }}, {{ $question->id }}, this)">- Gỡ khỏi đề</button>
            </div>
        @endforeach
    </div>
</div>

<div class="mb-4">
    <h4>Đúng / Sai</h4>
    <div id="truefalse-list">
        @foreach($exam->questions->where('type', 'true_false') as $index => $question)
            <div class="card p-3 mb-2">
                <div><b>Câu {{$index + 1}}:</b>{!! $question->content !!}</div>
                <button class="btn btn-danger mt-2" onclick="removeFromExam({{ $exam->id }}, {{ $question->id }}, this)">- Gỡ khỏi đề</button>
            </div>
        @endforeach
    </div>
</div>

<div class="mb-4">
    <h4>Điền kết quả</h4>
    <div id="fillblank-list">
        @foreach($exam->questions->where('type', 'fill_blank') as $index => $question)
            <div class="card p-3 mb-2">
                <div><b>Câu {{$index + 1}}:</b>{!! $question->content !!}</div>
                <button class="btn btn-danger mt-2" onclick="removeFromExam({{ $exam->id }}, {{ $question->id }}, this)">- Gỡ khỏi đề</button>
            </div>
        @endforeach
    </div>
</div>


<!-- Nếu cần: hidden input chứa id đề thi -->
<input type="hidden" id="exam_id" value="{{ $exam->id }}">
<input type="hidden" id="subject_id" value="{{ $exam->subject_id }}">
<script>
    function removeFromExam(examId, questionId, button) {
        if (!confirm('Bạn có chắc muốn gỡ câu hỏi này khỏi đề không?')) return;

        fetch(`/api/exams/${examId}/questions/${questionId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Có lỗi xảy ra');
            // Xoá phần tử trong DOM
            const wrapper = button.closest('.card');
            if (wrapper) wrapper.remove();
        })
        .catch(error => {
            alert('Lỗi khi xoá câu hỏi khỏi đề.');
            console.error(error);
        });
    }

</script>

@endsection
