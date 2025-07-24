@extends('layouts.admin')

@section('title', 'Danh sách Môn học')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Danh sách Môn học</h2>
        <a href="{{ route('subjects.create') }}" class="btn btn-success">+ Thêm Môn học</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead class="table-primary">
            <tr>
                <th>#</th>
                <th>Tên môn học</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse($subjects as $index => $subject)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $subject->name }}</td>
                    <td>
                        <a href="{{ route('subjects.edit', $subject->id) }}" class="btn btn-sm btn-primary">Sửa</a>

                        <form action="{{ route('subjects.destroy', $subject->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Bạn có chắc muốn xoá môn học này?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" type="submit">Xoá</button>
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
</div>
@endsection
