@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Danh sách tuyển tập</h2>
    <a href="{{ route('collections.create') }}" class="btn btn-primary mb-3">+ Thêm tuyển tập</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Tiêu đề</th>
                <th>Danh mục</th>
                <th>Mô tả</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($collections as $collection)
            <tr>
                <td>{{ $collection->title }}</td>
                <td>{{ $collection->category->name ?? 'Không có' }}</td>
                <td>{{ $collection->description }}</td>
                <td>
                    <a href="{{ route('collections.edit', $collection) }}" class="btn btn-sm btn-primary">Sửa</a>
                    <form action="{{ route('collections.destroy', $collection) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Xóa?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger">Xóa</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $collections->links() }}
</div>
@endsection
