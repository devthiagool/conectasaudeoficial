-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 12/12/2025 às 00:48
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `conecta_saude`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `agendamentos`
--

CREATE TABLE `agendamentos` (
  `id` int(11) NOT NULL,
  `paciente_id` int(11) NOT NULL,
  `medico_id` int(11) NOT NULL,
  `data_consulta` datetime NOT NULL,
  `tipo_consulta` enum('presencial','online') DEFAULT 'presencial',
  `motivo` text DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `status` enum('agendado','confirmado','realizado','cancelado','pendente') DEFAULT 'agendado',
  `data_agendamento` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `artigos`
--

CREATE TABLE `artigos` (
  `id` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `conteudo` text NOT NULL,
  `autor_id` int(11) NOT NULL,
  `categoria` varchar(50) DEFAULT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `visualizacoes` int(11) DEFAULT 0,
  `status` enum('publicado','rascunho') DEFAULT 'publicado',
  `data_publicacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `cpf` varchar(14) DEFAULT NULL,
  `telefone` varchar(15) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `tipo` enum('paciente','medico','admin') DEFAULT 'paciente',
  `especialidade` varchar(100) DEFAULT NULL,
  `crm` varchar(20) DEFAULT NULL,
  `endereco` text DEFAULT NULL,
  `cidade` varchar(50) DEFAULT NULL,
  `estado` char(2) DEFAULT NULL,
  `cep` varchar(9) DEFAULT NULL,
  `foto` varchar(255) DEFAULT 'default.jpg',
  `status` enum('ativo','inativo','pendente') DEFAULT 'ativo',
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `cpf`, `telefone`, `data_nascimento`, `tipo`, `especialidade`, `crm`, `endereco`, `cidade`, `estado`, `cep`, `foto`, `status`, `data_cadastro`) VALUES
(1, 'Dr. Carlos Silva', 'carlos@medico.com', '$2y$10$YourHashHere', NULL, '(11) 9999-8888', NULL, 'medico', 'Cardiologia', 'CRM-SP 12345', NULL, NULL, NULL, NULL, 'default.jpg', 'ativo', '2025-12-11 22:27:23'),
(2, 'Dra. Ana Souza', 'ana@medico.com', '$2y$10$YourHashHere', NULL, '(11) 9777-6666', NULL, 'medico', 'Pediatria', 'CRM-SP 67890', NULL, NULL, NULL, NULL, 'default.jpg', 'ativo', '2025-12-11 22:27:23'),
(3, 'Dr. Roberto Lima', 'roberto@medico.com', '$2y$10$YourHashHere', NULL, '(11) 9555-4444', NULL, 'medico', 'Ortopedia', 'CRM-SP 54321', NULL, NULL, NULL, NULL, 'default.jpg', 'ativo', '2025-12-11 22:27:23'),
(4, 'Administrador', 'admin@conectasaude.com', '$2y$10$YourHashHere', NULL, NULL, NULL, 'admin', NULL, NULL, NULL, NULL, NULL, NULL, 'default.jpg', 'ativo', '2025-12-11 22:27:24'),
(5, 'THIAGO GOMES DE OLIVEIRA', 'thiagogomesstudent@gmail.com', '$2y$10$3Tz5k/MNnMKVxQV/GjYyCe.Th5vlm2XLHui49I3lkrJ.jfv0LmL7q', NULL, NULL, NULL, 'paciente', NULL, NULL, NULL, NULL, NULL, NULL, 'default.jpg', 'ativo', '2025-12-11 22:43:18');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `agendamentos`
--
ALTER TABLE `agendamentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `paciente_id` (`paciente_id`),
  ADD KEY `medico_id` (`medico_id`);

--
-- Índices de tabela `artigos`
--
ALTER TABLE `artigos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `autor_id` (`autor_id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `cpf` (`cpf`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `agendamentos`
--
ALTER TABLE `agendamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `artigos`
--
ALTER TABLE `artigos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `agendamentos`
--
ALTER TABLE `agendamentos`
  ADD CONSTRAINT `agendamentos_ibfk_1` FOREIGN KEY (`paciente_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `agendamentos_ibfk_2` FOREIGN KEY (`medico_id`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `artigos`
--
ALTER TABLE `artigos`
  ADD CONSTRAINT `artigos_ibfk_1` FOREIGN KEY (`autor_id`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
