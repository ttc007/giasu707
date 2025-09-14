let playerColor; // Lưu màu quân đã chọn
let red, green;
let turn = 'red';
let selectedPiece = null;
let history = [];
let direct = 'forward';
let result = null;
let computerColor = '';
let lastMoveToDraw = null;

const canvas = document.getElementById("chessboard");
const ctx = canvas.getContext("2d");

const cellSize = 39;
const boardCols = 9;
const boardRows = 10;
const boardWidth = (boardCols - 1) * cellSize;
const boardHeight = (boardRows - 1) * cellSize;
const margin = 50;

const offsetX = 22;
const offsetY = 33;

function drawGrid() {
  ctx.strokeStyle = "#000";
  ctx.lineWidth = 1;

  // Vẽ các đường ngang
  for (let i = 0; i < 10; i++) {
    const y = offsetY + i * cellSize;
    ctx.beginPath();
    ctx.moveTo(offsetX, y);
    ctx.lineTo(offsetX + boardWidth, y);
    ctx.stroke();
  }

  // Vẽ các đường dọc
  for (let i = 0; i < 9; i++) {
    const x = offsetX + i * cellSize;
    ctx.beginPath();

    if (i > 0 && i < 8) {
      ctx.moveTo(x, offsetY);
      const riverY = cellSize * 4;
      ctx.lineTo(x, offsetY + riverY);
      ctx.stroke();

      ctx.beginPath();
      const riverY1 = cellSize * 5;
      ctx.moveTo(x, riverY1 + offsetY);
      ctx.lineTo(x, offsetY + boardHeight);
      ctx.stroke();
    } else {
      ctx.moveTo(x, offsetY);
      ctx.lineTo(x, offsetY + boardHeight);
      ctx.stroke();
    }
  }

  // Vẽ khung tướng
  const palaceX1 = offsetX + 3 * cellSize;
  const palaceX2 = offsetX + 5 * cellSize;
  const palaceYTop = offsetY;
  const palaceYBottom = offsetY + 7 * cellSize;

  ctx.beginPath();
  ctx.moveTo(palaceX1, palaceYTop);
  ctx.lineTo(palaceX2, palaceYTop + 2 * cellSize);
  ctx.moveTo(palaceX2, palaceYTop);
  ctx.lineTo(palaceX1, palaceYTop + 2 * cellSize);
  ctx.stroke();

  ctx.beginPath();
  ctx.moveTo(palaceX1, palaceYTop + 7 * cellSize);
  ctx.lineTo(palaceX2, palaceYTop + 9 * cellSize);
  ctx.moveTo(palaceX2, palaceYTop + 7 * cellSize);
  ctx.lineTo(palaceX1, palaceYTop + 9 * cellSize);
  ctx.stroke();

  // Vẽ các vị trí đặt pháo
  const cannonPositions = [
    [1, 2],
    [7, 2],
    [1, 7],
    [7, 7],

    [0, 3],
    [2, 3],
    [4, 3],
    [6, 3],
    [8, 3],
    [0, 6],
    [2, 6],
    [4, 6],
    [6, 6],
    [8, 6],
  ];

  for (const [col, row] of cannonPositions) {
    drawCannonPosition(offsetX + col * cellSize, offsetY + row * cellSize);
  }
}

function drawCannonPosition(x, y) {
  ctx.lineWidth = 1;
  ctx.strokeStyle = "#000";
  
  ctx.beginPath();

  if (x > offsetX && x < 8 * cellSize + offsetX) {
    ctx.moveTo(x + 5, y + 5);
    ctx.lineTo(x + 5, y + 10);
    ctx.moveTo(x - 5, y + 5);
    ctx.lineTo(x - 5, y + 10);
    ctx.moveTo(x - 5, y - 5);
    ctx.lineTo(x - 5, y - 10);
    ctx.moveTo(x + 5, y - 5);
    ctx.lineTo(x + 5, y - 10);

    ctx.moveTo(x + 5, y - 5);
    ctx.lineTo(x + 10, y - 5);
    ctx.moveTo(x - 5, y - 5);
    ctx.lineTo(x - 10, y - 5);
    ctx.moveTo(x + 5, y + 5);
    ctx.lineTo(x + 10, y + 5);
    ctx.moveTo(x - 5, y + 5);
    ctx.lineTo(x - 10, y + 5);
    ctx.stroke();
  } else if (x == offsetX) {
    ctx.moveTo(x + 5, y + 5);
    ctx.lineTo(x + 5, y + 10);
    ctx.moveTo(x + 5, y - 5);
    ctx.lineTo(x + 5, y - 10);

    ctx.moveTo(x + 5, y - 5);
    ctx.lineTo(x + 10, y - 5);
    ctx.moveTo(x + 5, y + 5);
    ctx.lineTo(x + 10, y + 5);
    ctx.stroke();
  } else {
    ctx.moveTo(x - 5, y + 5);
    ctx.lineTo(x - 5, y + 10);
    ctx.moveTo(x - 5, y - 5);
    ctx.lineTo(x - 5, y - 10);

    ctx.moveTo(x - 5, y - 5);
    ctx.lineTo(x - 10, y - 5);
    ctx.moveTo(x - 5, y + 5);
    ctx.lineTo(x - 10, y + 5);
    ctx.stroke();
  }  
}

document.getElementById('rotateButton').addEventListener('click', function() {
  rotate();
});

function rotate() {
  // if (computerColor === turn) return;

  direct = direct === 'forward'? 'reverse' : 'forward';
  selectedPiece = null;
  drawBoard();
  displayPieces();
}

function newGame() {
  history = [];
  lastMoveToDraw = null;
  const openingIdStart = document.getElementById('openingSelect').value;
  loadOpening(openingIdStart);
  canvas.addEventListener('click', handleCanvasClick);
}


newGame();
// Chức năng hiển thị quân cờ
function displayPieces() {
  // Xác định màu sắc quân cờ
  const redColor = 'red';
  const greenColor = '#06580a';

  // Vẽ quân đỏ
  for (const piece in red) {
      red[piece].forEach(position => {
          drawPiece(piece, position.x, position.y, redColor);
      });
  }

  // Vẽ quân xanh
  for (const piece in green) {
      green[piece].forEach(position => {
          drawPiece(piece, position.x, position.y, greenColor);
      });
  }
}

// Hàm vẽ một quân cờ
function drawPiece(piece, x, y, color) {
    let pieceX, pieceY;
    if (direct == 'forward') {
       pieceX = offsetX + (x - 1) * cellSize ; // Tọa độ x
       pieceY = offsetY + (10 - y) * cellSize ; // Tọa độ y
    } else {
      pieceX = offsetX + (9 - x) * cellSize ; // Tọa độ x
      pieceY = offsetY + (y -1) * cellSize ; // Tọa độ y
    }
    
    // Vẽ viền bao quanh quân cờ
    ctx.fillStyle = '#ffffff'; // Màu nền quân cờ
    ctx.strokeStyle = color; // Màu viền
    ctx.lineWidth = 3;
    ctx.beginPath();
    ctx.arc(pieceX, pieceY, 16, 0, Math.PI * 2); // Hình tròn cho quân cờ
    ctx.stroke();
    ctx.fill();

    // Vẽ ký tự quân cờ
    ctx.fillStyle = color; // Màu ký tự
    ctx.font = '22px Arial';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.fillText(piece, pieceX, pieceY);
}

// Chức năng vẽ bàn cờ
function drawBoard() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    drawGrid();
    drawMoveArrow();
}

document.getElementById('newButton').addEventListener('click', function() {
    newGame();
});

function getClickXY(cX, cY) {
  let x, y, col, row;

  // Tính toán hàng và cột từ tọa độ click
  col = ((cX- offsetX) / cellSize) + 1; 
  row = (10 - (cY - offsetY) / cellSize); // Hàng từ 1 đến 10
  if (direct != 'forward') {
    col = 10 - col; 
    row = 11 - row; // Hàng từ 1 đến 10
  }

  for (i = 1; i <= 10; i ++) {
    if (col < i + 0.4 && col > i - 0.4 && col < 9.6) x = i;
    if (row < i + 0.4 && row > i - 0.4) y = i;
  }

  return {x:x, y:y}
}
// Hàm xử lý click trên canvas
function handleCanvasClick(event) {
    if(result) return;

    const rect = canvas.getBoundingClientRect();
    const x = event.clientX - rect.left; // Tọa độ x trên canvas
    const y = event.clientY - rect.top; // Tọa độ y trên canvas

    if (x < offsetX - 10 || x > offsetX + boardWidth + 10 || y < offsetY - 10 || y > offsetY + boardHeight + 10) return;

    let { x: col, y: row } = getClickXY(x, y);

    var isClickPiece = false;
    // Kiểm tra quân cờ có thuộc về người chơi không và vị trí click có nằm trong khuôn khổ không bằng vòng lặp
    var playerPieces = null;
    if (true) {
      playerPieces = turn == 'red' ? red : green; 
      for (const piece in playerPieces) {
        playerPieces[piece].forEach(position => {
          if(col > position.x - 0.5 && col < position.x + 0.5 && row > position.y - 0.5 && row < position.y + 0.5) {
            selectedPiece = {piece: piece, x: position.x, y: position.y , color: turn};
            drawSelectedPiece();
            isClickPiece = true;
          }
        });
      }
    }

    if (!isClickPiece && selectedPiece !== null) {
      // Kiểm tra xem vị trí click có hợp lệ để quân cờ di chuyển đến không
      var board = {green: green, red:red}
      var move = {
        piece: selectedPiece.piece,
        fromX: selectedPiece.x,
        fromY: selectedPiece.y, 
        toX: col, 
        toY : row, 
        color: selectedPiece.color
      }

      if (isValidMove(board, move) ){
        // Tìm và xóa quân cờ khỏi vị trí ban đầu trong mảng quân cờ của người chơi
        var simulatedBoard = JSON.parse(JSON.stringify(board));
        moving(board, move);
        lastMoveToDraw = {
          fromX: move.fromX,
          fromY: move.fromY,
          toX: move.toX,
          toY: move.toY,
          color: 'blue'
        };

        history.push({
          'color' : turn, 
          'piece' : selectedPiece.piece,
          'fromX' : selectedPiece.x,
          'fromY' : selectedPiece.y,
          'toX': col ,
          'toY': row ,
          'imageChess' : JSON.stringify(simulatedBoard)
        });

        // Cập nhật vị trí mới cho selectedPiece
        selectedPiece.x = col;
        selectedPiece.y = row;

        // Reset selectedPiece để hủy chọn quân cờ sau khi di chuyển
        selectedPiece = null;

        // Vẽ lại bàn cờ để cập nhật vị trí quân cờ mới
        drawBoard();
        displayPieces();

        // Chuyển lượt chơi
        turn = turn === 'red' ? 'green' : 'red';

        document.getElementById('result').innerText = '';
        document.getElementById('comment').value = '';

        if (isAttackKing(board, turn)) {
          ctx.font = "16px Arial";
          ctx.fillStyle = "red";
          ctx.textAlign = "center";
          ctx.fillText("Chiếu", 165,209);
        }
        
        // Máy tính thực hiện nước đi của mình
        if (computerColor === turn) {
          computerMove();
        };
      }
    }
}

// Hàm vẽ vòng tròn quanh quân cờ đã chọn
function drawSelectedPiece() {
    let piece = selectedPiece;
    drawBoard();
    displayPieces();
    
    if (selectedPiece) {
      ctx.beginPath();

      if (direct == 'forward') {
        ctx.arc(offsetX + (piece.x * cellSize) - (cellSize), offsetY + ((10 - piece.y) * cellSize), cellSize / 2 - 2, 0, Math.PI * 2);
      } else {
        ctx.arc(offsetX + ((10 - piece.x) * cellSize) - (cellSize), offsetY + ((piece.y - 1) * cellSize), cellSize / 2 - 2, 0, Math.PI * 2);
      }
      ctx.strokeStyle = 'yellow'; // Màu viền của vòng tròn
      ctx.lineWidth = 2;
      ctx.stroke();
      ctx.closePath();
    }
}

function drawCornerSquare(x, y) {
  const size = cellSize;    // kích thước 1 ô
  const pad = -1;            // khoảng cách viền ra ngoài (giảm lại)
  const len = 8;            // độ dài đoạn vẽ ở góc (giảm lại)

  const left = x - size / 2 - pad;
  const top = y - size / 2 - pad;
  const right = x + size / 2 + pad;
  const bottom = y + size / 2 + pad;

  ctx.strokeStyle = 'blue';  // luôn dùng màu xanh
  ctx.lineWidth = 2;

  ctx.beginPath();
  // Góc trên trái
  ctx.moveTo(left, top + len);
  ctx.lineTo(left, top);
  ctx.lineTo(left + len, top);

  // Góc trên phải
  ctx.moveTo(right - len, top);
  ctx.lineTo(right, top);
  ctx.lineTo(right, top + len);

  // Góc dưới trái
  ctx.moveTo(left, bottom - len);
  ctx.lineTo(left, bottom);
  ctx.lineTo(left + len, bottom);

  // Góc dưới phải
  ctx.moveTo(right - len, bottom);
  ctx.lineTo(right, bottom);
  ctx.lineTo(right, bottom - len);

  ctx.stroke();
  ctx.closePath();
}

function drawMoveArrow() {
  if (!lastMoveToDraw) return;

  const { fromX, fromY, toX, toY } = lastMoveToDraw;
  let fx, fy, tx, ty;

  if (direct === 'forward') {
    fx = offsetX + (fromX * cellSize) - cellSize;
    fy = offsetY + ((10 - fromY) * cellSize);

    tx = offsetX + (toX * cellSize) - cellSize;
    ty = offsetY + ((10 - toY) * cellSize);
  } else {
    fx = offsetX + ((10 - fromX) * cellSize) - cellSize;
    fy = offsetY + ((fromY - 1) * cellSize);

    tx = offsetX + ((10 - toX) * cellSize) - cellSize;
    ty = offsetY + ((toY - 1) * cellSize);
  }

  // Vẽ khung góc ở from và to
  drawCornerSquare(fx, fy);
  drawCornerSquare(tx, ty);
}

// Kiểm tra nếu nước đi có hợp lệ cho quân cờ đã chọn
// red: ['帥', '仕', '相', '傌', '俥', '炮', '卒'],
// green: ['將', '士', '象', '馬', '車', '砲', '兵']
function isMove(board, move) {
    var fromX = move.fromX;
    var fromY = move.fromY;
    var toX = move.toX;
    var toY = move.toY;
    var turn = move.color;

    // Kiểm tra xem vị trí đến có bị chiếm bởi quân của người chơi không
    var check = getPiece(board, toX, toY);

    if (check && check.color == move.color) {
        if( check.piece == '俥' && move.piece == '將') console.log(123);
        return false; // Nước đi không hợp lệ nếu có quân của mình ở vị trí đến
    }

    if (move.toX < 1 || move.toX > 9 || move.toY < 1 || move.toY > 10) {
      return false;
    }

    switch (move.piece) {
        case '將': // Tướng chỉ di chuyển trong cung và từng ô một
            return Math.abs(fromX - toX) + Math.abs(fromY - toY) === 1 &&
              toX >= 4 && toX <= 6 && toY >= 8 && toY <= 10;
        case '帥':
            return Math.abs(fromX - toX) + Math.abs(fromY - toY) === 1 &&
              toX >= 4 && toX <= 6 && toY >= 1 && toY <= 3;
        case '士': // Sĩ di chuyển chéo trong cung
            return Math.abs(fromX - toX) === 1 && Math.abs(fromY - toY) === 1 &&
                   toX >= 4 && toX <= 6 && toY >= 8 && toY <= 10;
        case '仕':
            return Math.abs(fromX - toX) === 1 && Math.abs(fromY - toY) === 1 &&
                   toX >= 4 && toX <= 6 && toY >= 1 && toY <= 3;
        case '相': // Tượng di chuyển chéo 2 ô, không qua sông
            var middleX = (fromX + toX) / 2;
            var middleY = (fromY + toY) / 2;
            return Math.abs(fromX - toX) === 2 && Math.abs(fromY - toY) === 2 &&
                   toY < 6 && !isPieceAtPosition(board, middleX, middleY);
        case '象':
            var middleX = (fromX + toX) / 2;
            var middleY = (fromY + toY) / 2;
            return Math.abs(fromX - toX) === 2 && Math.abs(fromY - toY) === 2 &&
                   toY > 5 && !isPieceAtPosition(board, middleX, middleY);
        case '馬':
        case '傌': // Mã di chuyển hình chữ L
            var valid = false;
            // Kiểm tra nếu di chuyển 2 ô ngang và 1 ô dọc
            if (Math.abs(fromX - toX) === 2 && Math.abs(fromY - toY) === 1) {
                // Kiểm tra ô giữa trên trục ngang
                var middleX = (fromX + toX) / 2;
                if (!isPieceAtPosition(board, middleX, fromY)) {
                    valid = true;
                }
            } 
            // Kiểm tra nếu di chuyển 1 ô ngang và 2 ô dọc
            else if (Math.abs(fromX - toX) === 1 && Math.abs(fromY - toY) === 2) {
                // Kiểm tra ô giữa trên trục dọc
                var middleY = (fromY + toY) / 2;
                if (!isPieceAtPosition(board, fromX, middleY)) {
                    valid = true;
                }
            }
            return valid;
        case '車':
        case '俥': // Xe di chuyển dọc hoặc ngang không giới hạn khoảng cách
            var obstacle = 0;
            if (fromX === toX) {
              if (fromY > toY) {
                for (i = toY + 1; i < fromY; i++) {
                  if (isPieceAtPosition(board, toX, i)) obstacle++;
                }
              } else {
                for (i = fromY + 1; i < toY; i++) {
                  if (isPieceAtPosition(board, toX, i)) obstacle++;
                }
              }

              if (obstacle == 0) return true;
            } else if (fromY === toY) {
              if (fromX > toX) {
                for (i = toX + 1; i < fromX; i++) {
                  if (isPieceAtPosition(board, i, toY)) obstacle++;
                }
              } else {
                for (i = fromX + 1; i < toX; i++) {
                  if (isPieceAtPosition(board, i, toY)) obstacle++;
                }
              }

              if (obstacle == 0) return true;
            }

            return false;
        case '炮':
        case '砲': // Pháo di chuyển như xe nhưng có quy tắc ăn quân riêng
            var obstacle = 0;
            
            if (fromX === toX) {
              if (fromY > toY) {
                for (i = toY + 1; i < fromY; i++) {
                  if (isPieceAtPosition(board, toX, i)) obstacle++;
                }
              } else {
                for (i = fromY + 1; i < toY; i++) {
                  if (isPieceAtPosition(board, toX, i)) obstacle++;
                }
              }
              if (obstacle == 0 && !isPositionComputer(board, toX, toY, turn)) return true;
              if (obstacle == 1 && isPositionComputer(board, toX, toY, turn)) return true;

            } else if (fromY === toY) {
              if (fromX > toX) {
                for (i = toX + 1; i < fromX; i++) {
                  if (isPieceAtPosition(board, i, toY)) obstacle++;
                }
              } else {
                for (i = fromX + 1; i < toX; i++) {
                  if (isPieceAtPosition(board, i, toY)) obstacle++;
                }
              }

              if (obstacle == 0 && !isPositionComputer(board, toX, toY, turn)) return true;
              if (obstacle == 1 && isPositionComputer(board, toX, toY, turn)) return true;
            }

            return false;
        case '卒': // Tốt chỉ đi tiến một ô, qua sông được đi ngang
            return (fromY < toY && Math.abs(fromY - toY) === 1 && fromX === toX) || // Đi tiến
               (fromY >= 6 && fromY === toY && Math.abs(fromX - toX) === 1);  
        case '兵':
            return (fromY > toY && Math.abs(fromY - toY) === 1 && fromX === toX) || // Đi tiến
               (fromY <= 5 && fromY === toY && Math.abs(fromX - toX) === 1); 
        default:
            return false;
    }
}

function isValidMove(board, move) {
  if (isMove(board, move)) {
    var opponentKing = move.color === 'green' ? board.red['帥'][0] : board.green['將'][0];
    if (opponentKing.x == move.toX && opponentKing.y == move.toY) {
      return false;
    }

    // Check lỗi mặt tướng
    var simulatedBoard = JSON.parse(JSON.stringify(board));
    moving(simulatedBoard, move);

    if (isAttackKing(simulatedBoard, move.color)) {
      return false;
    }

    var redKing = simulatedBoard.red['帥'][0];
    var greenKing = simulatedBoard.green['將'][0];

    if (redKing.x == greenKing.x) {
      var obstruct = 0;
      for (i = redKing.y + 1; i < greenKing.y; i++) {
        if(isPieceAtPosition(simulatedBoard, redKing.x, i)) {
          obstruct++;
        }
      }

      if (obstruct == 0) return false;
    }

    return true;
  }
  
  return false;
}

function isPieceAtPosition(board, x, y) {
    const red = board.red;
    const green = board.green;
    const allPieces = { ...red, ...green }; // Gộp tất cả quân của cả hai bên
    for (const piece in allPieces) {
        if (allPieces[piece].some(position => position.x === x && position.y === y)) {
            return true; // Có quân cờ tại vị trí (x, y)
        }
    }
    return false; // Không có quân cờ tại vị trí (x, y)
}

function isPositionComputer(board, x, y, turn) {
  const red = board.red;
  const green = board.green;
  const computerPieces = turn === 'red' ? green : red; // Lấy quân cờ của computer
  for (const piece in computerPieces) {
      if (computerPieces[piece].some(position => position.x === x && position.y === y)) {
          return true; // Tìm thấy quân cờ của người chơi tại vị trí (x, y)
      }
  }
  return false; // Không tìm thấy quân cờ của người chơi tại vị trí (x, y)
}

function getPiece(board, x, y) {
  let pieceSymbol = null;
  let color = null;

  for (piece in board.red) {
      if (board.red[piece].some(position => position.x === x && position.y === y)) {
          pieceSymbol = piece;
          color = 'red';
      }
  }

  for (piece in board.green) {
      if (board.green[piece].some(position => position.x === x && position.y === y)) {
          pieceSymbol = piece;
          color = 'green'
      }
  }

  return {piece :pieceSymbol, color : color};
}

// Hàm vẽ ô vuông mờ
function drawHoverSquare(x, y) {
    drawSelectedPiece();

    ctx.beginPath();

    if (direct == 'forward') {
      ctx.arc(
          offsetX + (x - 1) * cellSize, 
          offsetY + (10 - y) * cellSize , 
          cellSize / 2 - 2, 
          0, 
          Math.PI * 2
      );
    } else {
      ctx.arc(
          offsetX + (9 - x) * cellSize, 
          offsetY + (y - 1) * cellSize , 
          cellSize / 2 - 2, 
          0, 
          Math.PI * 2
      );
    }
    
    ctx.fillStyle = "rgba(255, 255, 255, 0.5)"; // Màu trắng lợt
    ctx.fill();
    ctx.closePath();
}

function formatMove(piece, fromX, fromY, toX, toY) {
  return `${piece}${fromX}-${fromY}:${toX}-${toY}`;
}

function getMoveFromFormat(moveFormat) {
  // Tách phần quân cờ và vị trí
  const piece = moveFormat[0];
  
  // Sử dụng biểu thức chính quy để lấy các giá trị toạ độ
  const regex = /(\d+)-(\d+):(\d+)-(\d+)/;
  const match = moveFormat.match(regex);

  if (match) {
    const fromX = parseInt(match[1], 10);
    const fromY = parseInt(match[2], 10);
    const toX = parseInt(match[3], 10);
    const toY = parseInt(match[4], 10);

    return {
      piece,
      fromX,
      fromY,
      toX,
      toY
    };
  } else {
    return null; // Trường hợp không khớp định dạng
  }
}

function encodeBoard(red, green) {
  // Bảng 10x9 toàn dấu chấm
  let board = Array.from({ length: 10 }, () => Array(9).fill("."));

  // Map chữ Hán -> ký tự
  const mapRed = {
    "帥": "K",
    "仕": "A",
    "相": "B",
    "俥": "R",
    "傌": "N",
    "炮": "C",
    "卒": "P"
  };

  const mapGreen = {
    "將": "k",
    "士": "a",
    "象": "b",
    "車": "r",
    "馬": "n",
    "砲": "c",
    "兵": "p"
  };

  // Đặt quân đỏ
  for (let piece in red) {
    let symbol = mapRed[piece];
    red[piece].forEach(pos => {
      board[pos.y - 1][pos.x - 1] = symbol;
    });
  }

  // Đặt quân xanh
  for (let piece in green) {
    let symbol = mapGreen[piece];
    green[piece].forEach(pos => {
      board[pos.y - 1][pos.x - 1] = symbol;
    });
  }

  // Nối lại thành 1 chuỗi 90 ký tự
  return board.map(row => row.join("")).join("");
}

function saveBook() {
    if (!history.length) {
        alert("Chưa có nước đi nào để lưu!");
        return;
    }

    const select = document.getElementById('openingSelect');
    const selectedOption = select.options[select.selectedIndex];

    const opening_id = selectedOption.value;
    const opening_color = selectedOption.dataset.color; // lấy từ data-color

    // Lấy bản ghi cuối cùng
    const lastMove = history[history.length - 1];
    const imageObj = JSON.parse(lastMove.imageChess);

    // Xác định parent_image_chess
    let parent_image_chess = null;
    let preMove = null;
    if (history.length >= 3) {
        const parentMove = history[history.length - 3];
        const parentImageObj = JSON.parse(parentMove.imageChess);
        parent_image_chess = encodeBoard(parentImageObj.red, parentImageObj.green);

        preMove = history[history.length - 2];
        preMove = JSON.stringify({
          fromX: preMove.fromX,
          fromY: preMove.fromY,
          toX: preMove.toX,
          toY: preMove.toY,
          piece: preMove.piece,
          color: preMove.color
        });
    }

    // Kiểm tra màu hợp lệ
    if (lastMove.color !== opening_color) {
        document.getElementById('result').innerHTML = 'Không tìm thấy nước đi để lưu (màu không khớp với thế trận)!';
        return;
    }

    // Encode lại bàn cờ
    const image_chess = encodeBoard(imageObj.red, imageObj.green);

    // Đóng gói move thành chuỗi JSON
    const move = JSON.stringify({
        fromX: lastMove.fromX,
        fromY: lastMove.fromY,
        toX: lastMove.toX,
        toY: lastMove.toY,
        piece: lastMove.piece
    });

    const bookData = {
        image_chess: image_chess,
        color: lastMove.color,
        move: move,
        comment: document.getElementById('comment')?.value || null,
        opening_id: opening_id,
        step: history.length,
        pre_move: preMove,
        parent_image_chess: parent_image_chess,
    };

    // Gửi API
    fetch('/admin/books', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(bookData)
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('result').innerHTML = 'Lưu book thành công!';
    })
    .catch(err => {
        console.error(err);
        alert("Có lỗi khi lưu book!");
    });
}
  
function getMoves(board, piece, color) {
  const green = board.green;
  const red = board.red;
  const pieceSymbol = piece.piece;
  const x = piece.x;
  const y = piece.y;
  const moves = []; // Danh sách các nước đi hợp lệ
  const player = (color === 'red') ? red : green;

  var move = {
    piece: pieceSymbol,
    fromX: x,
    fromY: y, 
    color: color
  }

  // Tính các nước đi cho từng loại quân
  switch (pieceSymbol) {
    case '炮': case '砲': // Pháo
    case '車': case '俥': // Xe
      for (let i = x + 1; i <= 9; i++) {
        move.toX = i;
        move.toY = y;
        if (isValidMove(board, move)) moves.push({ x: i, y });
      }
      for (let i = x - 1; i >= 1; i--) {
        move.toX = i;
        move.toY = y;
        if (isValidMove(board, move)) moves.push({ x: i, y });
      }
      for (let i = y + 1; i <= 10; i++) {
        move.toX = x;
        move.toY = i;
        if (isValidMove(board, move)) moves.push({ x, y: i });
      }
      for (let i = y - 1; i >= 1; i--) {
        move.toX = x;
        move.toY = i;
        if (isValidMove(board, move)) moves.push({ x, y: i });
      }
      break;

    case '馬': case '傌': // Ngựa
      const horseMoves = [
        { dx: 2, dy: 1 }, { dx: 2, dy: -1 }, { dx: -2, dy: 1 }, { dx: -2, dy: -1 },
        { dx: 1, dy: 2 }, { dx: 1, dy: -2 }, { dx: -1, dy: 2 }, { dx: -1, dy: -2 }
      ];
      for (const move1 of horseMoves) {
        move.toX = x + move1.dx;
        move.toY = y + move1.dy;

        if (isValidMove(board, move)) moves.push({ x: move.toX, y: move.toY });
      }
      break;

    case '士': case '仕': // Ngựa
      const siMoves = [
        { dx: 1, dy: 1 }, { dx: 1, dy: -1 }, { dx: -1, dy: 1 }, { dx: -1, dy: -1 },
      ];
      for (const move1 of siMoves) {
        move.toX = x + move1.dx;
        move.toY = y + move1.dy;

        if (isValidMove(board, move)) moves.push({ x: move.toX, y: move.toY });
      }
      break;

    case '象': case '相': // Ngựa
      const elephantMoves = [
        { dx: 2, dy: 2 }, { dx: 2, dy: -2 }, { dx: -2, dy: 2 }, { dx: -2, dy: -2 },
      ];
      for (const move1 of elephantMoves) {
        move.toX = x + move1.dx;
        move.toY = y + move1.dy;
        if (isValidMove(board, move)) moves.push({ x: move.toX, y: move.toY });
      }
      break;

    case '兵': case '卒': // Chốt
    case '將': case '帥': // Tướng
      const checkMoves = [
        { dx: 1, dy: 0 }, { dx: 0, dy: 1 }, { dx: -1, dy: 0 }, { dx: 0, dy: -1 },
      ];
      for (const move1 of checkMoves) {
        move.toX = x + move1.dx;
        move.toY = y + move1.dy;
        
        if (isValidMove(board, move)) moves.push({ x: move.toX, y: move.toY });
      }
      break;

    default:
      break;
  }

  return moves;
}

// Thêm sự kiện click cho nút "Back"
document.getElementById('backButton').addEventListener('click', undoMove);

// Hàm undoMove để quay lại trạng thái trước đó
function undoMove() {
  lastMoveToDraw = null;
  if (history.length === 1) {
    document.getElementById('result').innerHTML = 'Không có nươc nào để quay lại';
    return;
  }

  // Trường hợp nước cuối cùng là của máy -> lùi 2 nước
  if (history[history.length - 1].color === computerColor) {
    // Lùi nước của máy
    history.pop();
    // Lùi thêm 1 nước của người
    if (history.length > 0) {
      history.pop();
    }
  } else {
    // Trường hợp nước cuối cùng là của người -> chỉ lùi 1 nước
    history.pop();
  }

  // Nếu còn lịch sử thì lấy trạng thái trước đó
  const lastMove = history[history.length - 1];
  board = JSON.parse(lastMove.imageChess);
  let move = {
    piece: lastMove.piece,
    fromX: lastMove.fromX,
    fromY: lastMove.fromY, 
    toX: lastMove.toX, 
    toY : lastMove.toY, 
    color: lastMove.color
  }
  moving(board, move);
  red = board.red;
  green = board.green;
  turn = lastMove.color === 'red' ? 'green' : 'red';

  let book = {comment: lastMove.comment}
  let variations = lastMove.variations;
  loadBookCommentAndVariations(variations, book);

  result = '';
  drawBoard();
  displayPieces();
}
