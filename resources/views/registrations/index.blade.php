@extends('layouts.app')

@section('title', 'Trang cá nhân | Giasu707')

@section('content')
    <div class="card p-4 p-md-5 profile-card">
        <div class="text-center">
            <img src="{{ asset('images/avatar.png') }}" class="rounded-circle shadow mb-3" width="150" alt="Ảnh đại diện">
            <h1 class="mb-1">{{ $registration->name ?? 'Tên người dùng' }}</h1>
            <p class="text-muted mb-3">{{ $registration->email ?? '' }}</p>
            <a href="{{ route('registration.create') }}" class="btn btn-primary mb-4">Cập nhật thông tin</a>
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
            <div class="tab-pane fade show active" id="recent-views" role="tabpanel">
                <div class="row collection-container pt-3" id="recent-views-container">
                    <!-- JS render recent views -->
                </div>
            </div>

            <div class="tab-pane fade" id="favorites" role="tabpanel">
                <div class="row collection-container pt-3" id="favorites-container"></div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const clientId = localStorage.getItem('client_id');
            if (!clientId) return alert("Không tìm thấy client_id.");

            fetch(`/api/registration/${clientId}`)
                .then(res => res.ok ? res.json() : Promise.reject("Không tìm thấy thông tin."))
                .then(data => {
                    renderUserInfo(data);
                    renderRecentViews(data);
                    renderFavorites(data);
                })
                .catch(err => alert(err));

            function renderUserInfo(data) {
                document.querySelector("h1").textContent = data.name;
                document.querySelector(".text-muted").textContent = data.email;
            }

            function renderFavorites(data) {
                renderCollectionList("#favorites-container", data.favorites);
            }

            function renderRecentViews(data) {
                renderCollectionList("#recent-views-container", data.recent_views);
            }

            function renderCollectionList(containerId, items) {
                const container = document.querySelector(containerId);
                if (!items || items.length === 0) {
                    container.innerHTML = `<p class="text-muted text-center">Chưa có mục nào.</p>`;
                    return;
                }
                container.innerHTML = '';
                items.forEach(item => {
                    const url = item.url || '#';
                    const imgHTML = item.image ? `<div class="square-box"><img src="/${item.image}" class="centered-img" alt="${item.title}"></div>` : `<div class="square-box"><span style="font-size:20px">${item.type}</span></div>`;
                    const cardHTML = `
                        <div class="col-md-3 mb-4">
                            <div class="card h-100">
                                <a href="${url}">${imgHTML}</a>
                                <div class="card-body text-center">
                                    <h5><a href="${url}">${item.title}</a></h5>
                                </div>
                            </div>
                        </div>`;
                    container.innerHTML += cardHTML;
                });
            }
        });

    </script>
@endsection