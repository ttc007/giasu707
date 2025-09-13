  function moving(board, move) {
    var green = board.green;
    var red = board.red;
    let computerPieces = move.color === 'green' ? green : red;
    let opponentPieces = move.color === 'red' ? green : red;
    let die;

    let {piece: piece, fromX: initialBishopX, fromY : initialBishopY, toX : targetX, toY : targetY} = move;

    computerPieces[piece] = computerPieces[piece].filter(pos => !(pos.x === initialBishopX && pos.y === initialBishopY));
    computerPieces[piece].push({ x: targetX, y: targetY });

    // Kiểm tra xem vị trí mới có quân của đối thủ không

    for (const piece in opponentPieces) {
        const index = opponentPieces[piece].findIndex(pos => pos.x === targetX && pos.y === targetY);
        if (index !== -1) {
            die = piece; // Lưu thông tin quân cờ bị ăn
            opponentPieces[piece].splice(index, 1); // Xóa quân cờ bị ăn khỏi mảng của đối thủ
            break;
        }
    }

    return die;
  }

  // Hàm để máy tính chọn quân cờ và di chuyển ngẫu nhiên
  // red: ['帥', '仕', '相', '傌', '俥', '炮', '卒'],
  // green: ['將', '士', '象', '馬', '車', '砲', '兵']
  function computerMove1() {
    let computerPieces = turn == 'green' ? green : red;
    let opponentPieces = turn === 'red' ? green : red;
    let color = turn == 'green' ? 'green' : 'red';

    let move = getBookMove();
    if (move) {
      let {piece: piece, fromX: initialBishopX, fromY : initialBishopY, toX : targetX, toY : targetY} = move;

      move.color = turn;
      var board = {red:red, green:green}
      var simulatedBoard = JSON.parse(JSON.stringify(board));
      moving(board, move, history);

      // Vẽ lại bàn cờ để cập nhật vị trí quân cờ mới
      history.push({
          'color': turn, // Màu của máy tính
          'piece': piece,
          'fromX': initialBishopX,
          'fromY': initialBishopY,
          'toX': targetX,
          'toY': targetY,
          'imageChess' : JSON.stringify(simulatedBoard)
      });
      drawBoard();
      displayPieces();
      turn = turn === 'red' ? 'green' : 'red';
    } else {
      var board = {red:red, green: green}

      var scoreArr = [];
      var movesArr = [];

      let computerPieces = turn == 'green' ? green : red;
      let color = turn;

      for (const piece in computerPieces) {
        // Duyệt qua từng quân cờ (mảng các vị trí của quân cờ đó)
        for (const position of computerPieces[piece]) {
          // Truy cập x và y của từng vị trí
          const { x, y } = position;
          var piece1 = {piece: piece, x: x, y: y}

          var moves = getMoves(board, piece1, color);

          for (move of moves) {
            // Tạo bản sao của bàn cờ để giả lập nước đi
            var simulatedBoard = JSON.parse(JSON.stringify(board));
            var move1 = {
              piece: piece,
              fromX: x,
              fromY: y, 
              toX: move.x, 
              toY : move.y, 
              color: turn
            }
            moving(simulatedBoard, move1);

            var opponentColor = turn === 'green' ? 'red' : 'green';
            var bestMoveOpponent = getBestMove(simulatedBoard, opponentColor);

            var simulatedBoard1 = JSON.parse(JSON.stringify(simulatedBoard));

            moving(simulatedBoard1, bestMoveOpponent.bestMove);

            var bestMove = getBestMove(simulatedBoard1, turn);

            boardScore = calculateBoardScore(simulatedBoard1);

            var maxScore = bestMove.maxScore;

            // Lưu lại nước đi và điểm số vào mảng
            scoreArr.push(bestMove.maxScore);
            movesArr.push({ move: move1, score: maxScore});
          }
        }
      }

      // Tìm nước đi có điểm số cao nhất
      var maxScore = Math.max(...scoreArr);

      if (maxScore > -1000) {
        var bestMove = movesArr.find(m => m.score === maxScore).move;

        var simulatedBoard = JSON.parse(JSON.stringify(board));
        var moveAI = bestMove;
        moving(board, moveAI);
        // Vẽ lại bàn cờ để cập nhật vị trí quân cờ mới
        moveAI.color = turn;
        moveAI.imageChess = JSON.stringify(simulatedBoard);
        history.push(moveAI);

        drawBoard();
        displayPieces();
        turn = turn === 'red' ? 'green' : 'red';
      } else {
        result = turn === 'red' ? 'Cờ xanh thắng' : 'Cờ đỏ thắng';
        ctx.font = "16px Arial";
        ctx.fillStyle = "red";
        ctx.textAlign = "center";
        ctx.fillText(result, 164,205);
      }
      
    }
  }

  function getMoveForCurrentBoard(board, turn) {
    const boardHash = generateHash(JSON.stringify(board));
    const keyWithTurn = turn + "_" + boardHash;
    // Xác định màu của máy tính và lấy hình cờ tương ứng từ book
    const imageChessBook = computerColor === 'green' 
      ? JSON.parse(localStorage.getItem('imageChessBookGreen')) || {} 
      : JSON.parse(localStorage.getItem('imageChessBookRed')) || {};

    return imageChessBook[keyWithTurn] || null;  // Trả về nước đi nếu tìm thấy, nếu không thì trả về null
  }


  function getBestMove(board, turn) {
    var green = board.green;
    var red = board.red;
    var scoreArr = [];
    var movesArr = [];

    let computerPieces = turn == 'green' ? green : red;
    let color = turn;

    for (const piece in computerPieces) {
      // Duyệt qua từng quân cờ (mảng các vị trí của quân cờ đó)
      for (const position of computerPieces[piece]) {
        // Truy cập x và y của từng vị trí
        const { x, y } = position;
        var piece1 = {piece: piece, x: x, y: y}
        var moves = getMoves(board, piece1, color);

        for (move of moves) {
          // Tạo bản sao của bàn cờ để giả lập nước đi
          var simulatedBoard = JSON.parse(JSON.stringify(board));
          var move1 = {
            piece: piece,
            fromX: x,
            fromY: y, 
            toX: move.x, 
            toY : move.y, 
            color: turn
          }
          moving(simulatedBoard, move1);

          boardScore = calculateBoardScore(simulatedBoard);
          let greenScore = parseInt(boardScore.greenScore) || 0;
          let redScore = parseInt(boardScore.redScore) || 0;
          var moveScore = greenScore - redScore;
          playerScore = turn === 'green' ? moveScore : -moveScore;
          // Lưu lại nước đi và điểm số vào mảng
          scoreArr.push(playerScore);
          movesArr.push({ move: move1, score: playerScore });
        }
      }
    }

    // Tìm nước đi có điểm số cao nhất
    var maxScore = Math.max(...scoreArr);
    if (movesArr.length == 0){
      maxScore = -1000;
      bestMove = null;
    } else {
      var bestMove = movesArr.find(m => m.score === maxScore).move;
    }

    return {maxScore:maxScore, bestMove:bestMove}
  }

  function calculatePieceScore(board, piece, color) {
    // Tính điểm nội tại
    const intrinsicScore = getIntrinsicScore(piece.piece);

    // Tính điểm nước đi dựa trên số nước đi hợp lệ
    const possibleMoves = getMoves(board, piece, color);
    let mobilityScore = possibleMoves.length;
    if (piece.piece == '車' || piece.piece == '俥') mobilityScore *= 2;

    // Tính điểm bảo vệ
    let protectionScore = 0;
    if (piece.piece === '炮' || piece.piece === '砲') {  // Kiểm tra nếu là quân Pháo
        const opponentKing = color === 'red' ? board.green['將'][0] : board.red['帥'][0];

        // Kiểm tra nếu quân Pháo ở cùng hàng hoặc cột với Tướng đối phương
        if (piece.x === opponentKing.x || piece.y === opponentKing.y) {
            const isFacingKing = checkIfFacingKing(board, piece, opponentKing);
            if (isFacingKing) {
              protectionScore = isFacingKing;
            }
        }
    }

    // Điểm tổng hợp = Điểm nội tại + Điểm nước đi + Điểm bảo vệ
    return intrinsicScore + mobilityScore + protectionScore;
  }

  // Hàm kiểm tra nếu quân Pháo đang đối mặt với Tướng
  function checkIfFacingKing(board, cannon, king) {
      // Kiểm tra nếu Pháo và Tướng cùng cột
      if (cannon.x === king.x) {
          const minY = Math.min(cannon.y, king.y);
          const maxY = Math.max(cannon.y, king.y);

          // Đếm số quân cờ giữa Pháo và Tướng
          let pieceCount = 0;
          for (let y = minY + 1; y < maxY; y++) {
              pieceCount += 2;
              if (isPieceAtPosition(board, king.x, y) ) {
                return 0;
              }
          }

          return pieceCount;
      }

      // Kiểm tra nếu Pháo và Tướng cùng hàng
      if (cannon.y === king.y) {
          const minX = Math.min(cannon.x, king.x);
          const maxX = Math.max(cannon.x, king.x);

          // Đếm số quân cờ giữa Pháo và Tướng
          let pieceCount = 0;
          for (let x = minX + 1; x < maxX; x++) {
              pieceCount += 2;
              if (isPieceAtPosition(board, x, king.y) ) {
                  return 0;
              }
          }

          return pieceCount;
      }

      return 0;
  }


  function calculateBoardScore(board) {
    var red = board.red;
    var green = board.green;
    var redScore = 0;
    var greenScore = 0;

    for (const piece in red) {
        // Duyệt qua từng quân cờ (mảng các vị trí của quân cờ đó)
        for (const position of red[piece]) {
          // Truy cập x và y của từng vị trí
          const { x, y } = position;
          var piece1 = {piece: piece, x: x, y: y}
          var score = calculatePieceScore(board, piece1, 'red');
          redScore += score;
        }
    }

    for (const piece in green) {
        // Duyệt qua từng quân cờ (mảng các vị trí của quân cờ đó)
        for (const position of green[piece]) {
          // Truy cập x và y của từng vị trí
          const { x, y } = position;
          var piece1 = {piece: piece, x: x, y: y}
          var score = calculatePieceScore(board, piece1, 'green');
          greenScore += score;
        }
    }

    return {redScore:redScore, greenScore: greenScore}
  }

  function getIntrinsicScore(piece) {
    // Định nghĩa điểm số cho từng quân cờ, bao gồm cả các phiên bản của từng bên
    let coefficient = 5;
    const pieceScores = {
      '車': 10,    // Xe (Xe đỏ)
      '俥': 10,    // Xe (Xe xanh)
      '炮': 7,     // Pháo (Pháo đỏ)
      '砲': 7,     // Pháo (Pháo xanh)
      '馬': 3,     // Ngựa (Ngựa đỏ)
      '傌': 3,     // Ngựa (Ngựa xanh)
      '將': 1000,  // Tướng (Tướng xanh)
      '帥': 1000,  // Tướng (Tướng đỏ)
      '兵': 1,     // Chốt (Chốt đỏ)
      '卒': 1,     // Chốt (Chốt xanh)
      '士': 2,     // Sĩ (Sĩ đỏ)
      '仕': 2,     // Sĩ (Sĩ xanh)
      '象': 2,     // Tượng (Tượng đỏ)
      '相': 2      // Tượng (Tượng xanh)
    };

    // Trả về điểm của quân cờ, hoặc 0 nếu không tìm thấy
    return pieceScores[piece] * coefficient || 0;
  }

  function isAttackKing(board, color) {
    // Tìm vị trí Tướng đối phương

    const king = color === 'red' ? board.red['帥'][0] : board.green['將'][0];

    if (!king) return false; // Nếu không tìm thấy Tướng, không có chiếu

    // Kiểm tra các quân của người chơi hiện tại xem có quân nào có thể tấn công Tướng đối phương
    var opponent = color === 'green' ? board.red : board.green;
    for (const piece in opponent) {
      if (['車', '俥', '馬', '傌', '炮', '砲', '兵', '卒'].includes(piece)) {
        for (position of opponent[piece]) {
          var move = {
            piece:piece,
            fromX: position.x,
            fromY: position.y,
            toX: king.x, 
            toY: king.y, 
            color: color == 'red' ? 'green' : 'red'
          }

          if (isMove(board, move)) {
            return true; // Có một quân có thể di chuyển đến vị trí Tướng, tức là chiếu
          }
        }
      }
      
    }

    return false; // Không có quân nào chiếu Tướng
  }

