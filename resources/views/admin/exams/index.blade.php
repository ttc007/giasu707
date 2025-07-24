@extends('layouts.admin')

@section('content')
<h2>Danh sách đề thi</h2>
<a href="{{ route('exams.create') }}" class="btn btn-success mb-3">+ Tạo đề thi</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Tiêu đề</th>
            <th>Số câu hỏi</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($exams as $exam)
        <tr>
            <td>{{ $exam->title }}</td>
            <td>{{ $exam->questions->count() }}</td>
            <td>
                <a href="{{ route('exams.edit', $exam) }}" class="btn btn-primary btn-sm">Sửa</a>
                <form action="{{ route('exams.destroy', $exam) }}" method="POST" style="display:inline-block">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger btn-sm" onclick="return confirm('Xoá?')">Xoá</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{ $exams->links() }}
@endsection
