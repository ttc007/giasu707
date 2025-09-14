function computerMove() {
	const bookData = {
		image_chess: encodeBoard(red, green),
		color: computerColor
	}

	fetch(`/api/get-book-from-image`, {
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
        	if (data.data) {
        		const book = data.data.book;
		        const variations = data.data.variations;

	            // Giải mã hình cờ từ book
	            const decoded = decodeBoard(book.image_chess);
	            board = decoded;
	            move = JSON.parse(book.move);
	            move.color = book.color;
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
		          'color' : book.color, 
		          'piece' : move.piece,
		          'fromX' : move.fromX,
		          'fromY' : move.fromY,
		          'toX': move.toX ,
		          'toY': move.toY ,
		          'imageChess' : JSON.stringify(simulatedBoard),
		          'comment': book.comment,
		          'variations': variations
		        });

		        red = board.red;
		        green = board.green;

		        drawBoard();
		        displayPieces();

		        // Chuyển lượt chơi
		        turn = book.color === 'red' ? 'green' : 'red';

		        document.getElementById('comment').value = book.comment;
		        loadBookCommentAndVariations(variations, book);
        	} else {
        		document.getElementById('result').innerHTML = "Chưa có trong book";
        	}
        });
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
        	// Chuyển lượt chơi

            const book = data.data.book;
	        const variations = data.data.variations;

	        turn = book.color === 'red' ? 'green' : 'red';

            // Giải mã hình cờ từ book
            const decoded = decodeBoard(book.image_chess);
            board = decoded;
            move = JSON.parse(book.move);
            move.color = book.color;

            history.push({
				'color' : turn, 
				'imageChess' : JSON.stringify(board),
	        });

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
				'color' : book.color, 
				'piece' : move.piece,
				'fromX' : move.fromX,
				'fromY' : move.fromY,
				'toX': move.toX ,
				'toY': move.toY ,
				'imageChess' : JSON.stringify(simulatedBoard),
				'comment': book.comment,
				'variations': variations
	        });

	        red = board.red;
	        green = board.green;

	        drawBoard();
	        displayPieces();

	        document.getElementById('comment').value = book.comment;
	        loadBookCommentAndVariations(variations, book);
        });
}

function loadOpening(openingId) {
	const select = document.getElementById('openingSelect');
    const selectedOption = select.options[select.selectedIndex];

    const opening_color = selectedOption.dataset.color;
	computerColor = opening_color;

    if ((opening_color === 'green' && direct === 'forward') || ((opening_color === 'red' && direct !== 'forward'))) {
    	rotate();
    }

    let step = 1;
    if (opening_color === 'green') step = 2;
    try {
        fetch(`/api/books/opening/${openingId}/${step}`)
	        .then(res => res.json())
	    	.then(data => {
	    		if (data.data) {
	    			const book = data.data.book;

		            // Giải mã hình cờ từ book
		            const decoded = decodeBoard(book.image_chess);
		            board = decoded;
		            move = JSON.parse(book.move);
		            move.color = book.color;
		            var simulatedBoard = JSON.parse(JSON.stringify(board));
			        moving(board, move);
		        	lastMoveToDraw = {
					  fromX: move.fromX,
					  fromY: move.fromY,
					  toX: move.toX,
					  toY: move.toY,
					  color: 'blue'
					};

			        red = board.red;
			        green = board.green;

			        drawBoard();
			        displayPieces();

			        // Chuyển lượt chơi
			        turn = book.color === 'red' ? 'green' : 'red';

			        document.getElementById('comment').value = book.comment;
			        const variations = data.data.variations;
			        loadBookCommentAndVariations(variations, book);
			        history.push({
						'color' : book.color, 
						'piece' : move.piece,
						'fromX' : move.fromX,
						'fromY' : move.fromY,
						'toX': move.toX ,
						'toY': move.toY ,
						'imageChess' : JSON.stringify(simulatedBoard),
						'comment': book.comment,
						'variations': variations
			        });
	    		} else {
    				playerColor = 'red';
					turn = 'red';
					history = [];

					if (playerColor == 'red') {
					red = {
					  '仕': [{x: 4, y : 1}, {x: 6, y: 1}],
					  '相': [{x: 3, y : 1}, {x:7, y: 1}],
					  '傌': [{x: 2, y : 1}, {x:8, y: 1}],
					  '俥': [{x: 1, y : 1}, {x:9, y: 1}],
					  '炮': [{x: 2, y : 3}, {x:8, y: 3}],
					  '卒':[{x:1, y:4}, {x:3, y:4}, {x:5, y:4}, {x:7, y:4}, {x:9, y:4}],
					  '帥': [{x: 5, y : 1}]
					};

					green = {
					  '士': [{x: 4, y : 10}, {x: 6, y: 10}],
					  '象': [{x: 3, y : 10}, {x:7, y: 10}],
					  '馬': [{x: 2, y : 10}, {x:8, y: 10}],
					  '車': [{x: 1, y : 10}, {x:9, y: 10}],
					  '砲': [{x: 2, y : 8}, {x:8, y: 8}],
					  '兵':[{x:1, y:7}, {x:3, y:7}, {x:5, y:7}, {x:7, y:7}, {x:9, y:7}],
					  '將': [{x: 5, y : 10}]
					};
					} else {
					red = {
					  '仕': [{x: 4, y : 10}, {x: 6, y: 10}],
					  '相': [{x: 3, y : 10}, {x:7, y: 10}],
					  '傌': [{x: 2, y : 10}, {x:8, y: 10}],
					  '俥': [{x: 1, y : 10}, {x:9, y: 10}],
					  '砲': [{x: 2, y : 8}, {x:8, y: 8}],
					  '卒':[{x:1, y:7}, {x:3, y:7}, {x:5, y:7}, {x:7, y:7}, {x:9, y:7}],
					  '帥': [{x: 5, y : 10}]
					};

					green = {
					  '士': [{x: 4, y : 1}, {x: 6, y: 1}],
					  '象': [{x: 3, y : 1}, {x:7, y: 1}],
					  '馬': [{x: 2, y : 1}, {x:8, y: 1}],
					  '車': [{x: 1, y : 1}, {x:9, y: 1}],
					  '炮': [{x: 2, y : 3}, {x:8, y: 3}],
					  '兵':[{x:1, y:4}, {x:3, y:4}, {x:5, y:4}, {x:7, y:4}, {x:9, y:4}],
					  '將': [{x: 5, y : 1}]
					};
					}

					drawBoard(); // Vẽ bàn cờ lại
					displayPieces(); // Hiển thị quân cờ
					document.getElementById('result').innerHTML = 'Chưa có trong book!';
					document.getElementById('comment').value = '';
	    		}
		        
	    	});
    } catch (err) {
        alert("Có lỗi khi tải thế trận!");
    }
}

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
    let resultHTML = "";

    // Nếu có lời bình
    if (book.comment && book.comment.trim() !== "") {
        resultHTML += `<p><strong>Lời bình:</strong> ${book.comment}</p>`;
    }

    // Nếu có biến thể
    if (variations && variations.length > 0) {
        resultHTML += `<p><strong>Các biến thể thường gặp của đối phương:</strong></p>`;
        resultHTML += `<div id="variations-container">`;

        variations.forEach((v, index) => {
            let move = JSON.parse(v.move);
            resultHTML += `
                <button class="variation-btn" 
                        onclick="loadBookByVariationId(${v.id})" >
                    ${index + 1}. ${move.piece} (${move.fromX},${move.fromY}) → (${move.toX},${move.toY})
                </button>
            `;
        });

        resultHTML += `</div>`;
    }

    // Render ra giao diện
    document.getElementById('result').innerHTML = resultHTML;
}
