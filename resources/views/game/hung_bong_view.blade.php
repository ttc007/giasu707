@extends('layouts.app')

@section('content')
<style type="text/css">
    .game-not-show {
        display: none;
    }
</style>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-success fw-bold"><i class="bi bi-controller"></i> Game Hứng Bóng</h2>
        <a href="{{ url('/') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại trang chủ
        </a>
    </div>

    <div class="row">
        <div class="col-lg-9">
            <div class="card shadow-lg border-0 overflow-hidden" style="border-radius: 15px;">
                <div class="ratio ratio-16x9">
                    <iframe 
                        src="{{ asset('games/hung_bong/index.html') }}" 
                        style="border:none;" 
                        allowfullscreen>
                    </iframe>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="card border-0 shadow-sm p-3 bg-light">
                <h5 class="fw-bold text-dark">Hướng dẫn</h5>
                <p class="small text-muted">- Dùng phím mũi tên để di chuyển.<br>- Hứng bóng để ghi điểm.</p>
                <hr>
                <div id="status-box" class="text-center p-2 rounded bg-white border">
                    <span class="small text-muted">Trạng thái: </span>
                    <span class="badge bg-primary">Đang kết nối...</span>
                </div>
            </div>
        </div>
    </div>
</div>

<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
    window.addEventListener('message', function(event) {
        // Kiểm tra nếu đúng là tín hiệu Game Ready
        if (event.data.type === 'SCRATCH_READY') {
            const statusBadge = document.querySelector('#status-box .badge');
            
            statusBadge.innerText = 'Đã kết nối';
            statusBadge.classList.remove('bg-primary');
            statusBadge.classList.add('bg-success'); // Đổi sang màu xanh lá
        }

        // Bạn có thể thêm các tín hiệu khác như 'SCORE_UPDATED' để cập nhật UI trang cha
        if (event.data.type === 'SCORE_SAVED') {
            const statusBadge = document.querySelector('#status-box .badge');
            statusBadge.innerText = 'Đã lưu điểm';
            setTimeout(() => {
                statusBadge.innerText = 'Đang chơi';
            }, 2000);
        }
    });
</script>
@endsection