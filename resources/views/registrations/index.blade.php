@extends('layouts.app')

@section('title', 'Trang cá nhân | Giasu707')

@section('content')
    <div class="card p-4 p-md-5 profile-card">
        <div class="text-center">
            <img src="{{ asset('images/avatar.png') }}" class="rounded-circle shadow mb-3" width="150" alt="Ảnh đại diện">
            <h1 class="mb-1">{{ $registration->name ?? 'Tên người dùng' }}</h1>
            <p class="text-muted mb-3">{{ $registration->email ?? '' }}</p>
            @if ($registration->created_at == $registration->updated_at)
                <a href="{{ route('registration.create') }}" class="btn btn-primary mx-2">Cập nhật thông tin</a>
            @endif
            <a href="{{route('student.logout')}}" class="btn btn-outline-secondary">Đăng xuất</a>
        </div>

        <ul class="nav nav-tabs" id="profileTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="recent-views-tab" data-bs-toggle="tab" href="#recent-views" role="tab">Xem gần đây</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="favorite-tab" data-bs-toggle="tab" href="#favorites" role="tab">Yêu thích</a>
            </li>
        </ul>

        <div class="tab-content mt-4" id="profileTabsContent">
            <!-- Recent Views -->
            <div class="tab-pane fade show active" id="recent-views" role="tabpanel">
                <div class="row collection-container pt-3">
                    @forelse($recentViews ?? [] as $item)
                        <div class="col-md-3 mb-4">
                            <div class="card h-100">
                                <a href="{{ $item['url'] ?? '#' }}">
                                    @if(!empty($item['image']))
                                        <div class="square-box">
                                            <img src="/{{ $item['image'] }}" class="centered-img" alt="{{ $item['title'] ?? '' }}">
                                        </div>
                                    @else
                                        <div class="square-box">
                                            <img src="{{ asset('images/lesson_default.jpg') }}" class="centered-img">
                                        </div>
                                    @endif
                                </a>
                                <div class="card-body text-center">
                                    <h5>
                                        <a href="{{ $item['url'] ?? '#' }}">{{ $item['title'] ?? '' }}</a>
                                    </h5>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center">Chưa có mục nào.</p>
                    @endforelse
                </div>
            </div>

            <!-- Favorites -->
            <div class="tab-pane fade" id="favorites" role="tabpanel">
                <div class="row collection-container pt-3">
                    @forelse($favorites ?? [] as $item)
                        <div class="col-md-3 mb-4">
                            <div class="card h-100">
                                <a href="{{ $item['url'] ?? '#' }}">
                                    @if(!empty($item['image']))
                                        <div class="square-box">
                                            <img src="/{{ $item['image'] }}" class="centered-img" alt="{{ $item['title'] ?? '' }}">
                                        </div>
                                    @else
                                        <div class="square-box">
                                            <img src="{{ asset('images/lesson_default.jpg') }}" class="centered-img">
                                        </div>
                                    @endif
                                </a>
                                <div class="card-body text-center">
                                    <h5>
                                        <a href="{{ $item['url'] ?? '#' }}">{{ $item['title'] ?? '' }}</a>
                                    </h5>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center">Chưa có mục nào.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
