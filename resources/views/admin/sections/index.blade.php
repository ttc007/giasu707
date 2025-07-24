@extends('layouts.admin')

@section('content')
    <h2>Section List</h2>
    <a href="{{ route('sections.create') }}" class="btn btn-primary mb-3">Tạo Section</a>

    <table class="table">
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

    {{ $sections->links() }}
@endsection
