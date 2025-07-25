@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Danh sách câu hỏi</h2>
    <a href="{{ route('questions.create') }}" class="btn btn-primary mb-3">+ Thêm câu hỏi</a>

    <form method="GET" action="{{ route('questions.index') }}" class="mb-4">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Từ khoá nội dung</label>
                <input type="text" name="search" class="form-control" placeholder="..." value="{{ request('search') }}">
            </div>

            <div class="col-md-4">
                <label class="form-label">Môn học</label>
                <select name="subject_id" id="subject_id" class="form-select">
                    <option value="">-- Tất cả --</option>
                    @foreach ($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Chương</label>
                <select name="chapter_id" id="chapter_id" class="form-select">
                    <option value="">-- Tất cả --</option>
                    @foreach ($chapters as $chapter)
                        <option value="{{ $chapter->id }}" {{ request('chapter_id') == $chapter->id ? 'selected' : '' }}>
                            {{ $chapter->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Bài học</label>
                <select name="lesson_id" id="lesson_id" class="form-select">
                    <option value="">-- Tất cả --</option>
                    @foreach ($lessons as $lesson)
                        <option value="{{ $lesson->id }}" {{ request('lesson_id') == $lesson->id ? 'selected' : '' }}>
                            {{ $lesson->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Loại</label>
                <select name="type" class="form-select">
                    <option value="multiple_choice" {{ request('type') == 'multiple_choice' ? 'selected' : ''}}>Trắc nghiệm</option>
                    <option value="true_false" {{ request('type') == 'true_false' ? 'selected' : ''}}>Đúng / Sai</option>
                    <option value="fill_blank" {{ request('type') == 'fill_blank' ? 'selected' : ''}}>Điền kết quả</option>
                </select>
            </div>

            <div class="col-md-1">
                <button class="btn btn-primary w-100">Tìm</button>
            </div>
        </div>
    </form>



    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Phần</th>
                <th>Loại</th>
                <th>Nội dung</th>
                <th>Đáp án</th>
                <th>Bài giải</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
        @foreach($questions as $question)
            <tr>
                <td>{{ $question->id }}</td>
                <td><b>{{ $question->section->title ?? '-' }}</b><br>
                    {{ $question->section->lesson->chapter->subject->name ?? 'N/A' }} -
                    {{ $question->section->lesson->chapter->title ?? 'N/A' }}<br>
                    {{ $question->section->lesson->title ?? 'N/A' }}<br>

                </td>
                <td>{{ ucfirst(str_replace('_', ' ', $question->type)) }}</td>
                <td>{!! $question->content !!}</td>
                <td>{{ $question->answer }}</td>
                <td>{!! $question->solution !!}</td>
                <td>
                    <a href="{{ route('questions.edit', $question) }}" class="btn btn-sm btn-primary">Sửa</a>
                    <form action="{{ route('questions.destroy', $question) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Xóa?')">Xóa</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $questions->links() }}
</div>
@endsection
