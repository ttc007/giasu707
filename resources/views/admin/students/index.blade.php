@extends('layouts.admin') {{-- hoặc layout bạn đang dùng --}}

@section('content')
<div class="container">
    <h3>Danh sách đăng ký học</h3>
    <table class="table table-bordered table-striped mt-3">
        <thead>
            <tr>
                <th>#</th>
                <th>Họ tên</th>
                <th>Email</th>
                <th>SĐT</th>
                <th>Môn học đăng kí</th>
                <th>Note</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($students as $student)
                <tr>
                    <td>{{ $loop->iteration + ($students->currentPage() - 1) * $students->perPage() }}</td>
                    <td>{{ $student->name }}</td>
                    <td>{{ $student->email }}</td>
                    <td>{{ $student->phone }}</td>
                    <td>{{ $student->subject }}</td>
                    <td>{{ $student->note }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $students->links() }}
</div>
@endsection
