<?php
// Configurações do Banco de Dados
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', ''); 
define('DB_NAME', 'termooo_db');

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

mb_internal_encoding("UTF-8");
$conn->set_charset("utf8mb4");

session_start();

// --- FUNÇÕES DO JOGO ---

function selecionarPalavraSecreta($conn) {
    $sql = "SELECT palavra FROM palavras ORDER BY RAND() LIMIT 1";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return strtoupper($row['palavra']);
    }
    return 'FEIOS'; 
}

function iniciarNovoJogo($conn) {
    $_SESSION['palavra_secreta'] = selecionarPalavraSecreta($conn);
    $_SESSION['tentativas'] = [];
    $_SESSION['tentativas_restantes'] = 6;
    $_SESSION['jogo_terminado'] = false;
    $_SESSION['vitoria'] = false;
    $_SESSION['tela_atual'] = 'jogo'; // Define que estamos na tela do jogo
}

// --- FUNÇÕES DE USUÁRIO E AUTENTICAÇÃO ---

function cadastrarUsuario($conn, $username, $senha) {
    // Verifica se usuário já existe
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    if ($stmt->fetch()) {
        return "Nome de usuário já existe.";
    }
    $stmt->close();

    // Cria o hash da senha (segurança)
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO usuarios (username, senha) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $senha_hash);
    
    if ($stmt->execute()) {
        return true;
    } else {
        return "Erro ao cadastrar.";
    }
}

function logarUsuario($conn, $username, $senha) {
    $stmt = $conn->prepare("SELECT id, username, senha, sequencia_vitorias FROM usuarios WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($senha, $row['senha'])) {
            // Login Sucesso
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['sequencia_vitorias'] = $row['sequencia_vitorias'];
            return true;
        }
    }
    return "Usuário ou senha incorretos.";
}

function atualizarSequencia($conn, $user_id, $ganhou) {
    if ($ganhou) {
        // Incrementa a sequência
        $sql = "UPDATE usuarios SET sequencia_vitorias = sequencia_vitorias + 1 WHERE id = ?";
        $_SESSION['sequencia_vitorias']++; // Atualiza na sessão também
    } else {
        // Zera a sequência
        $sql = "UPDATE usuarios SET sequencia_vitorias = 0 WHERE id = ?";
        $_SESSION['sequencia_vitorias'] = 0;
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
}

// Inicialização básica de navegação
if (!isset($_SESSION['tela_atual'])) {
    $_SESSION['tela_atual'] = 'menu';
}
?>