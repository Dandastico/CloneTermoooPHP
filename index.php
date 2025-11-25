<?php
require_once 'db_config.php';

$mensagem_erro = '';
$mensagem_sucesso = '';

// --- ROTEAMENTO DE AÇÕES (POST) ---

// 1. Ação de Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

// 2. Ação de Navegação via Botões
if (isset($_POST['ir_para'])) {
    $_SESSION['tela_atual'] = $_POST['ir_para'];
    if ($_POST['ir_para'] == 'jogo' && !isset($_SESSION['palavra_secreta'])) {
        iniciarNovoJogo($conn);
    }
}

// 3. Ação de Cadastro
if (isset($_POST['acao_cadastrar'])) {
    $user = trim($_POST['username']);
    $pass = $_POST['password'];
    $pass_conf = $_POST['password_confirm'];

    if ($pass !== $pass_conf) {
        $mensagem_erro = "As senhas não conferem!";
    } elseif (strlen($user) < 3 || strlen($pass) < 4) {
        $mensagem_erro = "Usuário ou senha muito curtos.";
    } else {
        $res = cadastrarUsuario($conn, $user, $pass);
        if ($res === true) {
            $mensagem_sucesso = "Conta criada! Faça login.";
            $_SESSION['tela_atual'] = 'login';
        } else {
            $mensagem_erro = $res;
        }
    }
}

// 4. Ação de Login
if (isset($_POST['acao_login'])) {
    $user = trim($_POST['username']);
    $pass = $_POST['password'];
    
    $res = logarUsuario($conn, $user, $pass);
    if ($res === true) {
        iniciarNovoJogo($conn); // Começa um jogo ao logar
    } else {
        $mensagem_erro = $res;
    }
}

// 5. Lógica do Jogo (Processamento do Palpite)
if (isset($_POST['palpite']) && $_SESSION['tela_atual'] == 'jogo' && !$_SESSION['jogo_terminado']) {
    // ... (A lógica é idêntica à anterior, apenas adicionamos a atualização do BD no final)
    $palpite = strtoupper(trim($_POST['palpite']));
    
    // (Validações básicas mantidas)
    if (strlen($palpite) !== 5 || !ctype_alpha($palpite)) {
        $mensagem_erro = "Palpite inválido (apenas 5 letras).";
    } else {
        // Lógica de cores (resumida aqui para brevidade, mas mantenha a sua lógica completa de cores)
        $resultado_palpite = [];
        $palavra_secreta_arr = str_split($_SESSION['palavra_secreta']);
        $palpite_arr = str_split($palpite);
        $palavra_temp = $palavra_secreta_arr;

        // Inicializa
        for ($i=0; $i<5; $i++) $resultado_palpite[$i] = ['letra'=>$palpite_arr[$i], 'status'=>'incorreta'];

        // Verde
        for ($i=0; $i<5; $i++) {
            if ($palpite_arr[$i] === $palavra_secreta_arr[$i]) {
                $resultado_palpite[$i]['status'] = 'correta';
                $palavra_temp[$i] = null;
            }
        }
        // Amarelo
        for ($i=0; $i<5; $i++) {
            if ($resultado_palpite[$i]['status'] !== 'correta') {
                $pos = array_search($palpite_arr[$i], $palavra_temp);
                if ($pos !== false) {
                    $resultado_palpite[$i]['status'] = 'posicao_errada';
                    $palavra_temp[$pos] = null;
                }
            }
        }

        $_SESSION['tentativas'][] = $resultado_palpite;
        $_SESSION['tentativas_restantes']--;

        // Checagem de Vitória/Derrota
        if ($palpite === $_SESSION['palavra_secreta']) {
            $_SESSION['jogo_terminado'] = true;
            $_SESSION['vitoria'] = true;
            $mensagem_sucesso = "Parabéns! Você acertou!";
            
            // ATUALIZAR SEQUENCIA NO BANCO SE LOGADO
            if (isset($_SESSION['user_id'])) {
                atualizarSequencia($conn, $_SESSION['user_id'], true);
            }
        } else if ($_SESSION['tentativas_restantes'] === 0) {
            $_SESSION['jogo_terminado'] = true;
            $mensagem_erro = "Fim de jogo! A palavra era: " . $_SESSION['palavra_secreta'];
            
            // ZERAR SEQUENCIA NO BANCO SE LOGADO
            if (isset($_SESSION['user_id'])) {
                atualizarSequencia($conn, $_SESSION['user_id'], false);
            }
        }
    }
}

// Novo Jogo (Botão dentro do jogo)
if (isset($_POST['novo_jogo'])) {
    iniciarNovoJogo($conn);
    header("Location: index.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Termooo Clone</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="header-user">
                <span>Olá, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></span>
                <span>🔥 Sequência: <?php echo $_SESSION['sequencia_vitorias']; ?></span>
                <a href="index.php?logout=1" class="logout-btn">Sair</a>
            </div>
        <?php endif; ?>

        <h1>Termooo Clone</h1>

        <?php if ($mensagem_erro): ?>
            <p class="mensagem derrota"><?php echo htmlspecialchars($mensagem_erro); ?></p>
        <?php endif; ?>
        <?php if ($mensagem_sucesso): ?>
            <p class="mensagem vitoria"><?php echo htmlspecialchars($mensagem_sucesso); ?></p>
        <?php endif; ?>


        <?php if ($_SESSION['tela_atual'] == 'menu' && !isset($_SESSION['user_id'])): ?>
            
            <div class="menu-botoes">
                <form method="POST">
                    <input type="hidden" name="ir_para" value="jogo">
                    <button type="submit" class="btn-grande">Jogar Agora</button>
                </form>

                <form method="POST">
                    <input type="hidden" name="ir_para" value="cadastro">
                    <button type="submit" class="btn-grande btn-secundario">Criar Conta</button>
                </form>

                <form method="POST">
                    <input type="hidden" name="ir_para" value="login">
                    <button type="submit" class="btn-grande btn-secundario">Entrar</button>
                </form>
            </div>

        <?php elseif ($_SESSION['tela_atual'] == 'cadastro' && !isset($_SESSION['user_id'])): ?>
            
            <h3>Criar Conta</h3>
            <form method="POST" class="form-auth">
                <input type="hidden" name="acao_cadastrar" value="1">
                
                <label>Usuário</label>
                <input type="text" name="username" required>
                
                <label>Senha</label>
                <input type="password" name="password" required>
                
                <label>Confirmar Senha</label>
                <input type="password" name="password_confirm" required>
                
                <button type="submit" class="btn-grande">Cadastrar</button>
            </form>
            <form method="POST"><button type="submit" name="ir_para" value="menu" class="link-voltar">Voltar</button></form>

        <?php elseif ($_SESSION['tela_atual'] == 'login' && !isset($_SESSION['user_id'])): ?>
            
            <h3>Login</h3>
            <form method="POST" class="form-auth">
                <input type="hidden" name="acao_login" value="1">
                
                <label>Usuário</label>
                <input type="text" name="username" required>
                
                <label>Senha</label>
                <input type="password" name="password" required>
                
                <button type="submit" class="btn-grande">Entrar</button>
            </form>
            <form method="POST"><button type="submit" name="ir_para" value="menu" class="link-voltar">Voltar</button></form>

        <?php else: ?>
            
            <div class="tabuleiro">
                <?php
                // Exibe tentativas
                foreach ($_SESSION['tentativas'] as $tentativa) {
                    echo '<div class="linha">';
                    foreach ($tentativa as $l) {
                        echo '<div class="celula ' . $l['status'] . '">' . $l['letra'] . '</div>';
                    }
                    echo '</div>';
                }
                // Linhas vazias
                for ($i = 0; $i < $_SESSION['tentativas_restantes']; $i++) {
                    echo '<div class="linha">';
                    for ($j = 0; $j < 5; $j++) echo '<div class="celula"></div>';
                    echo '</div>';
                }
                ?>
            </div>

            <?php if (!$_SESSION['jogo_terminado']): ?>
                <form method="POST" class="form-palpite">
                    <input type="text" name="palpite" maxlength="5" required autofocus autocomplete="off">
                    <button type="submit">Tentar</button>
                </form>
            <?php else: ?>
                <form method="POST" class="form-novo-jogo">
                    <input type="hidden" name="novo_jogo" value="1">
                    <button type="submit">Novo Jogo</button>
                </form>
            <?php endif; ?>

            <?php if (!isset($_SESSION['user_id'])): ?>
                <form method="POST" style="margin-top:20px;">
                    <button type="submit" name="ir_para" value="menu" class="link-voltar">Voltar ao Menu</button>
                </form>
            <?php endif; ?>

        <?php endif; ?>
    </div>
</body>
</html>