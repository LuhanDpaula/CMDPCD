-- SQL Schema Migration for cmpcd_jau
-- Created via Antigravity Agent

DROP DATABASE IF EXISTS `cmpcd_jau`;
CREATE DATABASE IF NOT EXISTS `cmpcd_jau` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `cmpcd_jau`;

SET FOREIGN_KEY_CHECKS = 0;

-- 1. documentos_pcd
DROP TABLE IF EXISTS `documentos_pcd`;
CREATE TABLE `documentos_pcd` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `pcd_id` bigint(20) NOT NULL,
  `usuario_upload_id` bigint(20) DEFAULT NULL,
  `tipo_documento` enum('rg','comprovante_residencia','laudo_medico','cartao_sus','carteira_pcd','outros') NOT NULL,
  `caminho_arquivo` varchar(255) NOT NULL,
  `data_upload` datetime DEFAULT current_timestamp(),
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deletado_em` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_doc_pcd` (`pcd_id`),
  KEY `fk_doc_usuario` (`usuario_upload_id`),
  CONSTRAINT `fk_doc_pcd` FOREIGN KEY (`pcd_id`) REFERENCES `pcds` (`id`),
  CONSTRAINT `fk_doc_usuario` FOREIGN KEY (`usuario_upload_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. documentos_publicos
DROP TABLE IF EXISTS `documentos_publicos`;
CREATE TABLE `documentos_publicos` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(200) NOT NULL,
  `tipo` enum('ata','resolucao','edital','oficio','relatorio','outros') NOT NULL,
  `data_publicacao` date NOT NULL,
  `caminho_arquivo` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `status` enum('publicado','arquivado') DEFAULT 'publicado',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deletado_em` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. forms_contato
DROP TABLE IF EXISTS `forms_contato`;
CREATE TABLE `forms_contato` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `assunto` varchar(255) NOT NULL,
  `mensagem` text NOT NULL,
  `lido` tinyint(1) DEFAULT 0,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. importacoes
DROP TABLE IF EXISTS `importacoes`;
CREATE TABLE `importacoes` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `usuario_id` bigint(20) NOT NULL,
  `nome_arquivo` varchar(255) NOT NULL,
  `data_importacao` datetime DEFAULT current_timestamp(),
  `registros_sucesso` bigint(20) DEFAULT 0,
  `registros_erro` bigint(20) DEFAULT 0,
  `status` enum('em_processamento','concluida','falhou') NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_importacao_usuario` (`usuario_id`),
  CONSTRAINT `fk_importacao_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. marcos_historicos
DROP TABLE IF EXISTS `marcos_historicos`;
CREATE TABLE `marcos_historicos` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `descricao` text NOT NULL,
  `data_marco` date DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. membros_conselho
DROP TABLE IF EXISTS `membros_conselho`;
CREATE TABLE `membros_conselho` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `nome_completo` varchar(150) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `biografia` text DEFAULT NULL,
  `funcao` enum('presidente','vice-presidente','secretario','titular','suplente') NOT NULL,
  `entidade_representada` varchar(150) DEFAULT NULL,
  `data_inicio` date NOT NULL,
  `data_fim` date DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deletado_em` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. noticias
DROP TABLE IF EXISTS `noticias`;
CREATE TABLE `noticias` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `usuario_id` bigint(20) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `conteudo` text NOT NULL,
  `imagem_capa` varchar(255) DEFAULT NULL,
  `tema` enum('eventos','direitos','inclusao','saude','educacao','acessibilidade','emprego','informativos','outros') NOT NULL,
  `status` enum('rascunho','publicado','arquivado') DEFAULT 'rascunho',
  `data_publicacao` datetime DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deletado_em` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_noticia_usuario` (`usuario_id`),
  CONSTRAINT `fk_noticia_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. parceiros
DROP TABLE IF EXISTS `parceiros`;
CREATE TABLE `parceiros` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nome` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. pcd_deficiencias
DROP TABLE IF EXISTS `pcd_deficiencias`;
CREATE TABLE `pcd_deficiencias` (
  `pcd_id` bigint(20) NOT NULL,
  `tipo_deficiencia_id` bigint(20) NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`pcd_id`,`tipo_deficiencia_id`),
  KEY `fk_pd_tipo` (`tipo_deficiencia_id`),
  CONSTRAINT `fk_pd_pcd` FOREIGN KEY (`pcd_id`) REFERENCES `pcds` (`id`),
  CONSTRAINT `fk_pd_tipo` FOREIGN KEY (`tipo_deficiencia_id`) REFERENCES `tipos_deficiencia` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. pcd_responsaveis
DROP TABLE IF EXISTS `pcd_responsaveis`;
CREATE TABLE `pcd_responsaveis` (
  `pcd_id` bigint(20) NOT NULL,
  `responsavel_id` bigint(20) NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`pcd_id`,`responsavel_id`),
  KEY `fk_pr_responsavel` (`responsavel_id`),
  CONSTRAINT `fk_pr_pcd` FOREIGN KEY (`pcd_id`) REFERENCES `pcds` (`id`),
  CONSTRAINT `fk_pr_responsavel` FOREIGN KEY (`responsavel_id`) REFERENCES `responsaveis_legais` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. pcds
DROP TABLE IF EXISTS `pcds`;
CREATE TABLE `pcds` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `origem_cadastro_id` bigint(20) DEFAULT NULL,
  `nome_completo` varchar(255) NOT NULL,
  `nome_social` varchar(255) DEFAULT NULL,
  `cpf` varchar(11) NOT NULL,
  `rg` varchar(20) NOT NULL,
  `data_nascimento` date NOT NULL,
  `sexo_biologico` enum('masculino','feminino','outro') NOT NULL,
  `genero` varchar(100) DEFAULT NULL,
  `estado_civil` varchar(50) DEFAULT NULL,
  `raca_cor` enum('branca','preta','parda','amarela','indigena','nao_declarado') NOT NULL,
  `nacionalidade` varchar(100) NOT NULL,
  `cep` varchar(8) NOT NULL,
  `logradouro` varchar(255) NOT NULL,
  `numero` varchar(20) NOT NULL,
  `complemento` varchar(255) DEFAULT NULL,
  `bairro` varchar(255) NOT NULL,
  `cidade` varchar(255) NOT NULL,
  `uf` varchar(2) NOT NULL,
  `telefone_principal` varchar(20) NOT NULL,
  `telefone_secundario` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `contato_emergencia_nome` varchar(255) NOT NULL,
  `contato_emergencia_telefone` varchar(20) NOT NULL,
  `renda_familiar` decimal(10,2) NOT NULL,
  `numero_dependentes` bigint(20) NOT NULL,
  `situacao_habitacional` enum('propria_quitada','propria_financiada','alugada','cedida','outra') NOT NULL,
  `escolaridade` enum('sem_instrucao','fundamental_incompleto','fundamental_completo','medio_incompleto','medio_completo','superior_incompleto','superior_completo') NOT NULL,
  `ocupacao_atual` varchar(255) DEFAULT NULL,
  `recebe_bpc_loas` tinyint(1) DEFAULT 0,
  `beneficio_bpc_loas` varchar(150) DEFAULT NULL,
  `grau_deficiencia` enum('leve','moderada','severa','profunda') DEFAULT NULL,
  `cid` varchar(255) DEFAULT NULL,
  `data_diagnostico` date DEFAULT NULL,
  `tecnologia_assistiva` text DEFAULT NULL,
  `necessita_acompanhante` tinyint(1) DEFAULT 0,
  `medicacao_continua` text DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deletado_em` datetime DEFAULT NULL,
  `status` enum('pendente','deferido') DEFAULT 'pendente',
  PRIMARY KEY (`id`),
  UNIQUE KEY `cpf` (`cpf`),
  KEY `fk_pcd_origem` (`origem_cadastro_id`),
  CONSTRAINT `fk_pcd_origem` FOREIGN KEY (`origem_cadastro_id`) REFERENCES `parceiros` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. perfis_acesso
DROP TABLE IF EXISTS `perfis_acesso`;
CREATE TABLE `perfis_acesso` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nome` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. responsaveis_legais
DROP TABLE IF EXISTS `responsaveis_legais`;
CREATE TABLE `responsaveis_legais` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `nome_completo` varchar(255) NOT NULL,
  `cpf` varchar(11) NOT NULL,
  `rg` varchar(20) NOT NULL,
  `data_nascimento` date NOT NULL,
  `parentesco` enum('mae','pai','tutor','curador','outro') NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `responsavel_formal` tinyint(1) DEFAULT 0,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deletado_em` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cpf` (`cpf`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14. tipos_deficiencia
DROP TABLE IF EXISTS `tipos_deficiencia`;
CREATE TABLE `tipos_deficiencia` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nome` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 15. usuarios
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `perfil_id` bigint(20) NOT NULL,
  `nome_completo` varchar(255) NOT NULL,
  `cpf` varchar(11) NOT NULL,
  `celular` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `status` enum('ativo','inativo','bloqueado') NOT NULL DEFAULT 'ativo',
  `ultimo_login` datetime DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deletado_em` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cpf` (`cpf`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_usuario_perfil` (`perfil_id`),
  CONSTRAINT `fk_usuario_perfil` FOREIGN KEY (`perfil_id`) REFERENCES `perfis_acesso` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 16. logs_auditoria (Fallback do MongoDB)
DROP TABLE IF EXISTS `logs_auditoria`;
CREATE TABLE `logs_auditoria` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `usuario_id` bigint(20) DEFAULT NULL,
  `usuario_nome` varchar(255) DEFAULT NULL,
  `usuario_email` varchar(255) DEFAULT NULL,
  `usuario_perfil` varchar(50) DEFAULT NULL,
  `acao` varchar(100) NOT NULL,
  `modulo` varchar(50) NOT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `detalhes` text DEFAULT NULL,
  `ip` varchar(45) NOT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_logs_criado_em` (`criado_em`),
  KEY `idx_logs_usuario` (`usuario_id`),
  KEY `idx_logs_modulo` (`modulo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
