CREATE DATABASE IF NOT EXISTS restaurante
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE restaurante;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS clientes;
CREATE TABLE clientes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  endereco VARCHAR(150) NULL,
  telefone VARCHAR(20) NULL,
  email VARCHAR(100) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO clientes (nome, endereco, telefone, email)
VALUES ('Cliente de Balcão', 'Não informado', NULL, NULL);

DROP TABLE IF EXISTS fornecedores;
CREATE TABLE fornecedores (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  cnpj VARCHAR(18) NULL,
  endereco VARCHAR(150) NULL,
  telefone VARCHAR(20) NULL,
  email VARCHAR(100) NULL,
  UNIQUE KEY uq_fornecedor_cnpj (cnpj)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS ingredientes;
CREATE TABLE ingredientes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  unidade VARCHAR(20) NOT NULL,
  preco_unitario DECIMAL(12,2) NOT NULL DEFAULT 0,
  estoque DECIMAL(12,3) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS pratos;
CREATE TABLE pratos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  preco_unitario DECIMAL(12,2) NOT NULL DEFAULT 0,
  imagem VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS composicao;
CREATE TABLE composicao (
  id INT AUTO_INCREMENT PRIMARY KEY,
  prato_id INT NOT NULL,
  ingrediente_id INT NOT NULL,
  quantidade DECIMAL(12,3) NOT NULL DEFAULT 0,
  CONSTRAINT uq_comp UNIQUE (prato_id, ingrediente_id),
  INDEX idx_comp_prato (prato_id),
  INDEX idx_comp_ingrediente (ingrediente_id),
  CONSTRAINT fk_comp_prato FOREIGN KEY (prato_id) REFERENCES pratos(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_comp_ingrediente FOREIGN KEY (ingrediente_id) REFERENCES ingredientes(id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS compras;
CREATE TABLE compras (
  id INT AUTO_INCREMENT PRIMARY KEY,
  fornecedor_id INT NOT NULL,
  data_compra DATE NOT NULL,
  nota_fiscal VARCHAR(50) NULL,
  valor_total DECIMAL(12,2) NOT NULL DEFAULT 0,
  INDEX idx_compra_fornecedor (fornecedor_id),
  CONSTRAINT fk_compra_fornecedor FOREIGN KEY (fornecedor_id) REFERENCES fornecedores(id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS itens_compra;
CREATE TABLE itens_compra (
  id INT AUTO_INCREMENT PRIMARY KEY,
  compra_id INT NOT NULL,
  ingrediente_id INT NOT NULL,
  quantidade DECIMAL(12,3) NOT NULL,
  preco_unitario DECIMAL(12,2) NOT NULL,
  INDEX idx_ic_compra (compra_id),
  INDEX idx_ic_ingrediente (ingrediente_id),
  CONSTRAINT fk_ic_compra FOREIGN KEY (compra_id) REFERENCES compras(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_ic_ingrediente FOREIGN KEY (ingrediente_id) REFERENCES ingredientes(id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS encomendas;
CREATE TABLE encomendas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cliente_id INT NULL,
  data_encomenda DATE NOT NULL,
  nome_cliente VARCHAR(100) NULL,
  endereco_cliente VARCHAR(150) NULL,
  telefone_cliente VARCHAR(20) NULL,
  INDEX idx_enc_cliente (cliente_id),
  CONSTRAINT fk_enc_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS itens_encomenda;
CREATE TABLE itens_encomenda (
  id INT AUTO_INCREMENT PRIMARY KEY,
  encomenda_id INT NOT NULL,
  prato_id INT NOT NULL,
  quantidade DECIMAL(12,3) NOT NULL,
  preco_unitario DECIMAL(12,2) NOT NULL,
  INDEX idx_ie_encomenda (encomenda_id),
  INDEX idx_ie_prato (prato_id),
  CONSTRAINT fk_ie_encomenda FOREIGN KEY (encomenda_id) REFERENCES encomendas(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_ie_prato FOREIGN KEY (prato_id) REFERENCES pratos(id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
