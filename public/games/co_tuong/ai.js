function getPieceValue(type, col = null, row = null, side = null) {
    const values = {
        'Xe': 100,
        'Phao': 50,
        'Ma': 45,
        'Si': 20,
        'Tuong_T': 20, // Tượng tịnh
        'Tot': 10,
        'Tuong_G': 1000 // Tướng soái
    };

    // Tìm kiếm trong chuỗi type (ví dụ type là 'R_Xe' thì lấy được 'Xe')
    let baseValue = 0;
    if (type.includes('Tot') && row !== null) {
        // Giả định: Phe Đỏ (R) nằm dưới (row 5-9), Phe Đen (B) nằm trên (row 0-4)
        // Nếu không truyền side, ta có thể đoán side từ type (ví dụ 'R_Tot')
        const pieceSide = side || (type.startsWith('R') ? 'R' : 'B');
        
        const hasCrossedRiver = (pieceSide === 'R') ? (row <= 4) : (row >= 5);
        
        if (hasCrossedRiver) {
            baseValue += 16; //
        }
    }

    for (let key in values) {
        if (type.includes(key)) {
            return values[key] + baseValue;
        }
    }
    return 0;
}

async function startAIOrder(scene) {
    let aiSide = (playerSide === 'R') ? 'B' : 'R';

    // Nếu không phải Auto và không phải lượt của AI thiết lập sẵn thì dừng
    // (Giữ lại logic này nếu bạn chỉ muốn AI đánh hộ người chơi khi bật Auto)
    if (!isAutoAI && turn !== aiSide) return;

    if (isAutoAI) {
        aiSide = turn;
    }

    // Lấy dữ liệu quân cờ hiện tại để truyền vào các hàm logic
    const currentPieces = allPieces.getChildren();
    const currentBoard = serializeBoard(); // Đảm bảo hàm này trả về đúng format API cần
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

            // 1. ƯU TIÊN BOOK (Khai cuộc mẫu)
            if (data.source === 'book' && data.move) {
                console.log("AI lấy nước đi từ book");
                chosen = data.move;
            } 
            // 2. DÙNG STATS (Dữ liệu thống kê)
            else if (data.source === 'stats' && data.moves) {
                const goodMoves = data.moves.filter(m => m.score >= 0);

                if (goodMoves.length > 0) {
                    chosen = goodMoves[Math.floor(Math.random() * goodMoves.length)];
                    console.log("AI lấy nước đi từ thống kê");

                } else {
                    // 3. TOÀN NƯỚC XẤU: Chuyển sang dùng AI Local để tính toán
                    // badKeys nên được chuẩn hóa để pickBestMove dễ lọc
                    const badKeys = data.moves.map(m => ({
                        fromCol: m.fromCol,
                        fromRow: m.fromRow,
                        toCol: m.toCol,
                        toRow: m.toRow
                    }));
                    // console.log(badKeys);
                    
                    const filtered = pickBestMove(scene, aiSide, badKeys);

                    if (filtered && filtered.length > 0) {
                        const pick = filtered[0]; // pickBestMove thường đã sort nước ngon nhất lên đầu
                        chosen = { 
                            fromCol: pick.fromCol, 
                            fromRow: pick.fromRow, 
                            toCol: pick.toCol, 
                            toRow: pick.toRow 
                        };
                    }
                }
            }

            // THỰC THI NƯỚC ĐI TỪ API/AI LOCAL
            if (chosen) {
                const piece = getPieceAt(chosen.fromCol, chosen.fromRow, currentPieces);
                if (piece) {
                    executeMove(scene, piece, chosen.toCol, chosen.toRow);
                    moveExecuted = true;
                }
            }
        }
    } catch (error) {
        console.error("AI Error:", error);
    }

    // 4. FALLBACK: Nếu API lỗi hoặc không tìm được nước đi (Cơ chế phòng vệ cuối cùng)
    if (!moveExecuted) {
        console.warn("AI Fallback activated");
        let allValid = getAllValidMoves(currentPieces, aiSide);

        if (allValid && allValid.length > 0) {
            // 2. Chọn ngẫu nhiên một index trong mảng allValid
            const randomIndex = Math.floor(Math.random() * allValid.length);
            const randomPick = allValid[randomIndex];

            // 3. Gán nước đi ngẫu nhiên này cho biến chosen
            chosen = { 
                fromCol: randomPick.fromCol, 
                fromRow: randomPick.fromRow, 
                toCol: randomPick.toCol, 
                toRow: randomPick.toRow 
            };
            
            const piece = getPieceAt(chosen.fromCol, chosen.fromRow, currentPieces);
            executeMove(scene, piece, chosen.toCol, chosen.toRow);

            console.log("AI không tìm được nước đi tốt, thực hiện đánh random từ allValid.");
        }
    }
}

function pickBestMove(scene, side, badKeys = []) {
    // 1. Chuẩn bị dữ liệu nguồn (Source of truth)
    const currentPieces = allPieces.getChildren().map((p, index) => ({
        id: p.id || `piece_${index}`, // Đảm bảo luôn có ID
        side: p.side,
        type: p.texture.key,
        col: Number(p.pieceData ? p.pieceData.col : p.col),
        row: Number(p.pieceData ? p.pieceData.row : p.row),
        active: p.active
    }));
    
    let allMoves = getAllValidMoves(currentPieces, side);

    // 2. Lọc nước đi: Loại bỏ nước "tự sát" và nước nằm trong danh sách "tệ" từ API
    allMoves = allMoves.filter(move => {
        // Kiểm tra xem nước này có nằm trong badKeys (từ stats API) không
        const isBadKey = badKeys.some(bk => 
            bk.fromCol === move.fromCol && 
            bk.fromRow === move.fromRow && 
            bk.toCol === move.toCol && 
            bk.toRow === move.toRow
        );

        // Kiểm tra xem nước này có làm mất Tướng không
        const isKingDangerous = isBadKeyMove(currentPieces, move, side);

        return !isBadKey && !isKingDangerous;
    });

    if (allMoves.length === 0) return [];

    const opponentSide = (side === 'R') ? 'B' : 'R';

    // 3. ƯU TIÊN 1: CHIẾU BÍ (Checkmate)
    // Nếu có nước thắng ngay, đi luôn không cần suy nghĩ
    for (let move of allMoves) {
        if (simulateAndCheckWin(currentPieces, move, opponentSide)) {
            console.log("%c CHIẾU BÍ ĐẾN NƠI! ", "background: red; color: white;");
            return [move];
        }
    }

    // 4. CHẤM ĐIỂM CHIẾN THUẬT
    allMoves.forEach(move => {
        // Chấm điểm dựa trên Matrix hiện tại
        move.scoreDetail = getMoveScore(currentPieces, move, side);
        move.totalScore = move.scoreDetail.total;
    });

    // 5. SẮP XẾP VÀ TRẢ VỀ
    // Sắp xếp giảm dần theo điểm tổng
    allMoves.sort((a, b) => b.totalScore - a.totalScore);

    // --- DEBUG LOG ---
    // --- DEBUG TABLE ĐẦY ĐỦ ---
    console.log(`--- AI ANALYSIS (${side}) ---`);

    if (allMoves.length > 1) {
        let len = gameHistory.length;
        if (len >= 11) {
            if (gameHistory[len -1].board === gameHistory[len - 9].board 
                && gameHistory[len - 2].board === gameHistory[len - 10].board
                && gameHistory[len - 3].board === gameHistory[len - 11].board) {
                allMoves.shift();
            }
        }
    }

    for (let i = 0; i <= allMoves.length; i++) {
        if (i <= 3)
        console.table(allMoves[i].type, allMoves[i].scoreDetail);
    }   

    // Trả về danh sách các nước đi tốt nhất (có cùng điểm cao nhất)
    if (allMoves.length > 0) {
        const topScore = allMoves[0].totalScore;
        return allMoves.filter(m => m.totalScore === topScore);
    }

    return [];
}

function simulateAndCheckWin(currentPieces, move, opponentSide) {
    // 1. Tạo bản sao "sạch" (Chỉ lấy quân đang sống và làm phẳng dữ liệu)
    // Lưu ý: currentPieces truyền vào nên là mảng đã được xử lý qua getCleanPieces
    let virtualPieces = currentPieces.map(p => ({...p}));

    // 2. GIẢ LẬP ĂN QUÂN: Xóa quân đối phương tại ô đích (nếu có)
    virtualPieces = virtualPieces.filter(p => !(p.col === move.toCol && p.row === move.toRow));

    // 3. GIẢ LẬP DI CHUYỂN: Tìm quân mình vừa đi và cập nhật tọa độ
    // Ta tìm theo move.fromCol/fromRow vì move.piece lúc này có thể là object Phaser
    const fromCol = move.fromCol !== undefined ? move.fromCol : move.piece.pieceData.col;
    const fromRow = move.fromRow !== undefined ? move.fromRow : move.piece.pieceData.row;
    
    const movingPiece = virtualPieces.find(p => p.col === fromCol && p.row === fromRow);
    
    if (movingPiece) {
        movingPiece.col = move.toCol;
        movingPiece.row = move.toRow;
    }

    // 4. KIỂM TRA CHIẾU BÍ: 
    // Truyền mảng virtualPieces "sạch" vào hàm isCheckmate
    return isCheckmate(virtualPieces, opponentSide);
}

function getMoveScore(currentPieces, move, side) {
    const opponentSide = (side === 'R') ? 'B' : 'R';
    const myValue = getPieceValue(move.type);
    
    let detail = { capture: 0, evasion: 0, protection: 0, threat: 0, tactics: 0, penalty: 0, boardImpact: 0, total: 0 };

    // --- 1. TẠO TRẠNG THÁI GIẢ LẬP (Mảng sạch) ---
    // Trước khi đi
    // Bước tạo beforePieces (giả sử từ allPieces của Phaser)
    const beforePieces = allPieces.getChildren().map((p, index) => ({
        id: p.id || `piece_${index}`, // Đảm bảo luôn có ID
        side: p.side,
        type: p.texture.key,
        col: Number(p.pieceData ? p.pieceData.col : p.col),
        row: Number(p.pieceData ? p.pieceData.row : p.row),
        active: p.active
    }));

    // Khi tạo afterPieces từ beforePieces
    let afterPieces = beforePieces
    .map(p => ({ ...p })) // Copy từng object
    .filter(p => {
        const isAtTarget = p.col === move.toCol && p.row === move.toRow;
        return !isAtTarget;
    });

    const movingInAfter = getPieceAt(move.fromCol, move.fromRow, afterPieces);
    if (movingInAfter) {
        movingInAfter.col = move.toCol;
        movingInAfter.row = move.toRow;
    }

    // Xác định quân bị ăn (nếu có)
    const targetPiece = getPieceAt(move.toCol, move.toRow, beforePieces);

    const isSafeBefore = isActuallySafe(move.fromCol, move.fromRow, side, beforePieces);
    const isSafeAfter = isActuallySafe(move.toCol, move.toRow, side, afterPieces);
    // --- 2. TÍNH ĐIỂM ĂN QUÂN (CAPTURE) ---
    if (targetPiece) {
        const opponentValue = getPieceValue(targetPiece.type, targetPiece.col, targetPiece.row, targetPiece.side);
        // Nếu an toàn: ăn trọn. Nếu không an toàn: tính điểm trao đổi (Lời/Lỗ)
        detail.capture = isSafeAfter ? (opponentValue * 1) : ((opponentValue - myValue) * 1);
    }

    if (!isSafeBefore && isSafeAfter) {
        if(move.type.includes('Tuong_G')) detail.evasion -= 50;
        // AI chỉ thực sự muốn chạy khi tình thế từ "Nguy hiểm (lỗ)" sang "An toàn (hòa/lãi)"
        else detail.evasion += myValue * 1; 
    }

    // --- 4. TÍNH ĐIỂM HỆ LỤY ĐỒNG ĐỘI (BOARD IMPACT) ---
    // Kiểm tra xem nước đi này có làm "hở sườn" đồng đội không
    const myTeammates = beforePieces.filter(p => p.side === side && !(p.col === move.fromCol && p.row === move.fromRow));
    
    myTeammates.forEach(tm => {
        const wasSafe = isActuallySafe(tm.col, tm.row, side, beforePieces);
        const isNowSafe = isActuallySafe(tm.col, tm.row, side, afterPieces);

        if (wasSafe && !isNowSafe) {
            // Phạt khi làm đồng đội từ "An toàn thực tế" trở thành "Bị treo/Lỗ"
            detail.boardImpact -= getPieceValue(tm.type) * 1;
        }

        if (!wasSafe && isNowSafe) {
            // Phạt khi làm đồng đội từ "An toàn thực tế" trở thành "Bị treo/Lỗ"
            detail.boardImpact += getPieceValue(tm.type) * 1 + 10;
        }
    });

    // --- 5. TÍNH ĐIỂM HĂM DỌA (THREAT) ---
    if (isSafeAfter) {
        const potentialMoves = getValidMovesForPiece(afterPieces, movingInAfter);
        let totalThreatScore = 0;

        potentialMoves.forEach(m => {
            const enemy = getPieceAt(m.toCol, m.toRow, afterPieces);
            if (enemy && enemy.side !== side) {
                const enemyValue = getPieceValue(enemy.type);
                const myValue = getPieceValue(movingInAfter.type);
                
                // KIỂM TRA: Quân địch này có được bảo vệ không?
                const isEnemyProtected = isProtected(m.toCol, m.toRow, enemy.side, afterPieces);

                if (!isEnemyProtected) {
                    // TH1: Hăm quân không có bảo vệ -> ĂN TRỌN
                    // Ưu tiên cao nhất
                    totalThreatScore = Math.max(totalThreatScore, enemyValue * 0.4); 
                } else {
                    // TH2: Hăm quân CÓ bảo vệ -> ĐỔI QUÂN
                    if (enemyValue > myValue) {
                        // Chỉ có giá trị nếu quân địch to hơn quân mình (ví dụ: Pháo hăm Xe)
                        // Điểm = phần lãi nếu đổi quân
                        totalThreatScore = Math.max(totalThreatScore, (enemyValue - myValue) * 0.2);
                    } else {
                        // TH2: Hăm quân CÓ bảo vệ -> ĐỔI QUÂN HOẶC TẬP TRUNG HỎA LỰC
                        
                        // Đếm số lượng quân mình đang cùng "ngắm" vào mục tiêu này
                        const myAttackers = countAttackers(m.toCol, m.toRow, side, afterPieces);
                        // Đếm số lượng quân địch đang bảo vệ mục tiêu này
                        const enemyDefenders = countAttackers(m.toCol, m.toRow, enemy.side, afterPieces);

                        if (myAttackers > enemyDefenders) {
                            // THÊM: Nếu phe mình áp đảo số lượng (ví dụ 2 Xe soi 1 Sĩ có 1 Tượng bảo vệ)
                            // Đây là tư duy tấn công vũ bão: Sẵn sàng phá vỡ hàng phòng thủ
                            const overloadBonus = (enemyValue * 0.5); 
                            totalThreatScore = Math.max(totalThreatScore, overloadBonus);
                        } else if (enemyValue > myValue) {
                            // Chỉ có giá trị nếu quân địch to hơn quân mình (ví dụ: Pháo hăm Xe)
                            totalThreatScore = Math.max(totalThreatScore, (enemyValue - myValue) * 1);
                        } else {
                            // Nếu quân mình tiếp sức cho đồng đội (áp lực tăng lên dù chưa áp đảo)
                            // Thay vì chỉ cho 10 điểm, ta cộng thêm điểm "áp lực"
                            const pressureBonus = (myAttackers >= 2) ? 50 : 10;
                            totalThreatScore = Math.max(totalThreatScore, pressureBonus); 
                        }
                    }
                }
            }
        });
        detail.threat = totalThreatScore;
    }

    // 1. Điểm Penalty nếu nhảy vào chỗ chết
    if (!isSafeAfter) {
        // Nếu nhảy vào chỗ không an toàn, bị trừ điểm rất nặng 
        // Trừ dựa trên giá trị quân của mình (Ví dụ: Mã nhảy vào chỗ chết bị -400 điểm)
        detail.penalty -= (myValue * 10); 
    }

    // --- 6. CHIẾN THUẬT & HÌNH PHẠT ---
    // Chiếu tướng đối phương
    if (isKingInDanger(opponentSide, afterPieces)) detail.tactics += 50;
    
    // Tự nhiên nhảy Tướng lên (trừ khi bị chiếu)
    if (move.type.includes('Tuong_G') && !isKingInDanger(side, beforePieces)) {
        detail.penalty -= 60;
    }

    // --- TỔNG KẾT ---
    detail.total = detail.capture + detail.evasion + detail.protection + detail.threat + detail.tactics + detail.penalty + detail.boardImpact;

    return detail;
}

function isCellSafe(col, row, side, source) {
    const opponentSide = (side === 'R') ? 'B' : 'R';
    const enemies = source.filter(p => p.side === opponentSide);
    
    // Nếu có bất kỳ quân địch nào ăn được ô (col, row)
    for (let p of enemies) {
        if (checkBasicMove(p, col, row, source)) return false;
    }
    return true;
}

function isProtected(col, row, side, source) {
    const targetCol = Number(col);
    const targetRow = Number(row);
    
    // Tìm allies: CHỈ lấy những đồng đội KHÔNG đứng tại ô đang xét
    const allies = source.filter(p => 
        p.side === side && 
        !(Number(p.col) === targetCol && Number(p.row) === targetRow)
    );

    // Tạo bản đồ giả lập: Đặt một quân địch giả vào ô đó để check ngòi Pháo/vật cản
    const tempSource = source.filter(p => 
        !(Number(p.col) === targetCol && Number(p.row) === targetRow)
    );
    tempSource.push({ side: (side === 'R' ? 'B' : 'R'), col: targetCol, row: targetRow, type: 'dummy' });

    return allies.some(ally => checkBasicMove(ally, targetCol, targetRow, tempSource));
}

function isActuallySafe(col, row, side, source) {
    const isAbsSafe = isCellSafe(col, row, side, source);
    if (isAbsSafe) return true;

    // 1. Tìm tất cả quân địch đang hăm dọa
    const opponentSide = (side === 'R' ? 'B' : 'R');
    const attackers = source.filter(p => 
        p.side === opponentSide && 
        p.active !== false &&
        checkBasicMove(p, col, row, source)
    );
    if (attackers.length === 0) return true;

    // 2. Kiểm tra xem có quân bảo vệ thực sự hay không (Xử lý lỗi ngòi Pháo)
    const currentlyProtected = isProtectedSmart(col, row, side, source, attackers);
    if (!currentlyProtected) return false; 

    // 3. So sánh giá trị để quyết định an toàn thực tế
    const myPiece = getPieceAt(col, row, source);
    const myValue = myPiece ? getPieceValue(myPiece.type || myPiece.texture?.key) : 0;
    const minAttackerValue = Math.min(...attackers.map(p => getPieceValue(p.type || p.texture?.key)));

    return minAttackerValue > myValue;
}

function isProtectedSmart(col, row, side, source, attackers) {
    const targetCol = Number(col);
    const targetRow = Number(row);
    const opponentSide = (side === 'R' ? 'B' : 'R');

    // Giả lập quân địch rẻ nhất ăn vào ô đó
    const cheapestAttacker = attackers.reduce((prev, curr) => 
        getPieceValue(prev.type) < getPieceValue(curr.type) ? prev : curr
    );

    // Tạo bản đồ mới: Quân địch đã chiếm vị trí quân mình, vị trí cũ của quân địch trống trơn
    const afterAttackSource = source.map(p => {
        // Quân mình bị ăn (biến mất)
        if (Number(p.col) === targetCol && Number(p.row) === targetRow) {
            return { ...cheapestAttacker, col: targetCol, row: targetRow };
        }
        // Quân địch di chuyển đi (vị trí cũ của nó trống)
        if (p.id === cheapestAttacker.id) {
            return { ...p, active: false, col: -1, row: -1 };
        }
        return p;
    }).filter(p => p.active !== false);

    // Tìm đồng đội có thể ăn lại quân địch vừa nhảy vào
    const allies = afterAttackSource.filter(p => p.side === side);
    
    return allies.some(ally => checkBasicMove(ally, targetCol, targetRow, afterAttackSource));
}

function isBadKeyMove(piecesSource, move, side) {
    const opponentSide = (side === 'R' ? 'B' : 'R');

    // --- BƯỚC 1: GIẢ LẬP NƯỚC ĐI CỦA TA ---
    let afterMyMove = simulateMove(piecesSource, move);

    // --- BƯỚC 2: GIẢ LẬP CÁC PHẢN CÔNG CỦA ĐỊCH ---
    // Lấy tất cả nước đi hợp lệ mà địch CÓ THỂ đi sau khi ta vừa di chuyển
    const opponentMoves = getAllValidMoves(afterMyMove, opponentSide);

    for (let opMove of opponentMoves) {
        // Giả lập nước đi của địch
        let afterOpponentMove = simulateMove(afterMyMove, opMove);

        if (isCheckmate(afterOpponentMove, side)) {
            console.log("Cảnh báo: Nước đi này dẫn đến bị bí cờ sau phản công của địch!");
            return true;
        }
    }

    const len = gameHistory.length;
    if (len >= 4) {
        const futureHash = serializeBoard(afterMyMove);
       // const futureThreats = getThreatenedPieceIds(afterPieces, piece, side); 

        // Tìm quân cờ bị đuổi chung (giao điểm của các tập hợp hăm dọa)
        const secondLastMove = gameHistory[len - 2].threatedPiece; // Lượt trước của chính phe đang đi
        const fourthLastMove = gameHistory[len - 4].threatedPiece; // Lượt trước nữa của chính phe đang đi

        // Kiểm tra xem có quân nào bị hăm dọa LIÊN TỤC trong cả 3 lượt của phe này không
        const persistentTarget = secondLastMove.find(id => 
            fourthLastMove.includes(id)
        );

        if (gameHistory[len - 4].board === futureHash) {
            if (persistentTarget) {
                console.warn(`Vi phạm: Quân ${move.type} trường tầm quân ${persistentTarget}`);
                return true;
            }
        }
        
    }

    return false; // Vượt qua được mọi phản công của địch
}

// Hàm bổ trợ để giả lập nước đi sạch sẽ
function simulateMove(source, m) {
    let virtual = source
        .filter(p => p.active !== false && !(Number(p.col) === m.toCol && Number(p.row) === m.toRow))
        .map(p => ({ ...p }));

    const moving = virtual.find(p => 
        (Number(p.col) === m.fromCol && Number(p.row) === m.fromRow)
    );
    
    if (moving) {
        moving.col = m.toCol;
        moving.row = m.toRow;
    }
    return virtual;
}

function countAttackers(col, row, side, boardState) {
    let count = 0;
    const pieces = boardState.filter(p => p.side === side && p.active !== false);
    
    pieces.forEach(p => {
        // Sử dụng checkBasicMove để tránh đệ quy
        if (checkBasicMove(p, col, row, boardState)) {
            count++;
        }
    });
    return count;
}