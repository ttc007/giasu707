@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h2>Thêm Danh mục</h2>
    @include('admin.categories._form', ['route' => route('categories.store'), 'method' => 'POST'])
</div>
@endsection
