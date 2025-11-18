<?php
// Configurações do Banco de Dados
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); // Altere para seu usuário
define('DB_PASSWORD', 'senac'); // Altere para sua senha
define('DB_NAME', 'termooo_db');

// Conexão com o Banco de Dados
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Checar conexão
if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}

// Configurar charset para UTF-8
mb_internal_encoding("UTF-8");
$conn->set_charset("utf8mb4");

// Iniciar sessão para armazenar o estado do jogo
session_start();

/**
 * Função para selecionar uma palavra aleatória do banco de dados.
 * @param mysqli $conn A conexão com o banco de dados.
 * @return string A palavra secreta em maiúsculas.
 */
function selecionarPalavraSecreta($conn) {
    // Consulta para selecionar uma palavra aleatória
    $sql = "SELECT palavra FROM palavras ORDER BY RAND() LIMIT 1";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return strtoupper($row['palavra']);
    } else {
        // Retorna uma palavra padrão em caso de falha
        return 'MANUS'; 
    }
}

/**
 * Função para iniciar um novo jogo.
 * @param mysqli $conn A conexão com o banco de dados.
 */
function iniciarNovoJogo($conn) {
    // Seleciona a palavra secreta
    $palavra_secreta = selecionarPalavraSecreta($conn);
    
    // Armazena o estado do jogo na sessão
    $_SESSION['palavra_secreta'] = $palavra_secreta;
    $_SESSION['tentativas'] = [];
    $_SESSION['tentativas_restantes'] = 6;
    $_SESSION['jogo_terminado'] = false;
    $_SESSION['vitoria'] = false;
}

// Inicia o jogo se a sessão não estiver configurada
if (!isset($_SESSION['palavra_secreta'])) {
    iniciarNovoJogo($conn);
}

// Se o usuário clicar em "Novo Jogo"
if (isset($_POST['novo_jogo'])) {
    iniciarNovoJogo($conn);
    // Redireciona para evitar reenvio do formulário
    header("Location: index.php");
    exit();
}
?>