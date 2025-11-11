<?php
// db_config.php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // <-- Seu usuário do MySQL
define('DB_PASS', '');           // <-- Sua senha do MySQL
define('DB_NAME', 'termooo_db'); // O banco de dados da sua documentação
define('DB_CHARSET', 'utf8mb4');

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // Em um ambiente de produção, você não deve exibir erros detalhados.
    // Você deve logar o erro e mostrar uma mensagem genérica.
    error_log($e->getMessage()); // Loga o erro no servidor
    
    // Resposta de erro genérica para o frontend (caso seja uma API)
    // header('Content-Type: application/json');
    // echo json_encode(['success' => false, 'message' => 'Erro de conexão com o banco de dados.']);
    
    // Ou uma página de erro
    die("Erro crítico de conexão com o banco de dados. Por favor, tente mais tarde.");
}
?>