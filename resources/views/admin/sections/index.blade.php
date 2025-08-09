@extends('layouts.admin')

@section('content')
    <h2>Section List</h2>
    <a href="{{ route('sections.create') }}" class="btn btn-primary mb-3">Tạo Section</a>

    <form method="GET" action="{{ route('sections.index') }}" class="mb-4">
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

            <div class="col-md-1">
                <button class="btn btn-primary w-100">Tìm</button>
            </div>
        </div>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Tiêu đề</th>
                <th>Nội dung</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sections as $section)
                <tr>
                    <td>{{ $section->id }}</td>
                    <td><b>{{ $section->title }}</b><br>
                    {{ $section->lesson->chapter->subject->name ?? 'N/A' }} - 
                    {{ $section->lesson->chapter->title ?? 'N/A' }}<br>
                    {{ $section->lesson->title ?? 'N/A' }}<br>

                    </td>
                    <td>{!!$section->content!!}</td>
                    <td>
                        <a href="{{ route('sections.edit', $section) }}" class="btn btn-sm btn-primary">Sửa</a>
                        <form action="{{ route('sections.destroy', $section) }}" method="POST" style="display:inline-block;">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this section?')">Xóa</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $sections->links('vendor.pagination.bootstrap-5') }}
@endsection
