@extends('layouts.admin')

@section('title', 'Danh sách chương')

@section('content')
    <h2>📘 Danh sách chương</h2>
    <a href="{{ route('chapters.create') }}" class="btn btn-primary mb-3">+ Thêm chương</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Tên chương</th>
                <th>Thuộc môn</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($chapters as $chapter)
                <tr>
                    <td>{{ $chapter->title }}</td>
                    <td>{{ $chapter->subject->name }}</td>
                    <td>
                        <a href="{{ route('chapters.edit', $chapter) }}" class="btn btn-sm btn-primary">Sửa</a>
                        <form action="{{ route('chapters.destroy', $chapter) }}" method="POST" style="display:inline-block">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Xoá chương?')">Xoá</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Chưa có môn học nào.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection


