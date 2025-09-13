function computerMove() {
  
}

document.getElementById('openingSelect').addEventListener('change', async function () {
    const openingId = this.value;
    loadOpening(openingId);
});

// Hàm loadBookById để gọi API lấy hình cờ của biến thể
function loadBookByVariationId(id) {
    fetch(`/api/get-book-from-variation/${id}`)
        .then(res => res.json())
        .then(data => {
            const book = data.data.book;

	            // Giải mã hình cờ từ book
	            const decoded = decodeBoard(book.image_chess);
	            board = decoded;
	            move = JSON.parse(book.move);
	            move.color = book.color;
	            var simulatedBoard = JSON.parse(JSON.stringify(board));
		        moving(board, move);

		        history.push({
		          'color' : book.color, 
		          'piece' : move.piece,
		          'fromX' : move.fromX,
		          'fromY' : move.fromY,
		          'toX': move.toX ,
		          'toY': move.toY ,
		          'imageChess' : JSON.stringify(simulatedBoard)
		        });

		        red = board.red;
		        green = board.green;

		        drawBoard();
		        displayPieces();

		        // Chuyển lượt chơi
		        turn = book.color === 'red' ? 'green' : 'red';

		        document.getElementById('comment').value = book.comment;
		        const variations = data.data.variations;
		        loadBookCommentAndVariations(variations, book);
        });
}

function loadOpening(openingId) {
    try {
        fetch(`/api/books/opening/${openingId}`)
	        .then(res => res.json())
	    	.then(data => {
		        const book = data.data.book;

	            // Giải mã hình cờ từ book
	            const decoded = decodeBoard(book.image_chess);
	            board = decoded;
	            move = JSON.parse(book.move);
	            move.color = book.color;
	            var simulatedBoard = JSON.parse(JSON.stringify(board));
		        moving(board, move);

		        history.push({
		          'color' : book.color, 
		          'piece' : move.piece,
		          'fromX' : move.fromX,
		          'fromY' : move.fromY,
		          'toX': move.toX ,
		          'toY': move.toY ,
		          'imageChess' : JSON.stringify(simulatedBoard)
		        });

		        red = board.red;
		        green = board.green;

		        drawBoard();
		        displayPieces();

		        // Chuyển lượt chơi
		        turn = book.color === 'red' ? 'green' : 'red';

		        document.getElementById('comment').value = book.comment;
		        const variations = data.data.variations;
		        loadBookCommentAndVariations(variations, book);
	    	});
    } catch (err) {
        console.error(err);
        alert("Có lỗi khi tải thế trận!");
    }
}

const openingIdStart = document.getElementById('openingSelect').value;
loadOpening(openingIdStart);

const pieceMap = {
  'R': '俥', 'N': '傌', 'B': '相', 'A': '仕', 'K': '帥', 'C': '炮', 'P': '卒',
  'r': '車', 'n': '馬', 'b': '象', 'a': '士', 'k': '將', 'c': '砲', 'p': '兵',
};

function decodeBoard(encoded) {
    const red = { '仕': [], '相': [], '傌': [], '俥': [], '炮': [], '卒': [], '帥': [] };
    const green = { '士': [], '象': [], '馬': [], '車': [], '砲': [], '兵': [], '將': [] };

    if (encoded.length !== 90) {
        console.error("Encoded phải đúng 90 ký tự! Hiện tại:", encoded.length);
        return { red, green };
    }

    for (let i = 0; i < 90; i++) {
        const char = encoded[i];
        if (char === '.') continue;

        const piece = pieceMap[char];
        if (!piece) continue;

        const x = (i % 9) + 1;        // cột (1–9)
        const y = Math.floor(i / 9) + 1; // hàng (1–10)

        if ('仕相傌俥炮卒帥'.includes(piece)) {
            red[piece].push({ x, y });
        } else {
            green[piece].push({ x, y });
        }
    }

    return { red, green };
}

function loadBookCommentAndVariations(variations, book) {
    let resultHTML = book.comment ? `<p><strong>Lời bình:</strong> ${book.comment}</p>` : "";

	if (variations && variations.length > 0) {
	    resultHTML += `<p><strong>Các biến thể của đối phương thường gặp:</strong></p>`;
	    variations.forEach((v, index) => {
	    	let move = JSON.parse(v.move);
	        // Tạo button, khi click sẽ gọi hàm loadBookById với id của book biến thể
	        resultHTML += `<button class="variation-btn" onclick="loadBookByVariationId(${v.id})">
	            ${index + 1}. ${move.piece} (${move.fromX},${move.fromY}) -> (${move.toX},${move.toY})
	        </button><br>`;
	    });

	}
    document.getElementById('result').innerHTML = resultHTML;

}