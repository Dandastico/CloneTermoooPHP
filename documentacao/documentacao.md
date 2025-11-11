# Clone do Termooo utilizando PHP
## 1.1 Objetivo do Sistema
O sistema é uma cópia do jogo Termooo (versão mais famosa desse tipo de jogo em português) ou Wordle (versão mais famosa em inglês, desenvolvido pelo NY Times). O site deve permitir a criação de novos usuários, log-in e gerenciamento do usuário (edição e deleção).

O jogo terá as mesmas regras de negócio do jogo Termooo:
- O jogo irá escolher uma palavra por dia para ser o "alvo"
- O jogador pode tentar acertar o "alvo" 6 vezes com palavras de 5 letras
- O site mostrará o resultado da comparação do "chute" com o "alvo" utilizando cores:
  - Verde: letra certa na posição certa
  - Amarela: letra certa na posição errada
  - Preto: letra errada
- O jogador pode criar uma conta
- O jogador pode efetuar log-in
  - Quando o jogador efetua o log-in, ele pode ter um histórico de seus jogos

## Público-alvo
O sistema é destinado a jogadores casuais de jogos eletrônicos.

## Wireframes
![tela1](./imagens/tela1.png)
![tela2](./imagens/tela2.png)
![tela3](./imagens/tela3.png)
![tela4](./imagens/tela4.png)

## Arquitetura do Banco de Dados
Tabelas dos jogos
```sql

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(254) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL
);

CREATE TABLE palavras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    palavra VARCHAR(5) UNIQUE,
    dificuldade ENUM('facil', 'medio', 'dificil')
);

CREATE TABLE jogos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    palavra_id INT,  -- referência à palavra secreta
    tentativas INT DEFAULT 0,
    max_tentativas INT DEFAULT 6,  -- limite de tentativa
    venceu BOOLEAN DEFAULT FALSE,
    data_inicio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_fim TIMESTAMP NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL, -- se usuário for deletado, permite manter o jogo
    FOREIGN KEY (palavra_id) REFERENCES palavras(id)
);
```