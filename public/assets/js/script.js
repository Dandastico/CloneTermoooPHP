// Lista de palavras (para simplificar, no Termooo real isso viria de um backend)
const WORD_LIST = [
    "CLARO", "MUNDO", "TEMPO", "IDEIA", "GENTE", "FOTOS", "LEGAL", "COBRA", "PEDRA", "PRAIA"
];
const CORRECT_WORD = WORD_LIST[Math.floor(Math.random() * WORD_LIST.length)];
const WORD_LENGTH = 5;
const MAX_TRIES = 6;

let currentGuess = "";
let currentRow = 0;
let gameOver = false;

document.addEventListener('DOMContentLoaded', () => {
    setupBoard();
    setupKeyboard();
    document.addEventListener('keyup', handleKeyPress);
});

// --- Configuração da Interface ---

function setupBoard() {
    const board = document.getElementById('board');
    for (let i = 0; i < MAX_TRIES; i++) {
        const row = document.createElement('div');
        row.className = 'word-row';
        for (let j = 0; j < WORD_LENGTH; j++) {
            const tile = document.createElement('div');
            tile.className = 'tile';
            row.appendChild(tile);
        }
        board.appendChild(row);
    }
}

function setupKeyboard() {
    const keyboardLayout = [
        ['Q', 'W', 'E', 'R', 'T', 'Y', 'U', 'I', 'O', 'P'],
        ['A', 'S', 'D', 'F', 'G', 'H', 'J', 'K', 'L'],
        ['ENTER', 'Z', 'X', 'C', 'V', 'B', 'N', 'M', 'BACKSPACE']
    ];
    const keyboard = document.getElementById('keyboard');

    keyboardLayout.forEach(rowKeys => {
        const row = document.createElement('div');
        row.className = 'key-row';
        rowKeys.forEach(keyText => {
            const key = document.createElement('div');
            key.className = 'key';
            key.textContent = keyText;
            key.id = `key-${keyText}`;
            if (keyText === 'ENTER' || keyText === 'BACKSPACE') {
                key.classList.add('large');
            }
            key.addEventListener('click', () => handleKeyClick(keyText));
            row.appendChild(key);
        });
        keyboard.appendChild(row);
    });
}

// --- Lógica de Entrada ---

function handleKeyPress(event) {
    if (gameOver) return;

    const key = event.key.toUpperCase();

    if (key === 'ENTER') {
        checkGuess();
        return;
    }

    if (key === 'BACKSPACE' || key === 'DELETE') {
        deleteLetter();
        return;
    }

    if (key.length === 1 && key.match(/[A-ZÇÉÍÓÚÃÕÊÎÔÛÀÈÌÒÙÄËÏÖÜ]/) && currentGuess.length < WORD_LENGTH) {
        // Aceita letras do alfabeto (com alguns acentos comuns em PT para o visual)
        addLetter(key);
    }
}

function handleKeyClick(keyText) {
    if (gameOver) return;

    if (keyText === 'ENTER') {
        checkGuess();
        return;
    }

    if (keyText === 'BACKSPACE') {
        deleteLetter();
        return;
    }

    if (keyText.length === 1 && keyText.match(/[A-Z]/) && currentGuess.length < WORD_LENGTH) {
        addLetter(keyText);
    }
}

function addLetter(letter) {
    currentGuess += letter;
    updateBoard();
}

function deleteLetter() {
    currentGuess = currentGuess.slice(0, -1);
    updateBoard();
}

function updateBoard() {
    const row = document.getElementById('board').children[currentRow];
    // Limpa a linha
    Array.from(row.children).forEach(tile => tile.textContent = '');

    // Preenche com a tentativa atual
    for (let i = 0; i < currentGuess.length; i++) {
        row.children[i].textContent = currentGuess[i];
    }
}

// --- Lógica do Jogo ---

function checkGuess() {
    if (currentGuess.length !== WORD_LENGTH) {
        showMessage("A palavra deve ter 5 letras!", 2000);
        return;
    }

    // Nota: Em uma versão real, você checaria se a palavra existe no dicionário.
    // if (!WORD_LIST.includes(currentGuess)) {
    //     showMessage("Palavra não reconhecida!", 2000);
    //     return;
    // }

    const guess = currentGuess;
    const word = CORRECT_WORD;
    const results = Array(WORD_LENGTH).fill(null);
    let tempWord = word.split('');

    // 1. Encontrar acertos (corretos e presentes)
    // 1a. Encontrar posições COLETAS
    for (let i = 0; i < WORD_LENGTH; i++) {
        if (guess[i] === word[i]) {
            results[i] = 'correct';
            tempWord[i] = null; // Marca a letra como usada
        }
    }

    // 1b. Encontrar letras PRESENTES
    for (let i = 0; i < WORD_LENGTH; i++) {
        if (results[i] === null) { // Se não for correta...
            const index = tempWord.findIndex(char => char === guess[i]);
            if (index !== -1) {
                results[i] = 'present';
                tempWord[index] = null; // Marca a letra como usada
            } else {
                results[i] = 'absent';
            }
        }
    }

    // 2. Aplicar o feedback visual
    applyFeedback(guess, results);

    // 3. Checar o fim do jogo
    if (guess === word) {
        showMessage("Parabéns! Você acertou!", 5000);
        gameOver = true;
    } else if (currentRow >= MAX_TRIES - 1) {
        showMessage(`Fim de jogo. A palavra era: ${CORRECT_WORD}`, 8000);
        gameOver = true;
    } else {
        // Próxima rodada
        currentRow++;
        currentGuess = "";
    }
}

function applyFeedback(guess, results) {
    const row = document.getElementById('board').children[currentRow];

    for (let i = 0; i < WORD_LENGTH; i++) {
        const tile = row.children[i];
        const keyElement = document.getElementById(`key-${guess[i]}`);
        
        // Aplica o estilo na telha
        setTimeout(() => {
            tile.classList.add(results[i]);
            // Atualiza o estilo do teclado
            if (keyElement) {
                // Prioriza "correct" > "present" > "absent"
                if (keyElement.classList.contains('correct')) {
                    // Não faz nada, já está no estado de maior prioridade
                } else if (results[i] === 'correct') {
                    keyElement.className = 'key correct';
                } else if (results[i] === 'present' && !keyElement.classList.contains('present')) {
                    keyElement.className = 'key present';
                } else if (results[i] === 'absent' && !keyElement.classList.contains('present')) {
                    keyElement.classList.add('absent');
                }
            }
        }, i * 300); // Pequeno delay para efeito visual
    }
}

function showMessage(message, duration = 3000) {
    const messageContainer = document.getElementById('message-container');
    messageContainer.textContent = message;
    messageContainer.classList.add('show-message');

    setTimeout(() => {
        messageContainer.classList.remove('show-message');
    }, duration);
}