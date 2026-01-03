function getAllValidMoves(pieces, side) {
    let validMoves = [];
    
    // 1. Lọc danh sách quân cờ từ nguồn (pieces) truyền vào
    // Hỗ trợ cả mảng đối tượng Phaser và mảng đối tượng Clone
    const myPieces = pieces.filter(p => 
        p.active && 
        p.side === side && 
        (p.pieceData || p.col !== undefined) // Linh hoạt giữa Phaser Piece và Clone Object
    );

    myPieces.forEach(piece => {
        // Tối ưu: Chỉ quét 90 ô (Cờ tướng bàn cờ nhỏ, loop này vẫn đủ nhanh)
        for (let r = 0; r < 10; r++) {
            for (let c = 0; c < 9; c++) {
                
                // 2. Gọi isValidMove và truyền 'pieces' vào để nó check vật cản trên đúng nguồn đó
                const moveResult = isValidMove(piece, c, r, pieces);
                
                if (moveResult && moveResult.valid) {
                    validMoves.push({
                        piece: piece,
                        fromCol: piece.pieceData ? piece.pieceData.col : piece.col,
                        fromRow: piece.pieceData ? piece.pieceData.row : piece.row,
                        toCol: c,
                        toRow: r,
                        type: piece.texture ? piece.texture.key : piece.type
                    });
                }
            }
        }
    });

    return validMoves;
}

function getPieceAt(col, row, source = null) {
    const targetCol = Number(col);
    const targetRow = Number(row);

    if (source && Array.isArray(source)) {
        return source.find(p => {
            // Lấy tọa độ linh hoạt
            const pCol = p.col !== undefined ? p.col : (p.pieceData ? p.pieceData.col : null);
            const pRow = p.row !== undefined ? p.row : (p.pieceData ? p.pieceData.row : null);
            
            // QUAN TRỌNG: Quân phải còn hoạt động (active)
            // Nếu p.active không tồn tại, mặc định coi là true
            const isActive = p.active !== false; 
            
            return isActive && Number(pCol) === targetCol && Number(pRow) === targetRow;
        }) || null;
    }

    if (typeof allPieces !== 'undefined') {
        return allPieces.getChildren().find(p => 
            p.active && 
            Number(p.pieceData.col) === targetCol && 
            Number(p.pieceData.row) === targetRow
        ) || null;
    }
    return null;
}

function isValidMove(piece, newCol, newRow, piecesSource = null) {
    // 1. Xác định nguồn dữ liệu (Hỗ trợ AI clone hoặc Bàn cờ thật)
    const currentPieces = piecesSource || (typeof allPieces !== 'undefined' ? allPieces.getChildren() : []);

    // 2. Kiểm tra luật di chuyển cơ bản (không tốn nhiều hiệu năng)
    if (!checkBasicMove(piece, newCol, newRow, currentPieces)) {
        return { valid: false, errorType: 'normal' };
    }

    // 3. TẠO GIẢ LẬP SẠCH (Không sửa đổi piece gốc)
    const side = piece.side;
    const pieceId = piece.id || (piece.pieceData ? piece.pieceData.id : null);

    // Tạo mảng mới mô phỏng sau khi đi nước này
    const afterPieces = currentPieces
        .filter(p => {
            const pCol = Number(p.pieceData ? p.pieceData.col : p.col);
            const pRow = Number(p.pieceData ? p.pieceData.row : p.row);
            const isAtTarget = pCol === Number(newCol) && pRow === Number(newRow);
            // Loại bỏ quân địch tại ô đích
            return !(isAtTarget && p.side !== side);
        })
        .map(p => {
            const pId = p.id || (p.pieceData ? p.pieceData.id : null);
            const isMovingPiece = (p === piece || (pieceId && pId === pieceId));

            if (isMovingPiece) {
                // Clone sâu pieceData để không dính líu đến quân gốc
                return { 
                    ...p, 
                    col: Number(newCol), 
                    row: Number(newRow),
                    pieceData: p.pieceData ? { ...p.pieceData, col: Number(newCol), row: Number(newRow) } : undefined 
                };
            }
            
            // Cực kỳ quan trọng: Phải clone cả pieceData của những quân đứng yên
            return { 
                ...p, 
                pieceData: p.pieceData ? { ...p.pieceData } : undefined 
            };
        });

    // 4. KIỂM TRA LỖI CHIẾU TƯỚNG / LỘ MẶT TƯỚNG TRÊN MẢNG GIẢ LẬP
    if (isKingFaceToFace(afterPieces)) {
        return { valid: false, errorType: 'face' };
    }
    
    if (isKingInDanger(side, afterPieces)) {
        return { valid: false, errorType: 'check' };
    }

    // 1. Tạo vân tay cho hình cờ sắp tới
    const futureHash = serializeBoard(afterPieces);

    // 2. Kiểm tra trong gameHistory
    // Chúng ta cần kiểm tra các mốc: cách 4 nước (index - 4) và index - 8
    // TRONG isValidMove (khi giả lập nước đi cho quân 'piece')
    const len = gameHistory.length;

    if (!piecesSource && len >= 4) {
        const futureHash = serializeBoard(afterPieces);
        // Lấy danh sách ID quân địch mà nước đi này sẽ hăm dọa
       // const futureThreats = getThreatenedPieceIds(afterPieces, piece, side); 

        // Tìm quân cờ bị đuổi chung (giao điểm của các tập hợp hăm dọa)
        const secondLastMove = gameHistory[len - 2].threatedPiece; // Lượt trước của chính phe đang đi
        const fourthLastMove = gameHistory[len - 4].threatedPiece; // Lượt trước nữa của chính phe đang đi

        // Kiểm tra xem có quân nào bị hăm dọa LIÊN TỤC trong cả 3 lượt của phe này không
        const persistentTarget = secondLastMove.find(id => 
            fourthLastMove.includes(id)
        );

        if (persistentTarget && gameHistory[len - 3].board === futureHash) {
            console.warn(`Vi phạm: Quân ${piece} đang trường tầm quân ${persistentTarget}`);
            return { valid: false, errorType: 'persistent_chase' };
        }
    }

    return { valid: true };
}

function checkBasicMove(piece, newCol, newRow, piecesSource = null) {
    // Lấy tọa độ hiện tại (Hỗ trợ cả Phaser PieceData và Clone Object)
    const oldCol = piece.pieceData ? piece.pieceData.col : piece.col;
    const oldRow = piece.pieceData ? piece.pieceData.row : piece.row;
    
    const dCol = Math.abs(newCol - oldCol);
    const dRow = Math.abs(newRow - oldRow);

    // Tìm quân tại ô đích dựa trên nguồn dữ liệu truyền vào
    const target = getPieceAt(newCol, newRow, piecesSource);

    // 1. KHÔNG ĂN QUÂN MÌNH
    if (target && target.side === piece.side) return false;

    // Lấy Key (Hỗ trợ Phaser texture hoặc Clone type)
    const key = piece.texture ? piece.texture.key : piece.type;

    // 2. LUẬT PHÁO
    if (key.includes('Phao')) {
        if (oldCol !== newCol && oldRow !== newRow) return false;
        // Truyền piecesSource vào để đếm vật cản chính xác
        const count = countPiecesBetween(oldCol, oldRow, newCol, newRow, piecesSource);
        if (!target) return count === 0; // Đi trống: không vật cản
        return count === 1; // Ăn quân: phải có 1 ngòi
    }

    // 3. LUẬT TỐT
    if (key.includes('Tot')) {
        const isRed = (piece.side === 'R');
        if (isRed && newRow > oldRow) return false; // Đỏ không lùi (row giảm là tiến)
        if (!isRed && newRow < oldRow) return false; // Đen không lùi (row tăng là tiến)

        const hasCrossedRiver = isRed ? (oldRow <= 4) : (oldRow >= 5);
        if (!hasCrossedRiver) {
            return dRow === 1 && dCol === 0; 
        } else {
            return (dRow === 1 && dCol === 0) || (dRow === 0 && dCol === 1);
        }
    }

    // 4. LUẬT XE
    if (key.includes('Xe')) {
        if (oldCol !== newCol && oldRow !== newRow) return false;
        return countPiecesBetween(oldCol, oldRow, newCol, newRow, piecesSource) === 0;
    }

    // 5. LUẬT MÃ
    if (key.includes('Ma')) {
        if (!((dCol === 1 && dRow === 2) || (dCol === 2 && dRow === 1))) return false;
        let bCol = oldCol, bRow = oldRow;
        if (dRow === 2) bRow = (oldRow + newRow) / 2;
        else bCol = (oldCol + newCol) / 2;
        // Kiểm tra chân mã trên nguồn dữ liệu ảo
        return !getPieceAt(bCol, bRow, piecesSource);
    }

    // 6. LUẬT TƯỢNG (Voi)
    if (key.includes('Tuong') && !key.includes('Tuong_G')) {
        if (dCol !== 2 || dRow !== 2) return false;
        const overRiver = piece.side === 'R' ? newRow < 5 : newRow > 4;
        if (overRiver) return false;
        // Kiểm tra mắt tượng trên nguồn dữ liệu ảo
        return !getPieceAt((oldCol + newCol) / 2, (oldRow + newRow) / 2, piecesSource);
    }

    // 7. LUẬT SĨ
    if (key.includes('Si')) {
        if (dCol !== 1 || dRow !== 1) return false;
        const inPalace = newCol >= 3 && newCol <= 5 && (piece.side === 'R' ? newRow >= 7 : newRow <= 2);
        return inPalace;
    }

    // 8. LUẬT TƯỚNG (King)
    if (key.includes('Tuong_G')) {
        if (dCol + dRow !== 1) return false;
        const inPalace = newCol >= 3 && newCol <= 5 && (piece.side === 'R' ? newRow >= 7 : newRow <= 2);
        return inPalace;
    }

    return false;
}

function isKingFaceToFace(piecesSource = null) {
    // 1. Xác định nguồn dữ liệu (Hỗ trợ AI clone hoặc Bàn cờ thật)
    const source = piecesSource || (typeof allPieces !== 'undefined' ? allPieces.getChildren() : []);
    
    // 2. Tìm quân Tướng Đỏ và Tướng Đen trong nguồn dữ liệu
    // Hỗ trợ cả Phaser texture.key và Clone type
    const redKing = source.find(p => 
        p.active && 
        (p.texture ? p.texture.key === 'R_Tuong_G' : p.type === 'R_Tuong_G')
    );
    const blackKing = source.find(p => 
        p.active && 
        (p.texture ? p.texture.key === 'B_Tuong_G' : p.type === 'B_Tuong_G')
    );

    // Nếu một trong hai tướng không tồn tại (trong giả lập ăn tướng) thì không tính lộ mặt
    if (!redKing || !blackKing) return false;

    // Lấy tọa độ (Linh hoạt pieceData của Phaser hoặc tọa độ phẳng của Clone)
    const rCol = redKing.pieceData ? redKing.pieceData.col : redKing.col;
    const rRow = redKing.pieceData ? redKing.pieceData.row : redKing.row;
    const bCol = blackKing.pieceData ? blackKing.pieceData.col : blackKing.col;
    const bRow = blackKing.pieceData ? blackKing.pieceData.row : blackKing.row;

    // 3. KIỂM TRA: Hai tướng phải cùng cột
    if (Number(rCol) === Number(bCol)) {
        // Đếm số quân ở giữa hai tướng trên đúng nguồn dữ liệu piecesSource
        const count = countPiecesBetween(rCol, rRow, bCol, bRow, piecesSource);
        
        // Nếu không có quân cản ở giữa (count === 0) -> Lộ mặt Tướng (Vi phạm luật)
        if (count === 0) return true;
    }
    
    return false;
}

function isKingInDanger(side, piecesSource = null) {
    // 1. Xác định nguồn dữ liệu
    const source = piecesSource || (typeof allPieces !== 'undefined' ? allPieces.getChildren() : []);
    
    // 2. Tìm vị trí Tướng của phe mình (side)
    const king = source.find(p => 
        p.active && 
        (p.texture ? p.texture.key === (side + '_Tuong_G') : p.type === (side + '_Tuong_G'))
    );
    
    // Nếu không tìm thấy Tướng (có thể đã bị ăn trong giả lập sâu), coi như nguy hiểm
    if (!king) return true;

    const kingCol = king.pieceData ? king.pieceData.col : king.col;
    const kingRow = king.pieceData ? king.pieceData.row : king.row;

    // 3. Tìm tất cả quân của đối phương
    const opponentSide = (side === 'R') ? 'B' : 'R';
    const opponents = source.filter(p => p.active && p.side === opponentSide);

    // 4. Kiểm tra xem có quân địch nào ăn được Tướng không
    for (let p of opponents) {
        // QUAN TRỌNG: Chỉ dùng checkBasicMove để kiểm tra khả năng ăn quân.
        // Phải truyền piecesSource vào để quân địch tính toán vật cản/ngòi chính xác.
        if (checkBasicMove(p, kingCol, kingRow, source)) {
            return true;
        }
    }

    return false;
}

function countPiecesBetween(c1, r1, c2, r2, piecesSource = null) {
    let count = 0;
    
    // Ép kiểu Number để tránh lỗi so sánh chuỗi
    const col1 = Number(c1);
    const row1 = Number(r1);
    const col2 = Number(c2);
    const row2 = Number(r2);

    if (col1 === col2) { 
        // TRƯỜNG HỢP: ĐI DỌC
        const minRow = Math.min(row1, row2);
        const maxRow = Math.max(row1, row2);
        
        for (let r = minRow + 1; r < maxRow; r++) {
            // Truyền piecesSource vào getPieceAt để tìm trên đúng bàn cờ giả lập
            if (getPieceAt(col1, r, piecesSource)) {
                count++;
            }
        }
    } else if (row1 === row2) { 
        // TRƯỜNG HỢP: ĐI NGANG
        const minCol = Math.min(col1, col2);
        const maxCol = Math.max(col1, col2);
        
        for (let c = minCol + 1; c < maxCol; c++) {
            // Tương tự, truyền nguồn dữ liệu vào đây
            if (getPieceAt(c, row1, piecesSource)) {
                count++;
            }
        }
    }
    
    return count;
}

function getValidMovesForPiece(piecesSource, piece) {
    // 1. Xác định phe của quân cờ đang xét
    const side = piece.side;

    // 2. Lấy tất cả nước đi hợp lệ của phe đó trên nguồn dữ liệu tương ứng
    // Truyền piecesSource vào để getAllValidMoves tính toán trên đúng tập dữ liệu đó
    const allMoves = getAllValidMoves(piecesSource, side);
    // 3. Lọc ra những nước đi dành riêng cho quân cờ này
    // Lưu ý: Nếu là quân cờ clone, ta so sánh qua ID hoặc vị trí (Col, Row)
    const data = allMoves.filter(move => {
        if (move.piece.id && piece.id) {
            return move.piece.id === piece.id;
        }
        // Dự phòng nếu không có ID thì so sánh tham chiếu object
        return move.piece === piece;
    });

    return data;
}

function isCheckmate(pieces, side) {
    const myPieces = pieces.filter(p => p.active && p.side === side);

    for (let piece of myPieces) {
        for (let r = 0; r < 10; r++) {
            for (let c = 0; c < 9; c++) {
                // Tận dụng chính hàm isValidMove của bạn
                if (isValidMove(piece, c, r, pieces).valid) {
                    return false; // Chỉ cần 1 nước hợp lệ -> Chưa bí
                }
            }
        }
    }
    return true; // Duyệt hết không có nước nào -> Thua
}

function getThreatenedPieceIds(boardState, movingPiece, side) {
    const threats = [];
    const opponents = boardState.filter(p => p.side !== side && p.active !== false);

    for (let target of opponents) {
        const tCol = target.pieceData ? target.pieceData.col : target.col;
        const tRow = target.pieceData ? target.pieceData.row : target.row;
        
        // CHỈ dùng checkBasicMove ở đây, tuyệt đối không dùng isValidMove
        if (checkBasicMove(movingPiece, tCol, tRow, boardState) && target) {
            threats.push(target.id || target.pieceData.id);
        }
    }
    return threats;
}