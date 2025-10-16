-- Todas as tabelas do projeto.

CREATE TABLE IF NOT EXISTS usuarios (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(120) NOT NULL,
  sobrenome VARCHAR(120) NOT NULL,
  email VARCHAR(160) NOT NULL UNIQUE,
  celular VARCHAR(20) DEFAULT NULL,
  senha_hash VARCHAR(255) NOT NULL,
  data_nascimento DATE DEFAULT NULL,
  criado_em DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX idx_usuarios_email ON usuarios (email);


CREATE TABLE IF NOT EXISTS comandas (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  data_hora DATETIME NOT NULL,
  veiculo VARCHAR(20) DEFAULT NULL,
  servico VARCHAR(255) DEFAULT NULL,
  categoria VARCHAR(50) DEFAULT NULL,
  valor DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  pagamento VARCHAR(30) DEFAULT NULL,
  situacao ENUM('andamento','finalizada') NOT NULL DEFAULT 'andamento',
  obs TEXT DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX idx_comandas_data      ON comandas (data_hora);
CREATE INDEX idx_comandas_situacao  ON comandas (situacao);
CREATE INDEX idx_comandas_veiculo   ON comandas (veiculo);
CREATE INDEX idx_comandas_categoria ON comandas (categoria);

CREATE TABLE IF NOT EXISTS despesas (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  data DATE NOT NULL,
  descricao VARCHAR(255) NOT NULL,
  valor DECIMAL(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX idx_despesas_data ON despesas (data);
