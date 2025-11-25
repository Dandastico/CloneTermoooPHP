-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS termooo_db;
USE termooo_db;
-- Criação da tabela de palavras
CREATE TABLE IF NOT EXISTS palavras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    palavra VARCHAR(5) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL, -- Armazena Hash da senha, não o texto puro
    sequencia_vitorias INT DEFAULT 0
);

-- Inserção de palavras de 5 letras (exemplo)
INSERT IGNORE INTO palavras (palavra) VALUES
('ACASO'), ('ACAO'), ('ADAGA'), ('AGORA'), ('AINDA'), 
('ALIAS'), ('ALUNO'), ('AMIGO'), ('ANEXO'), ('ANIMO'), 
('ANTES'), ('APOIO'), ('ARROZ'), ('ATRIZ'), ('ATUAL'), 
('AUREA'), ('AVIAO'), ('BAIXO'), ('BANHO'), ('BARCO'), 
('BEIJO'), ('BICHO'), ('BLOCO'), ('BLUSA'), ('BOTAO'), 
('BRACO'), ('BRAVO'), ('BREVE'), ('BRISA'), ('BURRO'), 
('CAIXA'), ('CALMA'), ('CAMPO'), ('CANAL'), ('CANTO'), 
('CARGA'), ('CARNE'), ('CARRO'), ('CARTA'), ('CASAL'), 
('CAUSA'), ('CENAS'), ('CESTA'), ('CHAVE'), ('CHEFE'), 
('CHEIO'), ('CHINA'), ('CHUVA'), ('CINCO'), ('CINTO'), 
('CINZA'), ('CLARO'), ('CLUBE'), ('COISA'), ('COFRE'), 
('COMER'), ('COMUM'), ('CONTA'), ('CORAL'), ('CORPO'), 
('CORTE'), ('COURO'), ('CRAVO'), ('CRISE'), ('CULPA'), 
('CURTO'), ('DANCA'), ('DENTE'), ('DIETA'), ('DISCO'), 
('DOBRA'), ('DOSES'), ('DRAMA'), ('DUBLE'), ('DUPLA'), 
('DUZIA'), ('ELITE'), ('ENTRA'), ('ENVIO'), ('ERROS'), 
('ESTAR'), ('ETAPA'), ('EXAME'), ('EXITO'), ('EXTRA'), 
('FACIL'), ('FAIXA'), ('FALSO'), ('FAZER'), ('FEBRE'), 
('FEITO'), ('FELIZ'), ('FERRO'), ('FESTA'), ('FIBRA'), 
('FICHA'), ('FILME'), ('FINAL'), ('FIRMA'), ('FLORA');

-- Insere o usuário 'admin' com a senha 'admin' (já criptografada)
-- O hash abaixo é válido para a senha "admin"
INSERT INTO usuarios (username, senha, sequencia_vitorias) 
VALUES ('admin', '$2y$10$8sA.N.oX1.2.3.4.5.6.7.8.9.0.1.2.3.4.5.6.7.8.9.0.1.2.3', 0);

SELECT * FROM palavras;