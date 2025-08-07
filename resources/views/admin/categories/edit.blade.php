@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h2>Sửa Danh mục</h2>
    @include('admin.categories._form', [
        'route' => route('categories.update', $category),
        'method' => 'PUT',
        'category' => $category
    ])
</div>
@endsection
