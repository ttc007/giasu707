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
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
        }
        header, footer {
            background: #7cb342;
            color: white;
            padding: 1rem;
        }
        nav {
            background: #6eb816;
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

        a {
            color: #58a407;
            text-decoration:none;
        }

        h2, h4, h1 {
            color: #699238!important;
        }

        #timer {
            position: fixed;
            top: 320px;
            right: 230px;
            background-color: white;
            padding: 10px 15px;
            border: 2px solid red;
            border-radius: 5px;
            z-index: 1000;
            font-size: 24px;
            font-weight: bold;
            color: red;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .result {
            background-color: #f9f9f9; /* nền xám nhạt */
            border: 1px solid #ddd;    /* viền xám mờ */
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
            font-size: 14px;
        }

        /* Mobile: màn hình nhỏ hơn hoặc bằng 768px */
        @media (max-width: 768px) {
            #timer {
                top: 10px;
                right: 10px;
                font-size: 18px;
                padding: 8px 12px;
            }
        }

        .square-box {
            width: 100%;
            aspect-ratio: 1 / 1;
            background: #eee;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        .centered-img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }

        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            line-height: 1.7;
            background-color: #fafafa;
        }

        h1, h2, h3, h4 {
            color: #298f45; /* Màu xanh đậm hơn một chút */
        }

        ol li::marker {
            color: #27ae60; /* Xanh lá chuẩn cho đánh số */
        }

        h1, h2, h3 {
            font-weight: bold;
        }
        
        h1 {
            font-size: 2rem;
            border-bottom: 3px solid #27ae60;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }

        ol li::marker {
            color: #27ae60;
            font-weight: bold;
        }

        strong {
            color: #35a263; /* Xanh lá sáng và mềm hơn */
            font-weight: 600;
        }

        h3 strong {
            color: #699238;
        }

        h4 strong {
            color: #58a407;
        }

        header {
            background: linear-gradient(90deg, #27ae60, #2ecc71);
        }

        .section {
            background-color: #ffffff00;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 1rem;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 12px 15px;
            text-align: left;
        }

        table th {
            background-color: #27ae60;
            color: #fff;
            font-weight: bold;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        #exercise-area input.form-control {
            max-width: 300px;   /* Giới hạn chiều rộng */
            display: inline-block; 
            margin-right: 10px; /* Khoảng cách với nút */
        }

        #exercise-area button.btn {
            width: auto;        /* Chỉ vừa nội dung */
            padding: 6px 12px;  /* Gọn gàng hơn */
            max-width:300px;
        }

        .card{
            background: #ffffffa3;
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
        <nav class="navbar navbar-expand-lg navbar-dark">
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
                        <a class="nav-link" href="{{ url('/vat-li-12') }}">Vật lí 12</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/toan-12') }}">Toán 12</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/hoa-12') }}">Hóa 12</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/thi-thu') }}">Thi thử</a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/thu-vien') }}">Thư viện</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/trang-ca-nhan') }}"><img src="{{ asset('images/avatar.png') }}" class="rounded-circle shadow" width="25" alt="Ảnh đại diện"></a>
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
    <script>
      window.MathJax = {
        tex: {
          inlineMath: [['$', '$'], ['\\(', '\\)']]
        },
        startup: {
          typeset: true
        }
      };
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-chtml.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const CLIENT_KEY = 'client_id';

            if (!localStorage.getItem(CLIENT_KEY)) {
                fetch("/api/register-client", {
                    method: "POST",
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.client_id) {
                        localStorage.setItem(CLIENT_KEY, data.client_id);
                    }
                })
                .catch(error => {
                    console.error("Lỗi khi tạo client:", error);
                });
            }
        });
    </script>
</body>
</html>
