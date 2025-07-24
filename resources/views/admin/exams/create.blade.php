@extends('layouts.admin')

@section('content')
<h2>Tạo đề thi</h2>

<form method="POST" action="{{ route('exams.store') }}">
    @csrf
    <div class="mb-3">
        <label>Tiêu đề</label>
        <input type="text" name="title" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Mô tả</label>
        <textarea name="description" class="form-control" rows="3"></textarea>
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

    <button class="btn btn-primary">Lưu</button>
    <a href="{{ route('exams.index') }}" class="btn btn-secondary">Quay lại</a>
</form>
@endsection
