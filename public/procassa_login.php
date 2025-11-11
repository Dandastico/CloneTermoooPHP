<?php
// processa_login.php

// 1. Inicia a sessão (ESSENCIAL para manter o usuário logado)
// Deve ser a primeira coisa no script
session_start();

// 2. Inclui a conexão com o banco
require '/../src/db_config.php';

// 3. Define o tipo de resposta como JSON
header('Content-Type: application/json');

// 4. Prepara a resposta padrão
$response = ['success' => false, 'message' => 'Requisição inválida.'];

// 5. Verifica se o método é POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Pega os dados JSON enviados pelo JavaScript
    $data = json_decode(file_get_contents('php://input'), true);

    // Validação básica
    if (empty($data['loginUser']) || empty($data['loginPass'])) {
        $response['message'] = 'Usuário/Email e senha são obrigatórios.';
        echo json_encode($response);
        exit;
    }

    $loginUser = trim($data['loginUser']); // Pode ser username ou email
    $password = $data['loginPass'];

    try {
        // 6. Prepara a query (Seguro contra SQL Injection)
        // O login funciona tanto com 'username' quanto com 'email'
        $sql = "SELECT id, username, senha_hash FROM usuarios WHERE username = :login OR email = :login LIMIT 1";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['login' => $loginUser]);
        
        $user = $stmt->fetch();

        // 7. Verifica se o usuário existe E se a senha está correta
        if ($user && password_verify($password, $user['senha_hash'])) {
            
            // SUCESSO!
            
            // 8. Regenera o ID da sessão para evitar "Session Fixation"
            session_regenerate_id(true);
            
            // 9. Armazena os dados do usuário na sessão
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['logged_in'] = true;

            $response['success'] = true;
            $response['message'] = 'Login bem-sucedido!';

        } else {
            // Falha (usuário não encontrado ou senha errada)
            // Usamos uma mensagem genérica por segurança
            $response['message'] = 'Usuário ou senha inválidos.';
        }

    } catch (PDOException $e) {
        // Loga o erro e envia uma resposta genérica
        error_log($e->getMessage());
        $response['message'] = 'Erro no servidor. Tente novamente mais tarde.';
    }
}

// 10. Retorna a resposta (seja sucesso ou falha) como JSON
echo json_encode($response);
?>