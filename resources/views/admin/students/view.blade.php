@extends('layouts.admin') {{-- hoặc layout bạn đang dùng --}}

@section('content')
<div class="container">
    <h3>Danh sách đăng ký học</h3>
    <table class="table table-bordered table-striped mt-3">
        <thead>
            <tr>
                <th>#</th>
                <th>User Argent</th>
                <th>IP</th>
                <th>Model</th>
                <th>Model ID</th>
                <th>Created at</th>
                <th>Updated at</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($views as $student)
                <tr>
                    <td>{{ $loop->iteration + ($views->currentPage() - 1) * $views->perPage() }}</td>
                    <td>{{ $student->user_agent }}</td>
                    <td>{{ $student->ip_address }}</td>
                    <td>{{ $student->model_type }}</td>
                    <td>{{ $student->model_id }}</td>
                    <td>{{ $student->created_at }}</td>
                    <td>{{ $student->updated_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $views->links('vendor.pagination.bootstrap-5') }}
</div>
@endsection
