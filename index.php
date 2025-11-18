<?php
// Inclui a configuração do banco de dados e inicialização da sessão
require_once 'db_config.php';

// Lógica de processamento do palpite
if (isset($_POST['palpite']) && !$_SESSION['jogo_terminado']) {
    $palpite = strtoupper(trim($_POST['palpite']));
    $palavra_secreta = $_SESSION['palavra_secreta'];
    $tentativas_restantes = $_SESSION['tentativas_restantes'];
    $mensagem = '';

    // 1. Validação do palpite
    if (strlen($palpite) !== 5) {
        $mensagem = "O palpite deve ter exatamente 5 letras.";
    } else if (!ctype_alpha($palpite)) {
        $mensagem = "O palpite deve conter apenas letras.";
    } else {
        // 2. Processamento do palpite
        $resultado_palpite = [];
        $palavra_secreta = $_SESSION['palavra_secreta'];
        $palavra_secreta_array = str_split($palavra_secreta);
        $palpite_array = str_split($palpite);
        
        // Array temporário para rastrear letras restantes na palavra secreta
        $palavra_secreta_temp = $palavra_secreta_array;

        // 1. Inicializa o array de resultado com as letras do palpite
        for ($i = 0; $i < 5; $i++) {
            $resultado_palpite[$i] = ['letra' => $palpite_array[$i], 'status' => 'incorreta'];
        }

        // 2. Primeira Passagem: Encontrar letras CORRETAS na posição correta (Verde)
        for ($i = 0; $i < 5; $i++) {
            if ($palpite_array[$i] === $palavra_secreta_array[$i]) {
                $resultado_palpite[$i]['status'] = 'correta';
                // Marca a letra como usada para não ser contada como 'posicao_errada'
                $palavra_secreta_temp[$i] = null; 
            }
        }

        // 3. Segunda Passagem: Encontrar letras CORRETAS na posição errada (Amarelo)
        for ($i = 0; $i < 5; $i++) {
            // Se a letra ainda NÃO foi marcada como 'correta' (Verde)
            if ($resultado_palpite[$i]['status'] !== 'correta') {
                $letra = $palpite_array[$i];
                $posicao = array_search($letra, $palavra_secreta_temp);

                if ($posicao !== false) {
                    $resultado_palpite[$i]['status'] = 'posicao_errada';
                    // Marca a letra como usada
                    $palavra_secreta_temp[$posicao] = null; 
                }
            }
        }

        // Não é mais necessário reindexar, pois os índices (0 a 4) sempre foram usados.
        // $resultado_palpite = array_values($resultado_palpite); // REMOVA ESSA LINHA

        // 4. Atualização do estado do jogo
        $_SESSION['tentativas'][] = $resultado_palpite;
        $_SESSION['tentativas_restantes']--;
// ... (o restante do código continua igual)

        // 4. Checagem de vitória
        if ($palpite === $palavra_secreta) {
            $_SESSION['jogo_terminado'] = true;
            $_SESSION['vitoria'] = true;
            $mensagem = "Parabéns! Você acertou a palavra!";
        } else if ($_SESSION['tentativas_restantes'] === 0) {
            $_SESSION['jogo_terminado'] = true;
            $mensagem = "Fim de jogo! A palavra era: " . $palavra_secreta;
        }
    }
}

// Lógica para reiniciar o jogo (já está em db_config.php, mas o formulário está aqui)
// ...

// HTML da página (será completado na próxima fase)
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Termooo Clone - PHP/MySQL</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Termooo Clone</h1>

        <?php if (isset($mensagem) && $mensagem): ?>
            <p class="mensagem <?php echo $_SESSION['jogo_terminado'] ? ($_SESSION['vitoria'] ? 'vitoria' : 'derrota') : 'alerta'; ?>">
                <?php echo htmlspecialchars($mensagem); ?>
            </p>
        <?php endif; ?>

        <div class="tabuleiro">
            <?php
            // Exibe as tentativas anteriores
            foreach ($_SESSION['tentativas'] as $tentativa) {
                echo '<div class="linha">';
                foreach ($tentativa as $letra_info) {
                    // Usa o status para aplicar a classe CSS
                    echo '<div class="celula ' . htmlspecialchars($letra_info['status']) . '">';
                    echo htmlspecialchars($letra_info['letra']);
                    echo '</div>';
                }
                echo '</div>';
            }

            // Exibe as linhas vazias restantes
            $linhas_vazias = $_SESSION['tentativas_restantes'];
            for ($i = 0; $i < $linhas_vazias; $i++) {
                echo '<div class="linha">';
                for ($j = 0; $j < 5; $j++) {
                    echo '<div class="celula"></div>';
                }
                echo '</div>';
            }
            ?>
        </div>

        <?php if (!$_SESSION['jogo_terminado']): ?>
            <form method="POST" action="index.php" class="form-palpite">
                <input type="text" name="palpite" maxlength="5" pattern="[a-zA-Z]{5}" required 
                       placeholder="Seu palpite (5 letras)" autofocus>
                <button type="submit">Tentar</button>
            </form>
        <?php endif; ?>

        <p class="info">Tentativas restantes: <?php echo $_SESSION['tentativas_restantes']; ?></p>

        <?php if ($_SESSION['jogo_terminado']): ?>
            <form method="POST" action="index.php" class="form-novo-jogo">
                <input type="hidden" name="novo_jogo" value="1">
                <button type="submit">Novo Jogo</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>