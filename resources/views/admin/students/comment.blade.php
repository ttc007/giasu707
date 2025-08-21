@extends('layouts.admin') {{-- hoặc layout bạn đang dùng --}}

@section('content')
<div class="container">
    <h3>Danh sách đăng ký học</h3>
    <table class="table table-bordered table-striped mt-3">
        <thead>
            <tr>
                <th>#</th>
                <th>Registration ID</th>
                <th>Model</th>
                <th>Model ID</th>
                <th>Comment</th>
                <th>Created at</th>
                <th>Updated at</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($comments as $student)
                <tr>
                    <td>{{ $loop->iteration + ($comments->currentPage() - 1) * $comments->perPage() }}</td>
                    <td>{{ $student->registration_id }}</td>
                    <td>{{ $student->model_type }}</td>
                    <td>{{ $student->model_id }}</td>
                    <td>{{ $student->content }}</td>
                    <td>{{ $student->created_at }}</td>
                    <td>{{ $student->updated_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $comments->links('vendor.pagination.bootstrap-5') }}
</div>
@endsection
