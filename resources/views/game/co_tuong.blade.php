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
    </style>
    <script src="https://cdn.jsdelivr.net/npm/phaser@3.60.0/dist/phaser.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@900&display=swap" rel="stylesheet">
</head>
<body>

    <a href="/" class="home-button">TRANG CHỦ</a>

    <div id="game-container"></div>
    
    <script src="{{ asset('games/co_tuong/game.js') }}?t=time()"></script>
</body>
</html>