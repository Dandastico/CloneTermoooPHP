-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS termooo_db;
USE termooo_db;
-- Criação da tabela de palavras
CREATE TABLE IF NOT EXISTS palavras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    palavra VARCHAR(5) NOT NULL UNIQUE
);

-- Inserção de palavras de 5 letras (exemplo)
INSERT INTO palavras (palavra) VALUES
('SAGAZ'),
('NOBRE'),
('AFETO'),
('PLENA'),
('TENUE'),
('AUDAC'),
('IDEIA'),
('VIVER'),
('CORPO'),
('TEMPO'),
('RAZAO'),
('CRIAR'),
('FALAR'),
('PENSE'),
('LUGAR'),
('VALOR'),
('MUNDO'),
('FORTE'),
('CLARO');

SELECT * FROM palavras;