// chạy với: python -m http.server 80

const isMobile = window.innerWidth < 768;
const CELL_SIZE = isMobile ? 35 : 40; 
const BOARD_OFFSET_X = isMobile ? 0 : 225; // Lề trái để bảng tên
const BOARD_OFFSET_Y = isMobile ? 200: 0;
const PADDING = isMobile ? 35 : 40;         // Khoảng cách từ mép bàn cờ đến quân cờ đầu tiên

// Tính toán chính xác độ rộng bàn cờ
const BOARD_REAL_WIDTH = CELL_SIZE * 8 + PADDING * 2;
const BOARD_REAL_HEIGHT = CELL_SIZE * 9 + PADDING * 2;

// Trong Config, width sẽ là: 180 (trái) + Bàn cờ + 180 (phải)
// width: BOARD_REAL_WIDTH + 360

const BOARD_WIDTH = PADDING * 2 + CELL_SIZE * 8;
const BOARD_HEIGHT = PADDING * 2 + CELL_SIZE * 9;

let turn = 'R';
let playerSide = localStorage.getItem('playerSide') || 'R';
let gameHistory = []; // Lưu các đối tượng {board, turn, move}

// Tính toán kích thước logic của game
const isMobileDevice = window.innerWidth < window.innerHeight; // Kiểm tra nhanh mobile
const GAME_WIDTH = isMobileDevice ? BOARD_WIDTH : (BOARD_WIDTH + 450);
// Trên mobile, tăng height để có chỗ cho 2 bảng tên bên trên
const GAME_HEIGHT = isMobileDevice ? (BOARD_HEIGHT + 200) : BOARD_HEIGHT; 

const config = {
    type: Phaser.AUTO,
    render: {
        antialias: true,                // QUAN TRỌNG: Bật chống răng cưa để thu nhỏ ảnh mịn
        antialiasGL: true,              // Chống răng cưa riêng cho WebGL
        mipmapFilter: 'LINEAR_MIPMAP_LINEAR', // Giúp ảnh khi thu nhỏ (downscale) không bị nhiễu vằn
        roundPixels: false,             // ĐỂ FALSE: Cho phép quân cờ nằm ở tọa độ lẻ giúp mượt hơn khi di chuyển
        pixelArt: false                 // QUAN TRỌNG: Phải để false vì chúng ta dùng ảnh HD, không phải pixel art
    },
    resolution: window.devicePixelRatio || 1,
    scale: {
        mode: Phaser.Scale.FIT,
        // TRÊN MOBILE: Chỉ căn giữa ngang (CENTER_HORIZONTALLY) 
        // để game dính vào mép trên màn hình.
        autoCenter: isMobileDevice ? Phaser.Scale.CENTER_HORIZONTALLY : Phaser.Scale.CENTER_BOTH,
        width: GAME_WIDTH,
        height: GAME_HEIGHT
    },
    parent: 'game-container',
    dom: {
        createContainer: true
    },
    backgroundColor: '#2c3e50',
    scene: {
        preload: preload,
        create: create
    }
};

const game = new Phaser.Game(config);

// Mảng định nghĩa vị trí ban đầu của 32 quân cờ
// Cấu trúc: { tên_file_svg, cột, hàng }
const initialSetup = [
    // Bên Đen (Hàng 0-3)
    { key: 'B_Xe', col: 0, row: 0 }, { key: 'B_Ma', col: 1, row: 0 }, { key: 'B_Tuong', col: 2, row: 0 },
    { key: 'B_Si', col: 3, row: 0 }, { key: 'B_Tuong_G', col: 4, row: 0 }, { key: 'B_Si', col: 5, row: 0 },
    { key: 'B_Tuong', col: 6, row: 0 }, { key: 'B_Ma', col: 7, row: 0 }, { key: 'B_Xe', col: 8, row: 0 },
    { key: 'B_Phao', col: 1, row: 2 }, { key: 'B_Phao', col: 7, row: 2 },
    { key: 'B_Tot', col: 0, row: 3 }, { key: 'B_Tot', col: 2, row: 3 }, { key: 'B_Tot', col: 4, row: 3 },
    { key: 'B_Tot', col: 6, row: 3 }, { key: 'B_Tot', col: 8, row: 3 },

    // Bên Đỏ (Hàng 6-9)
    { key: 'R_Xe', col: 0, row: 9 }, { key: 'R_Ma', col: 1, row: 9 }, { key: 'R_Tuong', col: 2, row: 9 },
    { key: 'R_Si', col: 3, row: 9 }, { key: 'R_Tuong_G', col: 4, row: 9 }, { key: 'R_Si', col: 5, row: 9 },
    { key: 'R_Tuong', col: 6, row: 9 }, { key: 'R_Ma', col: 7, row: 9 }, { key: 'R_Xe', col: 8, row: 9 },
    { key: 'R_Phao', col: 1, row: 7 }, { key: 'R_Phao', col: 7, row: 7 },
    { key: 'R_Tot', col: 0, row: 6 }, { key: 'R_Tot', col: 2, row: 6 }, { key: 'R_Tot', col: 4, row: 6 },
    { key: 'R_Tot', col: 6, row: 6 }, { key: 'R_Tot', col: 8, row: 6 }
];

function preload() {
    // Tải bàn cờ
    this.load.image('board', 'assets/board.png');
    this.load.svg('focus', 'assets/focus_8lines.svg');
    this.load.image('chieu_effect', 'assets/chieu_effect.png');

    // Tải toàn bộ các quân cờ cần thiết
    const pieceKeys = [
        'R_Xe', 'R_Ma', 'R_Tuong', 'R_Si', 'R_Tuong_G', 'R_Phao', 'R_Tot',
        'B_Xe', 'B_Ma', 'B_Tuong', 'B_Si', 'B_Tuong_G', 'B_Phao', 'B_Tot'
    ];
    pieceKeys.forEach(key => {
        this.load.image(key, `assets/HD+/${key}.png`);
    });
}

// Các biến toàn cục để quản lý trạng thái
let selectedPiece = null; 
let focusOld = null;      
let focusNew = null;      
let allPieces;        

function checkUserInfo(scene, callback) {
    const savedName = localStorage.getItem('user_name');
    const savedId = localStorage.getItem('user_id');

    if (savedName && savedId) {
        callback({ user_id: savedId, user_name: savedName.slice(0, 10) });
        return;
    }

    const centerX = scene.cameras.main.width / 2;
    const centerY = scene.cameras.main.height / 2;
    const container = scene.add.container(0, 0).setDepth(5000);

    const bg = scene.add.graphics();
    bg.fillStyle(0x000000, 0.8).fillRect(0, 0, scene.cameras.main.width, scene.cameras.main.height);
    container.add(bg);

    const panel = scene.add.graphics();
    panel.fillStyle(0x333333, 1).lineStyle(4, 0xffffff, 1);
    panel.fillRoundedRect(centerX - 200, centerY - 100, 400, 220, 15);
    panel.strokeRoundedRect(centerX - 200, centerY - 100, 400, 220, 15);
    container.add(panel);

    const title = scene.add.text(centerX, centerY - 60, "NHẬP TÊN KỲ THỦ", {
        fontSize: '24px', fontFamily: 'Arial', color: '#fbff00', fontStyle: 'bold'
    }).setOrigin(0.5);
    container.add(title); // Sửa lỗi ở đây: dùng container.add thay vì setParentContainer

    const nameDisplay = scene.add.text(centerX, centerY + 10, "Bấm để nhập...", {
        fontSize: '30px', fontFamily: 'Arial', color: '#ffffff',
        backgroundColor: '#000000', padding: { x: 10, y: 5 }, fixedWidth: 320, align: 'center'
    }).setOrigin(0.5);
    container.add(nameDisplay);

    // TẠO INPUT ẨN ĐỂ GÕ TIẾNG VIỆT
    const hiddenInput = document.createElement('input');
    hiddenInput.type = 'text';
    hiddenInput.maxLength = 10; // Giới hạn 10 ký tự
    hiddenInput.style = "position:absolute; opacity:0; pointer-events:none;";
    document.body.appendChild(hiddenInput);

    nameDisplay.setInteractive({ useHandCursor: true }).on('pointerdown', () => hiddenInput.focus());
    hiddenInput.focus();

    // Cập nhật text liên tục (Hỗ trợ gõ dấu tiếng Việt)
    const updateTimer = scene.time.addEvent({
        delay: 100,
        callback: () => {
            let currentVal = hiddenInput.value;
            nameDisplay.setText(currentVal + (scene.time.now % 1000 < 500 ? "|" : " "));
        },
        loop: true
    });

    const btn = scene.add.text(centerX, centerY + 100, "VÀO TRẬN", {
        fontSize: '22px', backgroundColor: '#aa0000', color: '#fff', padding: { x: 20, y: 10 }
    }).setOrigin(0.5).setInteractive({ useHandCursor: true });
    container.add(btn);

    btn.on('pointerdown', () => {
        const finalName = hiddenInput.value.trim().slice(0, 20);
        if (finalName.length >= 2) {
            localStorage.setItem('user_name', finalName);
            localStorage.setItem('user_id', 'ID_' + Date.now());
            
            // Dọn dẹp
            updateTimer.remove();
            if (document.body.contains(hiddenInput)) document.body.removeChild(hiddenInput);
            container.destroy();
            
            callback({ user_id: 'ID_' + Date.now(), user_name: finalName });
        } else {
            scene.cameras.main.shake(100, 0.01);
        }
    });
}

function showProfile(scene, name, side, isPlayer) {
    const screenWidth = scene.cameras.main.width;
    
    // 1. Cấu hình mặc định cho PC
    let scale = 1;
    let y = 30;
    let x = isPlayer ? 15 : (screenWidth - 195);

    // 2. Điều chỉnh riêng cho MOBILE
    if (typeof isMobile !== 'undefined' && isMobile) {
        scale = 0.8; // Nhỏ lại một chút nữa để tránh đụng nhau
        y = 80;       // Đẩy xuống thấp hơn (khoảng 80px) để né nút Trang Chủ
        
        const areaWidth = screenWidth / 2;
        const boxWidth = 180 * scale;
        
        // Thêm một khoảng đệm nhỏ (gap) ở giữa màn hình
        const centerGap = 10; 

        if (isPlayer) {
            // Player: Căn lề phải của nửa màn hình trái, trừ đi khoảng gap
            x = areaWidth - boxWidth - centerGap;
        } else {
            // AI: Căn lề trái của nửa màn hình phải, cộng thêm khoảng gap
            x = areaWidth + centerGap;
        }
    }

    const container = scene.add.container(x, y).setDepth(2000).setScale(scale);

    // --- Giữ nguyên phần vẽ đồ họa của bạn ---
    const bg = scene.add.graphics();
    container.add(bg);
    const frameColor = (side === 'R') ? 0xff0000 : 0xffffff;
    bg.fillStyle(0x1a1a1a, 0.9); 
    bg.lineStyle(4, frameColor, 1); 
    bg.fillRoundedRect(0, 0, 180, 110, 15);
    bg.strokeRoundedRect(0, 0, 180, 110, 15);

    const icon = scene.add.text(40, 40, isPlayer ? "👤" : "🤖", { fontSize: '40px' }).setOrigin(0.5);
    const nameTxt = scene.add.text(75, 40, name.toUpperCase(), {
        fontSize: '14px', 
        fontFamily: 'Arial', 
        fontStyle: 'bold', 
        color: '#ffffff'
    }).setOrigin(0, 0.5);
    container.add([icon, nameTxt]);

    const timerText = scene.add.text(90, 85, "00:00", {
        fontSize: '28px', 
        fontFamily: 'Courier', 
        fontStyle: 'bold', 
        color: (side === 'R') ? '#ff0000' : '#ffffff'
    }).setOrigin(0.5);
    container.add(timerText);

    if (isPlayer) {
        scene.playerProfile = container;
        scene.playerTimerText = timerText;
    } else {
        scene.aiProfile = container;
        scene.aiTimerText = timerText;
    }
}

let currentTurnTimer = null;
let timeLeft = 600;
let aiThinkTime = 1000;
let playerTimeTotal = 900;
let aiTimeTotal = 900;

function startTurnTimer(scene, isPlayerTurn) {
    // 1. Lấy phe người chơi từ biến toàn cục hoặc storage
    const pSide = localStorage.getItem('playerSide') || 'R';
    
    // 2. Xác định màu của quân đang đến lượt (R hoặc B)
    // Nếu là lượt người chơi thì quân đó có màu = pSide
    // Nếu là lượt máy thì quân đó có màu = đối lập với pSide
    const currentTurnColor = isPlayerTurn ? pSide : (pSide === 'R' ? 'B' : 'R');

    // 3. Ẩn tất cả Timer để reset
    if (scene.playerTimerText) scene.playerTimerText.setVisible(false);
    if (scene.aiTimerText) scene.aiTimerText.setVisible(false);

    // 4. CHỐN HIỂN THỊ: isPlayerTurn luôn trỏ về khung của Người chơi, 
    // không quan tâm người chơi đang cầm quân màu gì.
    const activeTimer = isPlayerTurn ? scene.playerTimerText : scene.aiTimerText;
    if (!activeTimer) return;

    // 5. Dừng bộ đếm cũ
    if (currentTurnTimer) currentTurnTimer.remove();

    // 6. Lấy quỹ thời gian tích lũy
    let currentTimeLeft = isPlayerTurn ? playerTimeTotal : aiTimeTotal;

    // 7. CẬP NHẬT GIAO DIỆN
    activeTimer.setVisible(true);
    activeTimer.setText(formatTime(currentTimeLeft));
    
    // 8. ĐẶT MÀU CHỮ THEO MÀU QUÂN CỜ ĐANG ĐI
    // Nếu quân đang đi là Đỏ (R) -> Chữ đỏ. Nếu là Đen (B) -> Chữ trắng.
    const themeColor = (currentTurnColor === 'R') ? '#ff0000' : '#ffffff';
    activeTimer.setColor(themeColor);

    // 9. Khởi tạo vòng lặp đếm ngược
    currentTurnTimer = scene.time.addEvent({
        delay: 1000,
        callback: () => {
            if (isPlayerTurn) {
                playerTimeTotal--;
                currentTimeLeft = playerTimeTotal;
            } else {
                aiTimeTotal--;
                currentTimeLeft = aiTimeTotal;
            }

            if (currentTimeLeft < 0) currentTimeLeft = 0;
            activeTimer.setText(formatTime(currentTimeLeft));

            // Cảnh báo hết giờ
            if (currentTimeLeft < 30) {
                activeTimer.setColor('#ff0000'); // Luôn chuyển đỏ khi nguy cấp
            } else {
                activeTimer.setColor(themeColor); // Quay lại màu phe (Trắng hoặc Đỏ)
            }

            if (currentTimeLeft <= 0) {
                currentTurnTimer.remove();
                handleTimeout(scene, isPlayerTurn);
            }
        },
        callbackScope: scene,
        loop: true
    });
}

function formatTime(seconds) {
    // Tính số phút
    const minutes = Math.floor(seconds / 60);
    // Tính số giây dư ra
    const partInSeconds = seconds % 60;
    
    // padStart(2, '0') giúp thêm số 0 ở trước nếu chỉ có 1 chữ số (ví dụ: "5" thành "05")
    const minuteString = minutes.toString().padStart(2, '0');
    const secondString = partInSeconds.toString().padStart(2, '0');
    
    return `${minuteString}:${secondString}`;
}

function getDisplayRow(logicalRow) {
    const pSide = localStorage.getItem('playerSide') || 'R';
    // Nếu người chơi phe Đen (B), đảo ngược hàng: 0->9, 1->8, ..., 9->0
    return (pSide === 'B') ? (9 - logicalRow) : logicalRow;
}

function create() {
    playerSide = localStorage.getItem('playerSide') || 'R';
    turn = 'R';
    aiTimeTotal = 900;
    playerTimeTotal = 900;
    // Cấu hình các hằng số tọa độ (Bạn có thể đưa ra ngoài hàm create nếu muốn dùng chung)
    const BOARD_REAL_WIDTH = CELL_SIZE * 8 + PADDING * 2;
    const BOARD_REAL_HEIGHT = CELL_SIZE * 9 + PADDING * 2;

    // 1. Vẽ ảnh bàn cờ
    const boardCenterX = BOARD_OFFSET_X + (BOARD_REAL_WIDTH / 2);
    
    // Y của tâm bàn cờ đơn giản là chiều cao bàn cờ chia đôi (cộng thêm PADDING nếu cần)
    const boardCenterY = BOARD_OFFSET_Y + BOARD_REAL_HEIGHT / 2; 

    const boardImg = this.add.image(boardCenterX, boardCenterY, 'board')
                         .setDisplaySize(BOARD_REAL_WIDTH, BOARD_REAL_HEIGHT)
                         .setInteractive();

    // 3. Khởi tạo Focus
    focusOld = this.add.image(0, 0, 'focus').setVisible(false).setDepth(10).setDisplaySize(CELL_SIZE, CELL_SIZE);
    focusNew = this.add.image(0, 0, 'focus').setVisible(false).setDepth(10).setDisplaySize(CELL_SIZE, CELL_SIZE);

    allPieces = this.add.group();

    const savedData = localStorage.getItem('xiangqi_save_game');
    let piecesToSetup = initialSetup; // Mặc định là bàn mới

    if (savedData) {
        const gameState = JSON.parse(savedData);
        // Khôi phục các biến toàn cục
        turn = gameState.turn;
        playerTimeTotal = gameState.playerTime;
        aiTimeTotal = gameState.aiTime;
        piecesToSetup = gameState.pieces; // Dùng danh sách quân từ file lưu
    } else {
        // Nếu không có file lưu, khởi tạo mặc định
        turn = 'R';
        playerTimeTotal = 900;
        aiTimeTotal = 900;
    }

    // 4. Vẽ quân cờ (Ở đây biến 'p' mới có hiệu lực)
    piecesToSetup.forEach(p => {
        const displayRow = getDisplayRow(p.row);
    
        const x = BOARD_OFFSET_X + PADDING + (p.col * CELL_SIZE);
        const y = BOARD_OFFSET_Y + PADDING + (displayRow * CELL_SIZE);
        
        let piece = this.add.image(x, y, p.key).setDisplaySize(CELL_SIZE * 0.9, CELL_SIZE * 0.9);
        piece.setInteractive().setDepth(5);
        // Giúp GPU xử lý texture lớn hiệu quả hơn khi thu nhỏ
        piece.side = p.key.startsWith('R') ? 'R' : 'B'; 
        piece.pieceData = { col: p.col, row: p.row }; // Vẫn giữ row logic để tính toán luật đi
        allPieces.add(piece);

        piece.on('pointerdown', (pointer, localX, localY, event) => {
            event.stopPropagation();
            if (selectedPiece && piece.side !== turn) {
                const moveResult = isValidMove(selectedPiece, piece.pieceData.col, piece.pieceData.row);
                if (moveResult.valid) {
                    executeMove(this, selectedPiece, piece.pieceData.col, piece.pieceData.row);
                } else {
                    invalidMoveEffect(this, selectedPiece, moveResult.errorType);
                }
            } else if (piece.side === turn) {
                selectedPiece = piece;
                focusNew.setPosition(piece.x, piece.y).setVisible(true);
            }
        });
    });

    // 5. Sự kiện Click bàn cờ (Tính toán dựa trên BOARD_OFFSET_X)
    // Tìm đến đoạn boardImg.on('pointerdown', ...) trong hàm create()
    boardImg.on('pointerdown', (pointer) => {
        if (selectedPiece) {
            const col = Math.round((pointer.x - BOARD_OFFSET_X - PADDING) / CELL_SIZE);
            const displayRow = Math.round((pointer.y - BOARD_OFFSET_Y - PADDING) / CELL_SIZE);
            
            // Chuyển từ hàng hiển thị về hàng logic để check luật
            const pSide = localStorage.getItem('playerSide') || 'R';
            const logicalRow = (pSide === 'B') ? (9 - displayRow) : displayRow;
            
            if (col >= 0 && col <= 8 && logicalRow >= 0 && logicalRow <= 9) {
                const result = isValidMove(selectedPiece, col, logicalRow);
                if (result.valid) {
                    executeMove(this, selectedPiece, col, logicalRow);
                } else {
                    invalidMoveEffect(this, selectedPiece, result.errorType);
                }
            }
        }
    });

    // 2. Hiện Profile (Sẽ bám theo lề trái 20px và lề phải sát biên)
    // Tìm đoạn này trong hàm create() của bạn và thay thế:
    checkUserInfo(this, (user) => {
        const opponentSide = (playerSide === 'R') ? 'B' : 'R';

        showProfile(this, user.user_name, playerSide, true);
        showProfile(this, "MÁY PRO", opponentSide, false);

        // FIX TẠI ĐÂY:
        if (playerSide === 'R' && turn === 'R') {
            // Nếu người chơi là Đỏ -> Chạy Timer Người chơi
            startTurnTimer(this, true); 
        } else if (playerSide === 'B' && turn === 'B') {
            startTurnTimer(this, true);
        } else {
            // Nếu Máy là Đỏ -> Chạy Timer Máy
            // Phải truyền false vì lượt này KHÔNG PHẢI của người chơi
            startTurnTimer(this, false); 
            
            // Gọi AI đi nước đầu tiên
            this.time.delayedCall(800, () => {
                startAIOrder(this);
            });
        }
    });
}

function executeMove(scene, piece, col, row) {
    const moveString = `${piece.pieceData.col},${piece.pieceData.row} to ${col},${row}`;
    
    // Lưu lại trạng thái TRƯỚC khi đổi lượt
    gameHistory.push({
        board: serializeBoard(), // Hàm biến bàn cờ thành chuỗi (xem bên dưới)
        turn: turn,
        move: moveString
    });

    // 1. Tìm xem tại ô đích có quân địch không
    const targetPiece = getPieceAt(col, row);

    const targetDisplayRow = getDisplayRow(row); // Chuyển row logic thành row hiển thị

    // 2. Cập nhật tọa độ logic cho quân đang đi
    piece.pieceData.col = col;
    piece.pieceData.row = row;

    const newX = BOARD_OFFSET_X + PADDING + col * CELL_SIZE;
    //const newY = PADDING + row * CELL_SIZE;
    const newY = BOARD_OFFSET_Y + PADDING + (targetDisplayRow * CELL_SIZE);

    // 3. Thực hiện di chuyển
    scene.tweens.add({
        targets: piece,
        x: newX,
        y: newY,
        duration: 200,
        onStart: () => {
            // Nếu có quân địch, thực hiện hiệu ứng bị ăn
            if (targetPiece) {
                scene.tweens.add({
                    targets: targetPiece,
                    alpha: 0,
                    scale: 0.5,
                    duration: 150,
                    onComplete: () => {
                        targetPiece.active = false; // Đánh dấu là đã chết để getPieceAt không tìm thấy nữa
                        targetPiece.destroy();      // Xóa hoàn toàn khỏi bộ nhớ
                    }
                });
            }
        },
        onComplete: () => {
            focusNew.setPosition(newX, newY).setVisible(true);

            // 1. Xác định phe đối thủ (phe chuẩn bị đến lượt đi)
            const nextTurn = (piece.side === 'R') ? 'B' : 'R';

            if (isCheckmate(scene, nextTurn)) {
                // 1. Xác định ai là người vừa thực hiện nước đi khiến đối phương hết cờ
                const winnerSide = piece.side; 
                
                // 2. Lấy phe của người chơi từ localStorage
                const pSide = localStorage.getItem('playerSide') || 'R';

                // 3. So sánh: Nếu phe thắng trùng với phe người chơi -> 'win'
                // Nếu không trùng (tức là Máy thắng) -> 'lose'
                const finalResult = (winnerSide === pSide) ? 'win' : 'lose';

                // 4. Kiểm tra trạng thái để in thông báo phụ (Tùy chọn)
                const isCheck = isKingInDanger(nextTurn);

                // 5. Hiển thị thông báo kết thúc
                showGameOver(scene, finalResult);
                
                // Dừng timer ngay lập tức
                if (currentTurnTimer) currentTurnTimer.remove();
                
                return; // Kết thúc, không đổi lượt nữa
            }

            // Kiểm tra chiếu tướng
            if (isOpponentKingUnderCheck(scene, piece.side)) {
                showCheckEffect(scene);
            }

            // Đổi lượt

            turn = (turn === 'R') ? 'B' : 'R';

            saveGameState();
            // Trong executeMove sau khi kết thúc lượt của Người chơi
            if (turn !== playerSide) {
                // Gọi AI bắt đầu suy nghĩ
                startAIOrder(scene);
            }
            // 3. XÁC ĐỊNH: Lượt mới có phải của người chơi không?
            const isNowPlayerTurn = (turn === playerSide);

            // 4. CHUYỂN TIMER QUA ĐỐI PHƯƠNG
            startTurnTimer(scene, isNowPlayerTurn);
            selectedPiece = null;
        }
    });
}

function isCheckmate(scene, sideToBottom) {
    // 1. Lấy tất cả quân cờ còn sống của phe đang đến lượt (sideToBottom)
    const myPieces = allPieces.getChildren().filter(p => p.active && p.side === sideToBottom);

    // 2. Duyệt qua từng quân cờ
    for (let piece of myPieces) {
        // 3. Thử giả lập di chuyển quân cờ này đến mọi ô trên bàn cờ (9x10)
        for (let r = 0; r <= 9; r++) {
            for (let c = 0; c <= 8; c++) {
                // Kiểm tra xem nước đi này có hợp lệ không
                const result = isValidMove(piece, c, r);
                
                if (result.valid) {
                    // Chỉ cần tìm thấy DUY NHẤT 1 nước đi hợp lệ -> Chưa thua
                    return false; 
                }
            }
        }
    }
    // Không tìm thấy nước đi nào cứu vãn được -> Chiếu bí!
    return true;
}

function checkBasicMove(piece, newCol, newRow) {
    const oldCol = piece.pieceData.col;
    const oldRow = piece.pieceData.row;
    const dCol = Math.abs(newCol - oldCol);
    const dRow = Math.abs(newRow - oldRow);
    const target = getPieceAt(newCol, newRow);

    // 1. KHÔNG ĂN QUÂN MÌNH
    if (target && target.side === piece.side) return false;

    const key = piece.texture.key;

    // 2. LUẬT PHÁO
    if (key.includes('Phao')) {
        if (oldCol !== newCol && oldRow !== newRow) return false;
        const count = countPiecesBetween(oldCol, oldRow, newCol, newRow);
        if (!target) return count === 0; // Đi trống: không vật cản
        return count === 1; // Ăn quân: phải có 1 ngòi
    }

    // 3. LUẬT TỐT
    if (key.includes('Tot')) {
        const isRed = (piece.side === 'R');
        // Không đi lùi
        if (isRed && newRow > oldRow) return false;
        if (!isRed && newRow < oldRow) return false;

        const hasCrossedRiver = isRed ? (oldRow <= 4) : (oldRow >= 5);
        if (!hasCrossedRiver) {
            return dRow === 1 && dCol === 0; // Chưa qua sông: chỉ đi thẳng
        } else {
            return (dRow === 1 && dCol === 0) || (dRow === 0 && dCol === 1); // Qua sông: thẳng hoặc ngang
        }
    }

    // 4. LUẬT XE
    if (key.includes('Xe')) {
        if (oldCol !== newCol && oldRow !== newRow) return false;
        return countPiecesBetween(oldCol, oldRow, newCol, newRow) === 0;
    }

    // 5. LUẬT MÃ
    if (key.includes('Ma')) {
        if (!((dCol === 1 && dRow === 2) || (dCol === 2 && dRow === 1))) return false;
        let bCol = oldCol, bRow = oldRow;
        if (dRow === 2) bRow = (oldRow + newRow) / 2;
        else bCol = (oldCol + newCol) / 2;
        return !getPieceAt(bCol, bRow); // Kiểm tra chân mã
    }

    // 6. LUẬT TƯỢNG
    if (key.includes('Tuong') && !key.includes('Tuong_G')) {
        if (dCol !== 2 || dRow !== 2) return false;
        const overRiver = piece.side === 'R' ? newRow < 5 : newRow > 4;
        if (overRiver) return false;
        return !getPieceAt((oldCol + newCol) / 2, (oldRow + newRow) / 2); // Mắt tượng
    }

    // 7. LUẬT SĨ
    if (key.includes('Si')) {
        if (dCol !== 1 || dRow !== 1) return false;
        const inPalace = newCol >= 3 && newCol <= 5 && (piece.side === 'R' ? newRow >= 7 : newRow <= 2);
        return inPalace;
    }

    // 8. LUẬT TƯỚNG
    if (key.includes('Tuong_G')) {
        if (dCol + dRow !== 1) return false;
        const inPalace = newCol >= 3 && newCol <= 5 && (piece.side === 'R' ? newRow >= 7 : newRow <= 2);
        return inPalace;
    }

    return false;
}

function isValidMove(piece, newCol, newRow) {
    // 1. Check luật đi cơ bản
    if (!checkBasicMove(piece, newCol, newRow)) {
        return { valid: false, errorType: 'normal' };
    }

    // 2. Giả lập nước đi
    const oldCol = piece.pieceData.col;
    const oldRow = piece.pieceData.row;
    const targetPiece = getPieceAt(newCol, newRow);

    piece.pieceData.col = newCol;
    piece.pieceData.row = newRow;
    if (targetPiece) targetPiece.active = false;

    // 3. KIỂM TRA LỖI RIÊNG BIỆT
    let error = null;
    
    if (isKingFaceToFace()) {
        error = 'face'; // Lỗi lộ mặt tướng
    } else if (isKingInDanger(piece.side)) {
        error = 'check'; // Lỗi đang bị chiếu hoặc đi vào thế bí
    }

    // 4. Hoàn tác giả lập
    piece.pieceData.col = oldCol;
    piece.pieceData.row = oldRow;
    if (targetPiece) targetPiece.active = true;

    // 5. Trả về kết quả
    if (error) {
        return { valid: false, errorType: error };
    }
    return { valid: true };
}

function invalidMoveEffect(scene, piece, type) {
    // 1. Rung quân cờ báo hiệu lỗi tại chỗ
    scene.tweens.add({
        targets: piece,
        x: piece.x + 8,
        duration: 50,
        yoyo: true,
        repeat: 3,
        onComplete: () => {
            piece.x = BOARD_OFFSET_X + PADDING + piece.pieceData.col * CELL_SIZE;
        }
    });

    // 2. Xác định nội dung thông báo
    let mainMsg = "KHÔNG HỢP LỆ";
    let subMsg = "Vui lòng đi đúng luật";

    switch(type) {
        case 'check':
            mainMsg = "CỨU TƯỚNG!";
            subMsg = "Tướng đang bị chiếu tướng";
            break;
        case 'face':
            mainMsg = "LỘ MẶT TƯỚNG!";
            subMsg = "Hai tướng không được nhìn nhau";
            break;
        case 'limit':
            mainMsg = "PHẠM LUẬT!";
            subMsg = "Không được chiếu quá 10 lần";
            break;
    }

    const centerX = scene.cameras.main.width / 2;
    const centerY = scene.cameras.main.height / 2;

    // 3. Tạo Container để nhóm các thành phần vẽ
    const container = scene.add.container(centerX, centerY).setDepth(3000);

    // Vẽ nền (Background) cho thông báo
    const bg = scene.add.graphics();
    bg.fillStyle(0x000000, 0.8); // Màu đen mờ
    bg.fillRoundedRect(-280, -60, 560, 120, 15); // Vẽ hình chữ nhật bo góc
    bg.lineStyle(3, 0xffffff, 1); // Viền trắng
    bg.strokeRoundedRect(-280, -60, 560, 120, 15);
    container.add(bg);

    // Vẽ chữ chính (Dùng Arial để chữ "Ạ" đều tăm tắp)
    const mainText = scene.add.text(0, -15, mainMsg, {
        fontSize: '35px',
        fontFamily: 'Arial, sans-serif',
        fontStyle: 'bold',
        color: '#ff0000',
        stroke: '#ffffff',
        strokeThickness: 6,
        padding: { bottom: 15 } // Padding này cực kỳ quan trọng để chữ Ạ không bị lỗi
    }).setOrigin(0.5);

    // Vẽ chữ phụ
    const subText = scene.add.text(0, 35, subMsg, {
        fontSize: '20px',
        fontFamily: 'Arial',
        color: '#ffffff',
        fontStyle: 'bold'
    }).setOrigin(0.5);

    container.add([mainText, subText]);

    // 4. Hiệu ứng Tween xuất hiện (Pop-up)
    container.setScale(0.5).setAlpha(0);
    
    scene.tweens.add({
        targets: container,
        alpha: 1,
        scale: 1,
        duration: 300,
        ease: 'Back.easeOut',
        onComplete: () => {
            // Rung màn hình nhẹ khi thông báo đập vào mắt
            scene.cameras.main.shake(150, 0.005);

            // Đợi 1.2 giây rồi biến mất
            scene.time.delayedCall(1200, () => {
                scene.tweens.add({
                    targets: container,
                    alpha: 0,
                    y: centerY - 50, // Bay nhẹ lên trên khi biến mất
                    duration: 300,
                    onComplete: () => container.destroy()
                });
            });
        }
    });
}

function getPieceAt(col, row) {
    if (!allPieces) return null;
    const targetCol = parseInt(col);
    const targetRow = parseInt(row);
    
    return allPieces.getChildren().find(p => 
        p.active && 
        parseInt(p.pieceData.col) === targetCol && 
        parseInt(p.pieceData.row) === targetRow
    );
}

// Hàm đếm số quân cờ nằm giữa hai điểm (theo đường thẳng dọc hoặc ngang)
function countPiecesBetween(c1, r1, c2, r2) {
    let count = 0;
    if (c1 === c2) { // Đi dọc
        const min = Math.min(r1, r2);
        const max = Math.max(r1, r2);
        for (let r = min + 1; r < max; r++) {
            if (getPieceAt(c1, r)) count++;
        }
    } else if (r1 === r2) { // Đi ngang
        const min = Math.min(c1, c2);
        const max = Math.max(c1, c2);
        for (let c = min + 1; c < max; c++) {
            if (getPieceAt(c, r1)) count++;
        }
    }
    return count;
}

function isKingFaceToFace() {
    const redKing = allPieces.getChildren().find(p => p.active && p.texture.key === 'R_Tuong_G');
    const blackKing = allPieces.getChildren().find(p => p.active && p.texture.key === 'B_Tuong_G');

    if (!redKing || !blackKing) return false;

    // Hai tướng phải cùng cột
    if (redKing.pieceData.col === blackKing.pieceData.col) {
        const count = countPiecesBetween(
            redKing.pieceData.col, 
            redKing.pieceData.row, 
            blackKing.pieceData.col, 
            blackKing.pieceData.row
        );
        // Nếu không có quân cản ở giữa
        if (count === 0) return true;
    }
    return false;
}

function showCheckEffect(scene) {
    const centerX = scene.cameras.main.width / 2;
    const centerY = scene.cameras.main.height / 2;

    const effect = scene.add.image(centerX, centerY, 'chieu_effect');
    // Bắt đầu từ scale 0, alpha 0
    effect.setDepth(1000).setScale(0).setAlpha(0);

    scene.tweens.add({
        targets: effect,
        scale: 0.3, // GIẢM TẠI ĐÂY: 0.5 là bằng một nửa ảnh gốc
        alpha: 1,
        duration: 100,
        ease: 'Back.out',
        onComplete: () => {
            scene.cameras.main.shake(20, 0.005);

            scene.time.delayedCall(200, () => {
                scene.tweens.add({
                    targets: effect,
                    alpha: 0,
                    scale: 0.6, // Phóng to nhẹ khi biến mất cho đẹp
                    duration: 100,
                    onComplete: () => effect.destroy()
                });
            });
        }
    });
}
function isOpponentKingUnderCheck(scene, currentTurn) {
    const opponentSide = (currentTurn === 'R') ? 'B' : 'R';
    
    // 1. Tìm vị trí Tướng đối phương
    const opponentKing = allPieces.getChildren().find(p => 
        p.active && p.texture.key === (opponentSide + '_Tuong_G')
    );

    if (!opponentKing) return false;

    const kingCol = opponentKing.pieceData.col;
    const kingRow = opponentKing.pieceData.row;

    // 2. Kiểm tra xem có quân nào của phe vừa đi (currentTurn) có thể ăn được Tướng không
    const myPieces = allPieces.getChildren().filter(p => p.active && p.side === currentTurn);
    
    for (let p of myPieces) {
        // Sử dụng chính hàm isValidMove đã viết để kiểm tra tầm tấn công
        if (isValidMove(p, kingCol, kingRow).valid) {
            return true;
        }
    }
    return false;
}

function isKingInDanger(side) {
    // 1. Tìm vị trí Tướng của phe cần kiểm tra
    const king = allPieces.getChildren().find(p => 
        p.active && p.texture.key === (side + '_Tuong_G')
    );
    if (!king) return false;

    const kingCol = king.pieceData.col;
    const kingRow = king.pieceData.row;

    // 2. Tìm tất cả quân của đối phương
    const opponentSide = (side === 'R') ? 'B' : 'R';
    const opponents = allPieces.getChildren().filter(p => p.active && p.side === opponentSide);

    // 3. Nếu bất kỳ quân đối phương nào có thể "ăn" Tướng -> Tướng đang gặp nguy hiểm
    for (let p of opponents) {
        // Lưu ý: Sử dụng isValidMove nhưng phải tránh đệ quy vô tận
        if (checkBasicMove(p, kingCol, kingRow)) {
            return true;
        }
    }
    return false;
}

function showGameOver(scene, result) {
    localStorage.removeItem('xiangqi_save_game');
    sendFinalStats(result);

    // result: 'win' (Đỏ thắng), 'lose' (Đen thắng), hoặc 'draw' (Hòa/Cờ trắng)
    const centerX = scene.cameras.main.width / 2;
    const centerY = scene.cameras.main.height / 2;
    const container = scene.add.container(centerX, centerY).setDepth(3000);

    // 1. Tạo lớp phủ mờ toàn màn hình (Overlay)
    const overlay = scene.add.graphics();
    overlay.fillStyle(0x000000, 0.6);
    overlay.fillRect(-centerX, -centerY, scene.cameras.main.width, scene.cameras.main.height);
    container.add(overlay);

    // 2. Vẽ hào quang (Glow effect)
    const glow = scene.add.graphics();
    const glowColor = (result === 'win') ? 0xffd700 : (result === 'lose' ? 0xff0000 : 0xffffff);
    glow.fillStyle(glowColor, 0.3);
    glow.fillCircle(0, 0, 200);
    container.add(glow);

    // 3. Cấu hình nội dung
    let mainMsg = "";
    let subMsg = "";
    let mainColor = "#ffffff";

    if (result === 'win') {
        mainMsg = "BẠN THẮNG";
        subMsg = "DANH CHẤN THIÊN HẠ";
        mainColor = "#fff700"; // Vàng rực
    } else if (result === 'lose') {
        mainMsg = "BẠN THUA";
        subMsg = "BẠI BINH CHI TƯỚNG";
        mainColor = "#ff0000"; // Đỏ
    } else {
        mainMsg = "HÒA CỜ";
        subMsg = "KỲ PHÙNG ĐỊCH THỦ";
        mainColor = "#ffffff"; // Trắng
    }

    // 4. Vẽ chữ chính (Có đổ bóng rực rỡ)
    const mainText = scene.add.text(0, -20, mainMsg, {
        fontSize: isMobile ? '45px' : '80px',
        fontFamily: 'Montserrat',
        color: mainColor,
        stroke: '#000000',
        strokeThickness: 10,
        shadow: { color: glowColor, fill: true, offsetX: 0, offsetY: 0, blur: 20 }
    }).setOrigin(0.5);

    // 5. Vẽ chữ phụ
    const subText = scene.add.text(0, 60, subMsg, {
        fontSize: isMobile ? '25px' : '30px',
        fontFamily: 'Montserrat',
        color: '#ffffff',
        fontStyle: 'bold'
    }).setOrigin(0.5);

    container.add([mainText, subText]);

    // 6. Hiệu ứng Tween xuất hiện hoành tráng
    container.setScale(0).setAlpha(0);
    scene.tweens.add({
        targets: container,
        scale: 1,
        alpha: 1,
        duration: 800,
        ease: 'Back.easeOut',
        onComplete: () => {
            // Hiệu ứng lấp lánh nhẹ cho chữ chính
            scene.tweens.add({
                targets: mainText,
                scale: 1.1,
                duration: 1000,
                yoyo: true,
                repeat: -1
            });
        }
    });

    // 7. Thêm nút Chơi lại (Restart)
    const restartBtn = scene.add.text(0, 150, "CHƠI LẠI", {
        fontSize: '32px',
        backgroundColor: '#00aa00',
        padding: { x: 20, y: 10 },
        color: '#ffffff'
    }).setOrigin(0.5).setInteractive({ useHandCursor: true });

    // 1. Lấy phe hiện tại từ LocalStorage (mặc định là 'R' nếu chưa có)
    let currentSide = localStorage.getItem('playerSide') || 'R';
    
    // 2. Đảo phe
    let nextSide = (currentSide === 'R') ? 'B' : 'R';
    
    // 3. Lưu phe mới vào LocalStorage
    localStorage.setItem('playerSide', nextSide);
    restartBtn.on('pointerdown', () => {
        // 4. Khởi động lại game
        scene.scene.restart();
    });
    
    container.add(restartBtn);
}

function getAllValidMoves(scene, side) {
    let validMoves = [];
    
    // Lấy danh sách tất cả các quân cờ thuộc phe side (ví dụ: 'B')
    const pieces = scene.children.list.filter(obj => obj.side === side && obj.pieceData);

    pieces.forEach(piece => {
        // Duyệt qua toàn bộ 90 ô trên bàn cờ (10 hàng x 9 cột)
        for (let r = 0; r < 10; r++) {
            for (let c = 0; c < 9; c++) {
                // Sử dụng hàm isValidMove mà bạn đã có sẵn
                const moveResult = isValidMove(piece, c, r);
                
                if (moveResult.valid) {
                    validMoves.push({
                        piece: piece,
                        toCol: c,
                        toRow: r
                    });
                }
            }
        }
    });

    return validMoves;
}

function getValidMovesForPiece(scene, piece) {
    // Lấy tất cả nước đi hợp lệ của phe hiện tại (side của quân cờ đó)
    const allMoves = getAllValidMoves(scene, piece.side);
    
    // Lọc ra những nước đi mà đối tượng quân cờ trùng với quân cờ đang xét
    return allMoves.filter(move => move.piece === piece);
}

function saveGameState() {
    const piecesData = [];
    allPieces.getChildren().forEach(piece => {
        if (piece.active) {
            piecesData.push({
                key: piece.texture.key,
                col: piece.pieceData.col,
                row: piece.pieceData.row,
                side: piece.side
            });
        }
    });

    const gameState = {
        turn: turn, // Biến turn toàn cục (R hoặc B)
        pieces: piecesData,
        playerTime: playerTimeTotal,
        aiTime: aiTimeTotal,
    };

    localStorage.setItem('xiangqi_save_game', JSON.stringify(gameState));
}

function sendFinalStats(finalResult) {
    if (gameHistory.length === 0) return;

    // Lấy CSRF Token từ meta tag (Laravel yêu cầu cho POST)
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // Chuyển đổi kết quả sang định dạng Controller mong muốn (nếu cần)
    let resultMapping = finalResult;
    if (finalResult === 'win') resultMapping = (playerSide === 'R') ? 'red_win' : 'black_win';
    if (finalResult === 'lose') resultMapping = (playerSide === 'R') ? 'black_win' : 'red_win';

    fetch('/api/update-stats', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            history: gameHistory, // Đảm bảo mỗi item có turn: 'red' hoặc 'black'
            result: resultMapping
        })
    })
    .then(response => response.json())
    .then(data => {
        gameHistory = []; 
    })
    .catch(error => console.error("Error:", error));
}

function serializeBoard() {
    // Tạo lưới 10x9 trắng (.)
    let grid = Array.from({ length: 10 }, () => Array(9).fill("."));

    // Bảng ánh xạ khớp chính xác với Key của bạn
    const pieceMap = {
        'XE': 'R',
        'MA': 'N',
        'TUONG': 'B',   // Tượng (Voi)
        'SI': 'A',      // Sĩ
        'TUONG_G': 'K', // Tướng (Vua) - Xử lý trường hợp có hậu tố _G
        'PHAO': 'C',
        'TOT': 'P'
    };

    allPieces.getChildren().forEach(p => {
        if (p.active) {
            let key = p.texture.key.toUpperCase(); // Ví dụ: "R_TUONG_G"
            let parts = key.split('_'); // ["R", "TUONG", "G"] hoặc ["R", "XE"]
            
            let side = parts[0]; // "R" hoặc "B"
            
            // Lấy phần loại quân: nếu là TUONG_G thì nối lại, nếu không thì lấy parts[1]
            let type = (parts[1] === 'TUONG' && parts[2] === 'G') ? 'TUONG_G' : parts[1];

            let char = pieceMap[type] || '?';

            // Đỏ (R) viết HOA, Đen (B) viết thường
            let finalChar = (side === 'R') ? char.toUpperCase() : char.toLowerCase();
            
            grid[p.pieceData.row][p.pieceData.col] = finalChar;
        }
    });

    return grid.reverse().map(row => row.join('')).join('');
}

async function startAIOrder(scene) {
    const aiSide = (playerSide === 'R') ? 'B' : 'R';
    if (turn !== aiSide) return;

    const currentBoard = serializeBoard();
    let moveExecuted = false;

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const response = await fetch('/api/ai-move', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ board: currentBoard, turn: turn })
        });

        const data = await response.json();

        if (data.status === "success") {
            let chosen = null;
            // 1. Ưu tiên Book
            if (data.source === 'book' && data.move) {
                chosen = data.move;
            } else if (data.source === 'stats' && data.moves) {
                const goodMoves = data.moves.filter(m => m.score >= 0);
                if (goodMoves.length > 0) {
                    chosen = goodMoves[Math.floor(Math.random() * goodMoves.length)];
                } else {
                    // 3. Toàn nước xấu: Loại trừ khỏi danh sách allMoves
                    const pickMoves = pickBestMove(scene, aiSide);
                    console.log(pickMoves);

                    const badKeys = data.moves.map(m => `${m.fromCol},${m.fromRow} to ${m.toCol},${m.toRow}`);
                    const filtered = pickMoves.filter(m => {
                        const key = `${m.piece.pieceData.col},${m.piece.pieceData.row} to ${m.toCol},${m.toRow}`;
                        return !badKeys.includes(key);
                    });

                    if (filtered.length > 0) {
                        const pick = filtered[Math.floor(Math.random() * filtered.length)];
                        chosen = { fromCol: pick.piece.pieceData.col, fromRow: pick.piece.pieceData.row, toCol: pick.toCol, toRow: pick.toRow };
                    }
                }
            }

            if (chosen) {
                const piece = getPieceAt(chosen.fromCol, chosen.fromRow);
                if (piece) {
                    executeMove(scene, piece, chosen.toCol, chosen.toRow);
                    moveExecuted = true;
                }
            }
        }
    } catch (error) {
        console.error("AI Error:", error);
    }

    // 4. FALLBACK: Nếu nãy giờ chưa đi được nước nào (No data hoặc lỗi)
    if (!moveExecuted) {
        const fallbackMoves = getAllValidMoves(scene, aiSide);
        if (fallbackMoves.length > 0) {
            const pick = fallbackMoves[Math.floor(Math.random() * fallbackMoves.length)];
            executeMove(scene, pick.piece, pick.toCol, pick.toRow);
        }
    }
}

function pickBestMove(scene, side) {
    const allMoves = getAllValidMoves(scene, side);
    if (allMoves.length === 0) return [];

    const checkmateMoves = [];
    const captureMoves = [];
    const threateningMoves = [];
    const safeMoves = [];
    const otherMoves = [];

    allMoves.forEach(move => {
        const targetPiece = getPieceAt(move.toCol, move.toRow);
        const originalCol = move.piece.pieceData.col;
        const originalRow = move.piece.pieceData.row;
        
        // --- GIẢ LẬP ---
        move.piece.pieceData.col = move.toCol;
        move.piece.pieceData.row = move.toRow;
        if (targetPiece) targetPiece.active = false;

        const opponentSide = (side === 'R') ? 'B' : 'R';

        // 1. Check Chiếu bí
        if (isCheckmate(scene, opponentSide)) {
            checkmateMoves.push(move);
        } 
        // 2. Check Ăn quân hoặc Chiếu tướng
        else if ((targetPiece && targetPiece.side !== side) || isOpponentKingUnderCheck(scene, side)) {
            move.priorityValue = targetPiece ? (getPieceValue(targetPiece.type) + 100) : 50; 
            captureMoves.push(move);
        } 
        else {
            // 3. Check Hăm bắt
            const potentialMoves = getValidMovesForPiece(scene, move.piece);
            let maxThreatValue = 0;

            potentialMoves.forEach(pMove => {
                const threatTarget = getPieceAt(pMove.toCol, pMove.toRow);
                if (threatTarget && threatTarget.side !== side) {
                    maxThreatValue = Math.max(maxThreatValue, getPieceValue(threatTarget.type));
                }
            });

            if (maxThreatValue > 0) {
                move.priorityValue = maxThreatValue;
                threateningMoves.push(move);
            } 
            // 4. Check An toàn
            else if (isCellSafe(scene, move.toCol, move.toRow, side)) {
                safeMoves.push(move);
            } 
            // 5. Nước đi bình thường (rủi ro cao)
            else {
                otherMoves.push(move);
            }
        }

        // --- HOÀN TRẢ ---
        move.piece.pieceData.col = originalCol;
        move.piece.pieceData.row = originalRow;
        if (targetPiece) targetPiece.active = true;
    });

    // Sắp xếp các nhóm có điểm số
    captureMoves.sort((a, b) => b.priorityValue - a.priorityValue);
    threateningMoves.sort((a, b) => b.priorityValue - a.priorityValue);

    // Gộp tất cả nước đi "có ích" vào một mảng theo đúng thứ tự ưu tiên
    // Chúng ta trả về mảng này để hàm startAIOrder lọc bỏ badKeys từ DB
    console.log(captureMoves);
    return [
        ...checkmateMoves,
        ...captureMoves,
        ...threateningMoves,
        // ...safeMoves,
    ];
}
// Hàm phụ trợ định nghĩa giá trị quân cờ
function getPieceValue(type) {
    const values = {
        'Tuong_G': 1000,
        'Xe': 100,
        'Phao': 50,
        'Ma': 45,
        'Tuong': 25,
        'Si': 20,
        'Tot': 10
    };
    return values[type] || 0;
}

function isCellSafe(scene, col, row, side) {
    const opponentSide = (side === 'R') ? 'B' : 'R';
    
    // Lấy tất cả nước đi hợp lệ của đối thủ
    const opponentMoves = getAllValidMoves(scene, opponentSide);
    
    // Kiểm tra xem có nước đi nào của đối thủ đích đến là ô (col, row) không
    const threat = opponentMoves.find(m => m.toCol === col && m.toRow === row);
    
    // Nếu tìm thấy threat (mối đe dọa) thì ô này KHÔNG an toàn
    return !threat;
}