
let board = [];
let solution = [];
let currentLevel = 'easy';
let selectedCell = null;


document.getElementById('play-btn').addEventListener('click', () => {
    document.getElementById('main-menu').style.display = 'none';
    document.getElementById('game-screen').style.display = 'flex';
    startNewGame(currentLevel);
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
    document.getElementById('main-menu').style.display = 'flex';
});

document.getElementById('difficulty-select').addEventListener('change', (e) => {
    currentLevel = e.target.value;
});

function generateSudoku(difficulty) {
    const emptyBoard = Array(9).fill().map(() => Array(9).fill(0));
    solveSudoku(emptyBoard);
    solution = JSON.parse(JSON.stringify(emptyBoard));
    
    const cellsToRemove = {
        easy: 40 + Math.floor(Math.random() * 5),
        medium: 50 + Math.floor(Math.random() * 5),
        hard: 60 + Math.floor(Math.random() * 5)
    };
    
    return removeCells(emptyBoard, cellsToRemove[difficulty]);
}

function solveSudoku(board) {
    const nums = [1,2,3,4,5,6,7,8,9];
    shuffleArray(nums);
    
    for (let i = 0; i < 9; i++) {
        for (let j = 0; j < 9; j++) {
            if (board[i][j] === 0) {
                for (const num of nums) {
                    if (isValid(board, i, j, num)) {
                        board[i][j] = num;
                        if (solveSudoku(board)) return true;
                        board[i][j] = 0;
                    }
                }
                return false;
            }
        }
    }
    return true;
}

function removeCells(board, count) {
    let removed = 0;
    while (removed < count) {
        const row = Math.floor(Math.random() * 9);
        const col = Math.floor(Math.random() * 9);
        if (board[row][col] !== 0) {
            board[row][col] = 0;
            removed++;
        }
    }
    return board;
}

function startNewGame(level) {
    board = generateSudoku(level);
    drawBoard();
}

function drawBoard() {
    const grid = document.getElementById('sudoku-grid');
    grid.innerHTML = '';
    
    for (let i = 0; i < 9; i++) {
        for (let j = 0; j < 9; j++) {
            const cell = document.createElement('div');
            cell.className = 'cell';
            if (board[i][j] !== 0) {
                cell.textContent = board[i][j];
                cell.classList.add('fixed');
            } else {
                cell.addEventListener('click', () => selectCell(cell, i, j));
            }
            grid.appendChild(cell);
        }
    }
}

function selectCell(cell, row, col) {
    if (selectedCell) selectedCell.classList.remove('selected');
    selectedCell = cell;
    cell.classList.add('selected');
    window.selectedRow = row;
    window.selectedCol = col;
}

document.addEventListener('keydown', (e) => {
    if (!selectedCell || selectedCell.classList.contains('fixed')) return;
    
    if (e.key >= '1' && e.key <= '9') {
        board[window.selectedRow][window.selectedCol] = parseInt(e.key);
        selectedCell.textContent = e.key;
    } 
    else if (e.key === 'Backspace' || e.key === '0') {
        board[window.selectedRow][window.selectedCol] = 0;
        selectedCell.textContent = '';
    }
});

function isValid(board, row, col, num) {
    for (let i = 0; i < 9; i++) {
        if (board[row][i] === num || board[i][col] === num || 
            board[3*Math.floor(row/3)+Math.floor(i/3)][3*Math.floor(col/3)+(i%3)] === num) {
            return false;
        }
    }
    return true;
}

function shuffleArray(array) {
    for (let i = array.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [array[i], array[j]] = [array[j], array[i]];
    }
}

document.getElementById('main-menu').style.display = 'flex';