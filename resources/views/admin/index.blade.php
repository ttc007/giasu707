@extends('layouts.admin')

@section('title', 'Danh sách Môn học')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Thống kê lượt xem</h2>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table">
        <thead>
            <tr>
                <th>Loại</th>
                <th>Tiêu đề</th>
                <th>Lượt xem</th>
            </tr>
        </thead>
        <tbody>
            @foreach($views as $view)
                <tr>
                    <td>{{ $view->model_type }}</td>
                    <td>
                        {{ $view->title }}
                    </td>
                    <td>{{ $view->total_views }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $views->links('vendor.pagination.bootstrap-5') }}

</div>
@endsection
