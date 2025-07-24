@extends('layouts.admin')

@section('content')
    <div class="container">
        <h2>Lessons</h2>
        <a href="{{ route('lessons.create') }}" class="btn btn-primary mb-3">Tạo lesson</a>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Tiêu đề</th>
                    <th>Chương</th>
                    <th>Môn học</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($lessons as $lesson)
                    <tr>
                        <td>{{ $lesson->title }}</td>
                        <td>{{ $lesson->chapter->title ?? '' }}</td>
                        <td>{{ $lesson->chapter->subject->name ?? '' }}</td>
                        <td>
                            <a href="{{ route('lessons.edit', $lesson) }}" class="btn btn-sm btn-primary">Sửa</a>
                            <form action="{{ route('lessons.destroy', $lesson) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Delete this lesson?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $lessons->links() }}
    </div>
@endsection
