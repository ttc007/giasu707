<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Gia sư 707')</title>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/icon.png') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
        }
        header, footer {
            background: #a7c5eb;
            color: white;
            padding: 1rem;
        }
        nav {
            background: ##a7c5eb;
        }
        nav a {
            color: white !important;
            margin-right: 1rem;
            text-decoration: none;
        }
        
        footer {
            margin-top: 2rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <img src="{{ asset('images/banner.png') }}" alt="Banner giáo dục" class="img-fluid w-100  shadow">
            </div>
        </div>
        <nav class="navbar navbar-expand-lg navbar-dark bg-info">
            <a class="navbar-brand px-2" href="{{ url('/') }}" >Gia sư 707</a>
            <!-- Nút toggle khi thu gọn -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Các link sẽ bị thu gọn -->
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/') }}">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/ly12') }}">Lý 12</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/khoahoc') }}">Khóa học</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/thaoluan') }}">Thảo luận</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/baiviet') }}">Bài viết</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/gioithieu') }}">Giới thiệu</a>
                    </li>
                </ul>
            </div>
        </nav>

        <main class="py-4 px-2">
            @yield('content')
        </main>

        <footer>
            © {{ date('Y') }} Gia sư 707 - Made with ❤️ ở quê
        </footer>
    </div>
</body>
</html>
