CREATE DATABASE db_pweb1_elvi;
USE db_pweb1_elvi;

-- Tabela de Usuários Administrativos
CREATE TABLE usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    login VARCHAR(50) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL
);

-- Inserção do Usuário Padrão
INSERT INTO usuario (nome, telefone, email, login, senha) 
VALUES ('Administrador', '00000000000', 'admin@elvi.com', 'admin', '123');

-- Tabela de Produtos (Roupas/Estoque)
CREATE TABLE produto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_peca VARCHAR(100) NOT NULL,
    tamanho VARCHAR(10) NOT NULL,
    cor_predominante VARCHAR(50) NOT NULL,
    preco_venda DECIMAL(10,2) NOT NULL,
    imagem VARCHAR(255) NULL -- Armazena o caminho ou nome do arquivo de imagem do upload
);

-- Tabela de Fornecedores Parceiros
CREATE TABLE fornecedor (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_empresa VARCHAR(150) NOT NULL,
    cnpj VARCHAR(18) NOT NULL UNIQUE,
    prazo_entrega_dias INT NOT NULL,
    telefone_contato VARCHAR(20) NOT NULL
);

-- Tabela de Vendas (Modificada com o Relacionamento)
CREATE TABLE venda (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT NOT NULL, -- ALTERAÇÃO: Coluna adicionada para vincular o produto vendido
    data_compra DATE NOT NULL,
    forma_pagamento VARCHAR(50) NOT NULL,
    valor_total DECIMAL(10,2) NOT NULL,
    status_pedido VARCHAR(30) NOT NULL,
    
    -- ALTERAÇÃO: Cria o elo de ligação (Chave Estrangeira) apontando para a tabela produto
    CONSTRAINT fk_venda_produto FOREIGN KEY (produto_id) REFERENCES produto(id)
);