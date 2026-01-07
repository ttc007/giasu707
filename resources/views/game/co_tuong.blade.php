<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cờ Tướng Online - Phaser 3</title>
    <link rel="icon" type="image/png" href="{{ asset('images/icon.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column; /* Xếp theo cột để nút nằm trên/dưới game */
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #2c3e50;
            font-family: 'Montserrat', sans-serif;
        }

        #game-container {
            box-shadow: 0 0 20px rgba(0,0,0,0.5);
            border-radius: 8px;
            overflow: hidden;
            position: relative;
        }

        /* Style cho nút Home */
        .home-button {
            position: absolute;
            top: 20px;
            left: 20px;
            padding: 10px 20px;
            background-color: #e74c3c; /* Màu đỏ nổi bật */
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            font-size: 14px;
            transition: background 0.3s ease, transform 0.2s;
            z-index: 100; /* Luôn nằm trên cùng */
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.2);
        }

        .home-button:hover {
            background-color: #c0392b;
            transform: translateY(-2px);
        }

        .home-button:active {
            transform: translateY(0);
        }

        /* Icon mũi tên đơn giản bằng CSS */
        .home-button::before {
            content: '←';
            font-size: 18px;
        }

        #loading-screen {
            position: absolute;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8); /* Nền tối */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999; /* Luôn nằm trên cùng */
            color: white;
            font-family: Arial, sans-serif;
        }

        /* Vòng xoay Loading */
        .loader {
            border: 8px solid #f3f3f3;
            border-top: 8px solid #db3434; /* Màu đỏ cờ tướng */
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .controls {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        #toggle-auto-ai {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            transition: 0.3s;
            font-weight: bold;
        }
        .btn-auto-off { background-color: #666; color: white; }
        .btn-auto-on { background-color: #2ecc71; color: white; box-shadow: 0 0 10px #2ecc71; }

        .btn-icon {
            background-color: #f39c12; /* Màu cam nổi bật cho Restart */
            color: white;
            border: none;
            border-radius: 50%; /* Làm nút hình tròn */
            width: 35px;
            height: 35px;
            cursor: pointer;
            font-size: 20px;
            display: inline;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s, background-color 0.2s;
        }

        .btn-icon:hover {
            background-color: #e67e22;
            transform: rotate(-45deg); /* Hiệu ứng xoay nhẹ khi hover */
        }

        .btn-icon:active {
            transform: scale(0.9); /* Hiệu ứng nhấn nút */
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/phaser@3.60.0/dist/phaser.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@900&display=swap" rel="stylesheet">
</head>
<body>
    <div id="loading-screen">
        <div class="loader"></div>
        <p>Đang tải bàn cờ...</p>
    </div>

    <a href="/" class="home-button">TRANG CHỦ</a>

    <div id="game-container"></div>

    <div class="controls">
        <button id="toggle-auto-ai" class="btn-auto-off">Auto AI: OFF</button>
        <button id="btn-restart" class="btn-icon" title="Restart Game">
            &#8635;
        </button>
    </div>

    <script src="{{ asset('games/co_tuong/game.js') }}?t=time()"></script>
    <script src="{{ asset('games/co_tuong/move.js') }}?t=time()"></script>
    <script src="{{ asset('games/co_tuong/ai.js') }}?t=time()"></script>
</body>
</html>