-- SQL schema for ConectaSaude (MySQL/MariaDB)
-- Run: mysql -u root -p < database.sql

CREATE DATABASE IF NOT EXISTS conecta_saude DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE conecta_saude;

-- Users table (equivalent to usuarios.json)
CREATE TABLE IF NOT EXISTS users (
  id VARCHAR(64) PRIMARY KEY,
  nome VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  senha VARCHAR(255) NOT NULL,
  tipo ENUM('paciente','agente','medico','admin') NOT NULL DEFAULT 'paciente',
  status ENUM('pendente','aprovado','rejeitado') NOT NULL DEFAULT 'pendente',
  foto VARCHAR(255) DEFAULT 'default.png',
  data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Profiles table (optional, mirrors perfil_*.json files)
CREATE TABLE IF NOT EXISTS profiles (
  id VARCHAR(64) PRIMARY KEY,
  user_id VARCHAR(64),
  nome VARCHAR(255),
  email VARCHAR(255),
  tipo VARCHAR(50),
  telefone VARCHAR(50),
  data_nascimento DATE,
  cpf VARCHAR(32),
  endereco TEXT,
  cidade VARCHAR(128),
  estado VARCHAR(16),
  cep VARCHAR(32),
  foto VARCHAR(255),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Consultas (appointments)
CREATE TABLE IF NOT EXISTS consultas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  paciente_id VARCHAR(64) DEFAULT NULL,
  medico VARCHAR(255) DEFAULT NULL,
  especialidade VARCHAR(255) DEFAULT NULL,
  data DATETIME,
  tipo ENUM('presencial','online') DEFAULT 'presencial',
  status VARCHAR(64) DEFAULT 'agendada',
  motivo TEXT,
  data_agendamento DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (paciente_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Mensagens (messages/inbox)
CREATE TABLE IF NOT EXISTS mensagens (
  id INT AUTO_INCREMENT PRIMARY KEY,
  remetente VARCHAR(255),
  assunto VARCHAR(255),
  mensagem TEXT,
  data DATETIME DEFAULT CURRENT_TIMESTAMP,
  lida TINYINT(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Audit log for admin actions (append-only)
CREATE TABLE IF NOT EXISTS audit_log (
  id INT AUTO_INCREMENT PRIMARY KEY,
  evento VARCHAR(128) NOT NULL,
  detalhes TEXT,
  usuario VARCHAR(255),
  ip VARCHAR(45),
  criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample/seed data (from existing JSON files)
-- Admin user (pre-created)
INSERT INTO users (id, nome, email, senha, tipo, status, foto, data_cadastro) VALUES
('admin000000000', 'Administrador Conecta Sa\u00fade', 'admin@conectasaude.com', '$2y$10$GJGkuqEKRDHfq08QuPFoT.Nld9J480azRfmJqJNVMXh3O5XMKwZRu', 'admin', 'aprovado', 'default.png', '2026-06-13 00:00:00')
ON DUPLICATE KEY UPDATE nome=VALUES(nome);

-- Example medico
INSERT INTO users (id, nome, email, senha, tipo, status, foto, data_cadastro) VALUES
('6986235079794', 'miguel', 'miguel@gmail.com', '$2y$10$bQG5/nuawufghaM8UxbcCOMuTlq656XK0aPN7RVviiGkPiUZiiwY2', 'medico', 'aprovado', 'default.png', '2026-02-06 17:22:24')
ON DUPLICATE KEY UPDATE nome=VALUES(nome);

-- Example profile
INSERT INTO profiles (id, user_id, nome, email, tipo, telefone, data_nascimento, cpf, endereco, cidade, estado, cep, foto) VALUES
('6984c142d1b90', NULL, 'zoin zoiao', 'zoinzoiao@hotmail.com', 'paciente', '', '2026-03-07', '10823925374', 'rua da chiquinha e do chaves', 'pacajus', 'CE', '62870-000', '6984c142d1b90_1770311804.png')
ON DUPLICATE KEY UPDATE nome=VALUES(nome);

-- Example consultas (three sample entries)
INSERT INTO consultas (id, medico, especialidade, data, tipo, status, motivo, data_agendamento) VALUES
(1, 'Dr. Carlos Silva', 'Cardiologia', '2026-02-25 15:00:00', 'presencial', 'cancelado', 'oi3epewlpdlwpdlwd', '2026-02-05 16:29:23'),
(2, 'Dr. Carlos Silva', 'Cardiologia', '2026-02-16 09:00:00', 'presencial', 'cancelado', 'dor de cabeça', '2026-02-05 17:07:22'),
(3, 'Dr. Carlos Silva', 'Cardiologia', '2026-02-13 11:00:00', 'presencial', 'cancelado', 'dor de denteee', '2026-02-05 17:25:33')
ON DUPLICATE KEY UPDATE medico=VALUES(medico);

-- Example mensagens
INSERT INTO mensagens (id, remetente, assunto, mensagem, data, lida) VALUES
(2, 'Teste', 'Dúvida sobre agendamento', 'Olá, gostaria de confirmar os horários disponíveis para consulta.', '2026-02-05 17:08:00', 0),
(3, 'Allan', 'Solicitação de informações', 'Preciso de informações sobre procedimentos e disponibilidade.', '2026-02-05 17:26:00', 1)
ON DUPLICATE KEY UPDATE remetente=VALUES(remetente);

-- Example audit log starter
INSERT INTO audit_log (evento, detalhes, usuario, ip, criado_em) VALUES
('seed', 'Seed data imported', 'system', '127.0.0.1', NOW());

-- Useful indexes
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_consultas_medico ON consultas(medico);
CREATE INDEX idx_mensagens_remetente ON mensagens(remetente);

-- End of file
