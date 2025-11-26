
let board = [];
let currentPlayer = 'white';
let selectedSquare = null;
let selectedPosition = null;
let possibleMoves = [];
let currentDifficulty = 'easy';
let moveHistory = [];
let gameActive = true;
let gameMode = 'two-player'; 
let aiColor = 'black'; // 


const pieces = {
    'white': {
        'king': '♔', 'queen': '♕', 'rook': '♖', 'bishop': '♗', 'knight': '♘', 'pawn': '♙'
    },
    'black': {
        'king': '♚', 'queen': '♛', 'rook': '♜', 'bishop': '♝', 'knight': '♞', 'pawn': '♟'
    }
};


const pieceSymbols = { king: 'K', queen: 'Q', rook: 'R', bishop: 'B', knight: 'N', pawn: '' };


document.getElementById('play-btn').addEventListener('click', () => {
    document.getElementById('main-menu').style.display = 'none';
    document.getElementById('mode-menu').style.display = 'flex';
});

document.getElementById('two-player-btn').addEventListener('click', () => {
    gameMode = 'two-player';
    startNewGame();
    enterGameScreen();
});

document.getElementById('ai-mode-btn').addEventListener('click', () => {
    gameMode = 'ai';
    aiColor = 'black'; 
    startNewGame();
    enterGameScreen();
});

document.getElementById('settings-btn').addEventListener('click', () => {
    document.getElementById('main-menu').style.display = 'none';
    document.getElementById('settings-menu').style.display = 'flex';
});

document.getElementById('back-btn').addEventListener('click', () => {
    document.getElementById('settings-menu').style.display = 'none';
    document.getElementById('main-menu').style.display = 'flex';
});

document.getElementById('game-back-btn').addEventListener('click', () => {
    document.getElementById('game-screen').style.display = 'none';
    document.getElementById('move-history').style.display = 'none';
    document.getElementById('main-menu').style.display = 'flex';
});

document.getElementById('reset-btn').addEventListener('click', () => {
    startNewGame();
});

document.getElementById('difficulty-select').addEventListener('change', (e) => {
    currentDifficulty = e.target.value;
    document.getElementById('difficulty-badge').textContent = 
        e.target.value === 'easy' ? 'KOLAY' : 
        e.target.value === 'medium' ? 'ORTA' : 'ZOR';
});


function enterGameScreen() {
    document.getElementById('mode-menu').style.display = 'none';
    document.getElementById('game-screen').style.display = 'flex';
    document.getElementById('move-history').style.display = 'block';
    document.getElementById('moves-list').innerHTML = '';
    moveHistory = [];
}


function initBoard() {
    return [
        ['b-rook', 'b-knight', 'b-bishop', 'b-queen', 'b-king', 'b-bishop', 'b-knight', 'b-rook'],
        ['b-pawn', 'b-pawn', 'b-pawn', 'b-pawn', 'b-pawn', 'b-pawn', 'b-pawn', 'b-pawn'],
        [null, null, null, null, null, null, null, null],
        [null, null, null, null, null, null, null, null],
        [null, null, null, null, null, null, null, null],
        [null, null, null, null, null, null, null, null],
        ['w-pawn', 'w-pawn', 'w-pawn', 'w-pawn', 'w-pawn', 'w-pawn', 'w-pawn', 'w-pawn'],
        ['w-rook', 'w-knight', 'w-bishop', 'w-queen', 'w-king', 'w-bishop', 'w-knight', 'w-rook']
    ];
}

function getPieceElement(piece) {
    if (!piece) return '';
    const color = piece.startsWith('w') ? 'white' : 'black';
    const type = piece.split('-')[1];
    return pieces[color][type];
}

function drawBoard() {
    const chessboard = document.getElementById('chessboard');
    chessboard.innerHTML = '';
    for (let row = 0; row < 8; row++) {
        for (let col = 0; col < 8; col++) {
            const square = document.createElement('div');
            square.className = `square ${(row + col) % 2 === 0 ? 'white' : 'black'}`;
            square.dataset.row = row;
            square.dataset.col = col;

            const piece = board[row][col];
            square.textContent = getPieceElement(piece);

            square.addEventListener('click', () => handleSquareClick(square, row, col));
            chessboard.appendChild(square);
        }
    }
}

function handleSquareClick(square, row, col) {
    if (!gameActive || (gameMode === 'ai' && currentPlayer === aiColor)) return;

    if (selectedSquare) {
        const fromRow = selectedPosition.row;
        const fromCol = selectedPosition.col;

        if (fromRow === row && fromCol === col) {
            clearSelection();
            return;
        }

        if (possibleMoves.some(move => move.row === row && move.col === col)) {
            const piece = board[fromRow][fromCol];
            const pieceType = piece.split('-')[1];
            const isCapture = board[row][col] !== null;

            movePiece(fromRow, fromCol, row, col);
            clearSelection();
            recordMove(pieceType, fromRow, fromCol, row, col, isCapture);

            if (isInCheck(currentPlayer === 'white' ? 'black' : 'white')) {
                if (isCheckmate(currentPlayer)) {
                    setTimeout(() => {
                        alert(`${currentPlayer.toUpperCase()} KAZANDI!`);
                        gameActive = false;
                    }, 100);
                } else {
                    console.log('Şah!');
                }
            } else if (isStalemate(currentPlayer)) {
                alert('PAT! Oyun berabere.');
                gameActive = false;
            }

            switchPlayer();

            
            if (gameMode === 'ai' && currentPlayer === aiColor && gameActive) {
                setTimeout(makeAiMove, 600);
            }
        } else {
            const piece = board[row][col];
            if (piece && piece.startsWith(currentPlayer === 'white' ? 'w' : 'b')) {
                clearSelection();
                selectNewPiece(square, row, col);
            } else {
                clearSelection();
            }
        }
    } else {
        const piece = board[row][col];
        if (piece && piece.startsWith(currentPlayer === 'white' ? 'w' : 'b')) {
            selectNewPiece(square, row, col);
        }
    }
}

function selectNewPiece(square, row, col) {
    selectedSquare = square;
    selectedPosition = { row, col };
    square.classList.add('selected');

    possibleMoves = calculatePossibleMoves(row, col);
    possibleMoves.forEach(move => {
        const el = document.querySelector(`.square[data-row="${move.row}"][data-col="${move.col}"]`);
        if (!el.classList.contains('selected')) {
            el.classList.add('possible-move');
        }
    });
}

function clearSelection() {
    if (selectedSquare) {
        selectedSquare.classList.remove('selected');
    }
    document.querySelectorAll('.possible-move').forEach(el => {
        el.classList.remove('possible-move');
    });
    selectedSquare = null;
    selectedPosition = null;
    possibleMoves = [];
}

function movePiece(fromRow, fromCol, toRow, toCol) {
    board[toRow][toCol] = board[fromRow][fromCol];
    board[fromRow][fromCol] = null;
    drawBoard();
}

function switchPlayer() {
    currentPlayer = currentPlayer === 'white' ? 'black' : 'white';
    document.getElementById('turn-indicator').textContent = `Sıra: ${currentPlayer === 'white' ? 'Beyaz' : 'Siyah'}`;
}


function recordMove(type, fromRow, fromCol, toRow, toCol, isCapture) {
    const files = 'abcdefgh';
    const ranks = '87654321';
    const from = files[fromCol] + ranks[fromRow];
    const to = files[toCol] + ranks[toRow];
    const symbol = pieceSymbols[type] || '';
    const capture = isCapture ? 'x' : '';
    const moveStr = `${symbol}${from}${capture}${to}`;
    moveHistory.push(moveStr);

    const movesList = document.getElementById('moves-list');
    const moveEl = document.createElement('div');
    moveEl.className = 'move-item';
    moveEl.textContent = `${Math.floor((moveHistory.length + 1) / 2)}. ${moveStr}`;
    movesList.appendChild(moveEl);
    movesList.scrollTop = movesList.scrollHeight;
}


function findKing(color) {
    const king = color === 'white' ? 'w-king' : 'b-king';
    for (let i = 0; i < 8; i++) {
        for (let j = 0; j < 8; j++) {
            if (board[i][j] === king) return { row: i, col: j };
        }
    }
    return null;
}

function isInCheck(color) {
    const kingPos = findKing(color);
    if (!kingPos) return false;

    const opponent = color === 'white' ? 'black' : 'white';
    for (let i = 0; i < 8; i++) {
        for (let j = 0; j < 8; j++) {
            const piece = board[i][j];
            if (piece && piece.startsWith(opponent === 'white' ? 'w' : 'b')) {
                const moves = calculatePossibleMoves(i, j);
                if (moves.some(m => m.row === kingPos.row && m.col === kingPos.col)) {
                    return true;
                }
            }
        }
    }
    return false;
}

function isCheckmate(player) {
    for (let i = 0; i < 8; i++) {
        for (let j = 0; j < 8; j++) {
            const piece = board[i][j];
            if (piece && piece.startsWith(player === 'white' ? 'w' : 'b')) {
                const moves = calculatePossibleMoves(i, j);
                for (const move of moves) {
                    const temp = board[move.row][move.col];
                    board[move.row][move.col] = board[i][j];
                    board[i][j] = null;

                    const inCheck = isInCheck(player);
                    board[i][j] = board[move.row][move.col];
                    board[move.row][move.col] = temp;

                    if (!inCheck) return false;
                }
            }
        }
    }
    return true;
}

function isStalemate(player) {
    if (isInCheck(player)) return false;
    return isCheckmate(player);
}


function makeAiMove() {
    const aiPieces = [];
    for (let i = 0; i < 8; i++) {
        for (let j = 0; j < 8; j++) {
            const piece = board[i][j];
            if (piece && piece.startsWith(aiColor === 'white' ? 'w' : 'b')) {
                aiPieces.push({ row: i, col: j });
            }
        }
    }

    
    const candidates = [];
    for (const piece of aiPieces) {
        const moves = calculatePossibleMoves(piece.row, piece.col);
        for (const move of moves) {
            candidates.push({ from: piece, to: move });
        }
    }

    if (candidates.length === 0) {
        if (isCheckmate(aiColor)) alert('Şah mat! Kazandınız!');
        else alert('PAT!');
        gameActive = false;
        return;
    }

    
    const move = candidates[Math.floor(Math.random() * candidates.length)];
    const { from, to } = move;
    const piece = board[from.row][from.col];
    const pieceType = piece.split('-')[1];
    const isCapture = board[to.row][to.col] !== null;

    movePiece(from.row, from.col, to.row, to.col);
    recordMove(pieceType, from.row, from.col, to.row, to.col, isCapture);

    if (isInCheck(currentPlayer)) {
        if (isCheckmate(currentPlayer)) {
            setTimeout(() => {
                alert('Bilgisayar kazandı!');
                gameActive = false;
            }, 100);
        }
    }

    switchPlayer();
}


function calculatePossibleMoves(row, col) {
    const piece = board[row][col];
    if (!piece) return [];

    const moves = [];
    const type = piece.split('-')[1];

    if (type === 'pawn') {
        const direction = piece.startsWith('w') ? -1 : 1;
        const startRow = piece.startsWith('w') ? 6 : 1;

        let r = row + direction;
        if (r >= 0 && r < 8 && !board[r][col]) {
            moves.push({ row: r, col: col });
            if (row === startRow) {
                r = row + 2 * direction;
                if (r >= 0 && r < 8 && !board[r][col]) {
                    moves.push({ row: r, col: col });
                }
            }
        }

        for (const dc of [-1, 1]) {
            r = row + direction;
            const c = col + dc;
            if (r >= 0 && r < 8 && c >= 0 && c < 8) {
                const target = board[r][c];
                if (target && !target.startsWith(piece[0])) {
                    moves.push({ row: r, col: c });
                }
            }
        }
    }
    else if (type === 'rook') {
        addLinearMoves(row, col, [[-1,0], [1,0], [0,-1], [0,1]], moves);
    }
    else if (type === 'bishop') {
        addLinearMoves(row, col, [[-1,-1], [-1,1], [1,-1], [1,1]], moves);
    }
    else if (type === 'queen') {
        addLinearMoves(row, col, [[-1,-1], [-1,0], [-1,1], [0,-1], [0,1], [1,-1], [1,0], [1,1]], moves);
    }
    else if (type === 'king') {
        const deltas = [[-1,-1], [-1,0], [-1,1], [0,-1], [0,1], [1,-1], [1,0], [1,1]];
        for (const [dx, dy] of deltas) {
            const r = row + dx;
            const c = col + dy;
            if (r >= 0 && r < 8 && c >= 0 && c < 8) {
                const target = board[r][c];
                if (!target || !target.startsWith(piece[0])) {
                    moves.push({ row: r, col: c });
                }
            }
        }
    }
    else if (type === 'knight') {
        const knightMoves = [[-2,-1], [-2,1], [-1,-2], [-1,2], [1,-2], [1,2], [2,-1], [2,1]];
        for (const [dx, dy] of knightMoves) {
            const r = row + dx;
            const c = col + dy;
            if (r >= 0 && r < 8 && c >= 0 && c < 8) {
                const target = board[r][c];
                if (!target || !target.startsWith(piece[0])) {
                    moves.push({ row: r, col: c });
                }
            }
        }
    }

    return moves;
}

function addLinearMoves(row, col, directions, moves) {
    const piece = board[row][col];
    for (const [dx, dy] of directions) {
        let r = row + dx;
        let c = col + dy;
        while (r >= 0 && r < 8 && c >= 0 && c < 8) {
            const target = board[r][c];
            if (!target) {
                moves.push({ row: r, col: c });
            } else {
                if (!target.startsWith(piece[0])) {
                    moves.push({ row: r, col: c });
                }
                break;
            }
            r += dx;
            c += dy;
        }
    }
}


function startNewGame() {
    board = initBoard();
    currentPlayer = 'white';
    gameActive = true;
    moveHistory = [];
    document.getElementById('moves-list').innerHTML = '';
    document.getElementById('turn-indicator').textContent = 'Sıra: Beyaz';
    clearSelection();
    drawBoard();
}


document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('main-menu').style.display = 'flex';
    document.getElementById('difficulty-select').value = currentDifficulty;
    document.getElementById('difficulty-badge').textContent = 'KOLAY';
});