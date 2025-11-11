/* 1. Crie o banco de dados (se ainda não o fez) */
CREATE DATABASE IF NOT EXISTS termooo_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

/* 2. Use o banco */
USE termooo_db;

/* 3. Sua tabela 'usuarios' com a correção 'UNIQUE' */
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE, /* <-- Adicionado UNIQUE */
    email VARCHAR(254) NOT NULL UNIQUE, /* <-- Adicionado UNIQUE */
    senha_hash VARCHAR(255) NOT NULL
    /* A coluna data_criacao não estava no seu doc, mas é uma boa prática.
       Vou seguir estritamente o seu doc por enquanto. */
);

/* 4. Tabelas restantes da sua documentação (para contexto) */
CREATE TABLE IF NOT EXISTS palavras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    palavra VARCHAR(5) UNIQUE,
    dificuldade ENUM('facil', 'medio', 'dificil')
);

CREATE TABLE IF NOT EXISTS jogos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    palavra_id INT,
    tentativas INT DEFAULT 0,
    max_tentativas INT DEFAULT 6,
    venceu BOOLEAN DEFAULT FALSE,
    data_inicio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_fim TIMESTAMP NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL, /* Permite manter o jogo se o usuário for deletado */
    FOREIGN KEY (palavra_id) REFERENCES palavras(id)
);

/* 5. (Importante) Crie um usuário de teste */
/* A senha é 'senha123' */
INSERT INTO usuarios (username, email, senha_hash) 
VALUES ('demo', 'demo@exemplo.com', '$2y$10$T.M.2lCNvjP1E.nBq/hG/e.Lw.jE/Xh.mYKJg38Y6lVK98mUnvQem')
ON DUPLICATE KEY UPDATE username=username; /* Evita erro se o usuário 'demo' já existir */