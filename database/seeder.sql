-- SQL Data Seeder for cmpcd_jau
-- Created via Antigravity Agent

USE `cmpcd_jau`;

SET FOREIGN_KEY_CHECKS = 0;

-- 1. perfis_acesso
INSERT INTO `perfis_acesso` (`id`, `nome`, `descricao`, `criado_em`, `atualizado_em`) VALUES
('1', 'admin_total', 'Administrador com acesso total ao sistema', '2026-06-01 21:55:21', '2026-06-01 21:55:21'),
('2', 'admin_parcial', 'Administrador com acesso parcial', '2026-06-01 21:55:21', '2026-06-01 21:55:21'),
('3', 'editor', 'Editor de conteúdos', '2026-06-01 21:55:21', '2026-06-01 21:55:21');

-- 2. tipos_deficiencia
INSERT INTO `tipos_deficiencia` (`id`, `nome`, `descricao`, `criado_em`, `atualizado_em`) VALUES
('1', 'Física', 'Alteração completa ou parcial de um ou mais segmentos do corpo humano', '2026-06-01 21:55:21', '2026-06-01 21:55:21'),
('2', 'Auditiva', 'Perda bilateral, parcial ou total de quarenta decibéis (dB) ou mais', '2026-06-01 21:55:21', '2026-06-01 21:55:21'),
('3', 'Visual', 'Cegueira, baixa visão ou visão monocular', '2026-06-01 21:55:21', '2026-06-01 21:55:21'),
('4', 'Intelectual', 'Funcionamento intelectual significativamente inferior à média', '2026-06-01 21:55:21', '2026-06-01 21:55:21'),
('5', 'Múltipla', 'Associação de duas ou mais deficiências', '2026-06-01 21:55:21', '2026-06-01 21:55:21'),
('6', 'TEA', 'Transtorno do Espectro Autista', '2026-06-01 21:55:21', '2026-06-01 21:55:21'),
('7', 'Outros', 'Outras condições não listadas', '2026-06-01 21:55:21', '2026-06-01 21:55:21');

-- 3. usuarios
INSERT INTO `usuarios` (`id`, `perfil_id`, `nome_completo`, `cpf`, `celular`, `email`, `senha`, `status`, `ultimo_login`, `criado_em`, `atualizado_em`, `deletado_em`) VALUES
('1', '1', 'Administrador Geral', '12345678901', '14999999999', 'admin@admin.com', '$2y$10$wwOrm3AjfJx09mewDLDjuuLczM7vfV2wb.PdvQT2.VCGjc.SVhWyu', 'ativo', '2026-06-04 18:47:39', '2026-06-01 21:55:21', '2026-06-04 18:47:39', NULL),
('2', '1', 'Samuel Custodio', '52434393802', '(14) 99999-9999', 'samuel@email.com', '$2y$10$20QhrF1xwtH.YFkFyB2W8.PUvwASI6IfBK/LMBX1Gk7AcVkP6EMM6', 'ativo', '2026-06-04 18:46:40', '2026-06-01 21:55:54', '2026-06-04 20:01:46', NULL),
('3', '3', 'Ana maria', '1234567894', '(14) 99999-9999', 'anamaria@email.com', '$2y$10$2aHgQ6xbqC6npd0ODvKQA.GVdTPCuFvXkQgLHWCh26WUDSbkamPiW', 'ativo', '2026-06-02 06:54:00', '2026-06-02 06:53:43', '2026-06-04 18:36:36', NULL);

-- 4. pcds
INSERT INTO `pcds` (`id`, `origem_cadastro_id`, `nome_completo`, `nome_social`, `cpf`, `rg`, `data_nascimento`, `sexo_biologico`, `genero`, `estado_civil`, `raca_cor`, `nacionalidade`, `cep`, `logradouro`, `numero`, `complemento`, `bairro`, `cidade`, `uf`, `telefone_principal`, `telefone_secundario`, `email`, `contato_emergencia_nome`, `contato_emergencia_telefone`, `renda_familiar`, `numero_dependentes`, `situacao_habitacional`, `escolaridade`, `ocupacao_atual`, `recebe_bpc_loas`, `beneficio_bpc_loas`, `grau_deficiencia`, `cid`, `data_diagnostico`, `tecnologia_assistiva`, `necessita_acompanhante`, `medicacao_continua`, `criado_em`, `atualizado_em`, `deletado_em`, `status`) VALUES
('1', NULL, 'João da Silva', 'Joãozinho', '98765432100', '123456789', '1990-05-15', 'masculino', 'masculino', 'solteiro', 'parda', 'Brasileira', '17201000', 'Rua das Flores', '123', 'Apto 1', 'Centro', 'Jaú', 'SP', '(14) 99888-7766', '', 'joao@email.com', 'Maria Silva', '(14) 99777-6655', '1500.00', '2', 'alugada', 'medio_completo', '', '0', '', 'leve', 'G80', '2010-10-10', 'Nenhuma', '0', 'Nenhuma', '2026-06-01 21:58:17', '2026-06-04 18:20:55', '2026-06-01 22:51:08', 'deferido'),
('2', NULL, 'Maria de Souza', '', '12345678909', '12345678', '1985-08-20', 'feminino', '', 'casado', 'branca', 'Brasileira', '17201100', 'Avenida Principal', '456', '', 'Jardim America', 'Jaú', 'SP', '14998765432', '', 'maria@email.com', 'José Souza', '14997654321', '2000.00', '1', 'propria_quitada', 'superior_completo', NULL, '0', '', 'moderada', 'H54', NULL, '', '0', '', '2026-06-01 21:58:43', '2026-06-04 19:08:11', '2026-06-04 19:08:11', 'deferido'),
('6', NULL, 'Ana maria', 'Joãozinho', '78945612314', '51541562', '1993-02-20', 'feminino', '', '', 'preta', 'Brasileira', '17201000', 'Rua José Carlone', '275', '', 'Centro', 'Jaú', 'SP', '(14) 99888-7766', '', '', 'Marcia', '(14) 99777-8946', '4000.00', '1', 'alugada', 'medio_incompleto', '', '0', '', 'profunda', 'F84', '2005-01-12', '', '0', '', '2026-06-02 07:13:43', '2026-06-04 19:08:00', '2026-06-04 19:08:00', 'deferido'),
('16', NULL, 'Lucas Oliveira Santos', 'Lucas', '44433322211', '12345678', '1992-04-15', 'masculino', 'Cisgênero', 'solteiro', 'branca', 'Brasileira', '17201000', 'Rua Quintino Bocaiúva', '120', '', 'Centro', 'Jaú', 'SP', '(14) 99888-1111', '', 'lucas.oliveira@email.com', 'Mariana Oliveira', '(14) 99888-2222', '2200.00', '1', 'alugada', 'medio_completo', NULL, '0', '', 'moderada', 'G80, H54', '2000-05-10', 'Cadeira de rodas manual', '0', 'Nenhuma', '2026-06-02 07:50:35', '2026-06-04 19:08:07', '2026-06-04 19:08:07', 'deferido'),
('17', NULL, 'Beatriz Ramos de Souza', '', '55566677788', '987654321', '1985-09-20', 'feminino', '', 'casado', 'parda', 'Brasileira', '17207120', 'Avenida Rodrigues Alves', '1040', 'Bloco B', 'Jardim América', 'Jaú', 'SP', '(14) 99777-3333', '', 'beatriz.ramos@email.com', 'Carlos de Souza', '(14) 99777-4444', '3500.00', '2', 'propria_quitada', 'superior_completo', '', '0', '', 'severa', 'H54.0, F84', '1995-10-12', 'Lupa eletrônica', '1', 'Medicação controle especial', '2026-06-02 07:50:35', '2026-06-04 19:08:05', '2026-06-04 19:08:05', 'deferido'),
('18', NULL, 'Thiago Costa Almeida', '', '99988877766', '456789012', '2003-12-05', 'masculino', '', 'solteiro', 'preta', 'Brasileira', '17210150', 'Rua Major Prado', '55', '', 'Vila Nova', 'Jaú', 'SP', '(14) 99666-5555', '', 'thiago.almeida@email.com', 'Sônia Costa', '(14) 99666-4444', '1800.00', '0', 'cedida', 'fundamental_incompleto', NULL, '1', 'BPC Benefício Federal', 'moderada', 'F84.0, H90', '2007-03-25', 'Aparelho auditivo bilateral', '1', 'Nenhuma', '2026-06-02 07:50:35', '2026-06-04 19:08:14', '2026-06-04 19:08:14', 'deferido'),
('21', NULL, 'Carlos Alberto de Teste', 'Carlos Teste', '77777777777', '777777777', '1995-07-20', 'masculino', 'Cisgênero', 'solteiro', 'parda', 'Brasileira', '17201000', 'Avenida Principal', '100', '', 'Centro', 'Jaú', 'SP', '(14) 99111-2222', '', 'carlos.teste@email.com', 'Ana Teste', '(14) 99111-3333', '2500.00', '0', 'propria_quitada', 'medio_completo', NULL, '0', '', 'moderada', 'H54', '2005-08-15', 'Óculos especiais', '0', 'Nenhuma', '2026-06-04 18:25:20', '2026-06-04 18:28:00', '2026-06-04 18:28:00', 'deferido'),
('25', NULL, 'João da Silva', '', '73387225231', '498152617', '1975-06-05', 'masculino', '', 'solteiro', 'branca', 'Brasileira', '17201000', 'Rua Tiradentes', '762', '', 'Vila Maria', 'Jahu', 'SP', '(14) 99968-6986', '', 'joão.da.silva@exemplo.com', 'Contato Parentee', '(14) 99421-3420', '4450.16', '3', 'alugada', 'superior_completo', NULL, '1', '', 'moderada', 'G80', '2019-06-05', '', '1', '', '2026-06-04 19:14:29', '2026-06-04 19:14:29', NULL, 'deferido'),
('26', NULL, 'Maria Oliveira Santos', '', '39267164071', '433608561', '1975-06-05', 'feminino', '', 'divorciado', 'parda', 'Brasileira', '17202120', 'Avenida XV de Novembro', '965', 'Apto 65', 'Centro', 'Jahu', 'SP', '(14) 99328-8896', '', 'maria.oliveira.santos@exemplo.com', 'Contato Parentee', '(14) 99345-1764', '3529.05', '2', 'alugada', 'superior_completo', NULL, '0', '', 'severa', 'H54', '2020-06-05', 'Cadeira de rodas', '1', '', '2026-06-04 19:14:29', '2026-06-04 19:14:29', NULL, 'deferido'),
('27', NULL, 'Pedro Henrique Lima', '', '63619710563', '498756678', '1965-06-05', 'masculino', '', 'solteiro', 'preta', 'Brasileira', '17201000', 'Avenida XV de Novembro', '31', 'Apto 48', 'Jardim Rosa Branca', 'Jahu', 'SP', '(14) 99529-2009', '', 'pedro.henrique.lima@exemplo.com', 'Contato Parentee', '(14) 99354-1111', '2588.90', '0', 'cedida', 'fundamental_completo', NULL, '1', '', 'leve', 'H90', '2024-06-05', '', '0', '', '2026-06-04 19:14:29', '2026-06-04 19:14:29', NULL, 'deferido'),
('28', NULL, 'Ana Beatriz Souza', '', '28685941017', '132143465', '1994-06-05', 'feminino', '', 'divorciado', 'branca', 'Brasileira', '17203240', 'Rua Humaitá', '124', '', 'Jardim Rosa Branca', 'Jahu', 'SP', '(14) 99173-2930', '', 'ana.beatriz.souza@exemplo.com', 'Contato Parentee', '(14) 99802-9973', '4918.31', '3', 'cedida', 'medio_completo', NULL, '0', '', 'moderada', 'F84', '2020-06-05', 'Cadeira de rodas', '0', '', '2026-06-04 19:14:29', '2026-06-04 19:14:29', NULL, 'deferido'),
('29', NULL, 'Carlos Eduardo Costa', '', '44784615331', '422911861', '1998-06-05', 'masculino', '', 'divorciado', 'amarela', 'Brasileira', '17205150', 'Rua Major Prado', '735', '', 'Vila Maria', 'Jahu', 'SP', '(14) 99286-8941', '', 'carlos.eduardo.costa@exemplo.com', 'Contato Parentee', '(14) 99979-5425', '3646.36', '2', 'propria_quitada', 'superior_completo', NULL, '1', '', 'severa', 'G80', '2023-06-05', 'Cadeira de rodas', '0', '', '2026-06-04 19:14:29', '2026-06-04 19:14:29', NULL, 'deferido'),
('30', NULL, 'Juliana Ferreira Melo', '', '93394541637', '119172365', '1991-06-05', 'feminino', '', 'solteiro', 'indigena', 'Brasileira', '17207300', 'Rua Major Prado', '947', '', 'Centro', 'Jahu', 'SP', '(14) 99295-4123', '', 'juliana.ferreira.melo@exemplo.com', 'Contato Parentee', '(14) 99869-9539', '3176.67', '2', 'alugada', 'fundamental_completo', NULL, '1', '', 'leve', 'H54', '2020-06-05', '', '0', '', '2026-06-04 19:14:29', '2026-06-04 19:14:29', NULL, 'deferido'),
('31', NULL, 'Lucas Rodrigues Alves', '', '65897682926', '152483574', '1982-06-05', 'masculino', '', 'divorciado', 'parda', 'Brasileira', '17201000', 'Rua Major Prado', '318', '', 'Chácara Braz Miraglia', 'Jahu', 'SP', '(14) 99886-3199', '', 'lucas.rodrigues.alves@exemplo.com', 'Contato Parentee', '(14) 99978-5919', '2050.54', '1', 'propria_quitada', 'medio_completo', NULL, '1', '', 'moderada', 'H90', '2020-06-05', '', '0', '', '2026-06-04 19:14:29', '2026-06-04 19:14:29', NULL, 'deferido'),
('32', NULL, 'Camila Barbosa Nunes', '', '82515051120', '397694665', '2015-06-05', 'feminino', '', 'divorciado', 'branca', 'Brasileira', '17207300', 'Rua Tiradentes', '266', 'Apto 111', 'Centro', 'Jahu', 'SP', '(14) 99726-1031', '', 'camila.barbosa.nunes@exemplo.com', 'Contato Parentee', '(14) 99182-1160', '4481.69', '3', 'cedida', 'medio_completo', NULL, '1', '', 'severa', 'F84', '2021-06-05', '', '1', '', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL, 'deferido'),
('33', NULL, 'Matheus Pereira Cruz', '', '25353541873', '144368237', '1990-06-05', 'masculino', '', 'solteiro', 'preta', 'Brasileira', '17201000', 'Rua Major Prado', '796', '', 'Chácara Braz Miraglia', 'Jahu', 'SP', '(14) 99705-3564', '', 'matheus.pereira.cruz@exemplo.com', 'Contato Parentee', '(14) 99257-6885', '4746.16', '3', 'cedida', 'medio_completo', NULL, '0', '', 'leve', 'G80', '2020-06-05', '', '1', '', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL, 'deferido'),
('34', NULL, 'Larissa Fernandes Rocha', '', '27322583242', '427452540', '2009-06-05', 'feminino', '', 'divorciado', 'branca', 'Brasileira', '17205150', 'Avenida XV de Novembro', '917', 'Apto 68', 'Vila Maria', 'Jahu', 'SP', '(14) 99588-5562', '', 'larissa.fernandes.rocha@exemplo.com', 'Contato Parentee', '(14) 99153-2792', '4595.55', '0', 'propria_quitada', 'superior_completo', NULL, '0', '', 'moderada', 'H54', '2024-06-05', 'Cadeira de rodas', '0', '', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL, 'deferido'),
('35', NULL, 'Thiago Gomes Ribeiro', '', '61112336818', '127233322', '1989-06-05', 'masculino', '', 'casado', 'parda', 'Brasileira', '17207300', 'Rua Humaitá', '169', 'Apto 52', 'Chácara Braz Miraglia', 'Jahu', 'SP', '(14) 99360-3187', '', 'thiago.gomes.ribeiro@exemplo.com', 'Contato Parentee', '(14) 99742-5996', '1934.05', '0', 'cedida', 'medio_completo', NULL, '1', '', 'severa', 'H90', '2019-06-05', 'Cadeira de rodas', '0', '', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL, 'deferido'),
('36', NULL, 'Amanda Martins Carvalho', '', '68663297696', '224256271', '2005-06-05', 'feminino', '', 'casado', 'amarela', 'Brasileira', '17202120', 'Rua Amaral Gurgel', '624', '', 'Jardim América', 'Jahu', 'SP', '(14) 99555-2180', '', 'amanda.martins.carvalho@exemplo.com', 'Contato Parentee', '(14) 99791-1929', '4071.65', '1', 'propria_quitada', 'medio_completo', NULL, '0', '', 'leve', 'F84', '2022-06-05', 'Cadeira de rodas', '1', '', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL, 'deferido'),
('37', NULL, 'Rafael Cardoso Dias', '', '67171597229', '321184103', '2013-06-05', 'masculino', '', 'solteiro', 'branca', 'Brasileira', '17203240', 'Avenida XV de Novembro', '669', '', 'Centro', 'Jahu', 'SP', '(14) 99425-3456', '', 'rafael.cardoso.dias@exemplo.com', 'Contato Parentee', '(14) 99183-5481', '1754.82', '2', 'alugada', 'superior_completo', NULL, '1', '', 'moderada', 'G80', '2023-06-05', '', '0', '', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL, 'deferido'),
('38', NULL, 'Gabriela Castro Teixeira', '', '27852649430', '125139720', '1993-06-05', 'feminino', '', 'solteiro', 'preta', 'Brasileira', '17207300', 'Rua Humaitá', '103', '', 'Centro', 'Jahu', 'SP', '(14) 99673-8857', '', 'gabriela.castro.teixeira@exemplo.com', 'Contato Parentee', '(14) 99820-9181', '4847.17', '2', 'cedida', 'medio_completo', NULL, '0', '', 'severa', 'H54', '2019-06-05', 'Cadeira de rodas', '0', '', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL, 'deferido'),
('39', NULL, 'Bruno Araujo Vieira', '', '83195488826', '478262500', '1979-06-05', 'masculino', '', 'divorciado', 'parda', 'Brasileira', '17201000', 'Avenida XV de Novembro', '992', '', 'Centro', 'Jahu', 'SP', '(14) 99242-6001', '', 'bruno.araujo.vieira@exemplo.com', 'Contato Parentee', '(14) 99138-9277', '2040.22', '1', 'alugada', 'medio_completo', NULL, '1', '', 'leve', 'H90', '2024-06-05', '', '1', '', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL, 'deferido'),
('40', NULL, 'Fernanda Moreira Pinto', '', '60739146060', '504737985', '1966-06-05', 'feminino', '', 'solteiro', 'branca', 'Brasileira', '17201000', 'Avenida XV de Novembro', '197', '', 'Vila Maria', 'Jahu', 'SP', '(14) 99959-9677', '', 'fernanda.moreira.pinto@exemplo.com', 'Contato Parentee', '(14) 99377-1990', '4732.13', '3', 'cedida', 'medio_completo', '', '1', '', 'moderada', 'F84', '2022-06-05', 'Cadeira de rodas', '1', '', '2026-06-04 19:14:30', '2026-06-04 19:16:07', NULL, 'deferido'),
('41', NULL, 'Gabriel Almeida Correia', '', '34095621847', '274471536', '2012-06-05', 'masculino', '', 'casado', 'preta', 'Brasileira', '17201000', 'Rua Major Prado', '646', '', 'Jardim Rosa Branca', 'Jahu', 'SP', '(14) 99384-8008', '', 'gabriel.almeida.correia@exemplo.com', 'Contato Parentee', '(14) 99582-8921', '4737.87', '2', 'alugada', 'medio_completo', NULL, '0', '', 'severa', 'G80', '2024-06-05', '', '0', '', '2026-06-04 19:14:30', '2026-06-04 19:16:10', NULL, 'deferido'),
('42', NULL, 'Patricia Ramos Machado', '', '45280934149', '317898152', '2012-06-05', 'feminino', '', 'casado', 'indigena', 'Brasileira', '17203240', 'Rua Major Prado', '969', 'Apto 116', 'Vila Maria', 'Jahu', 'SP', '(14) 99849-1168', '', 'patricia.ramos.machado@exemplo.com', 'Contato Parentee', '(14) 99887-5742', '2220.10', '2', 'alugada', 'medio_completo', NULL, '0', '', 'leve', 'H54', '2023-06-05', 'Cadeira de rodas', '1', '', '2026-06-04 19:14:30', '2026-06-04 19:16:47', NULL, 'deferido'),
('43', NULL, 'Leonardo Cavalcanti Borges', '', '93355818969', '245912956', '1985-06-05', 'masculino', '', 'solteiro', 'parda', 'Brasileira', '17201000', 'Rua Tiradentes', '800', 'Apto 13', 'Centro', 'Jahu', 'SP', '(14) 99319-8214', '', 'leonardo.cavalcanti.borges@exemplo.com', 'Contato Parentee', '(14) 99262-1490', '1977.82', '3', 'cedida', 'superior_completo', NULL, '0', '', 'moderada', 'H90', '2021-06-05', '', '0', '', '2026-06-04 19:14:30', '2026-06-04 19:16:44', NULL, 'deferido'),
('44', NULL, 'Vanessa Santos Nascimento', '', '39842917476', '219049647', '2011-06-05', 'feminino', '', 'solteiro', 'branca', 'Brasileira', '17207300', 'Rua Tiradentes', '38', '', 'Centro', 'Jahu', 'SP', '(14) 99988-1168', '', 'vanessa.santos.nascimento@exemplo.com', 'Contato Parentee', '(14) 99299-9335', '2618.08', '0', 'cedida', 'fundamental_completo', NULL, '1', '', 'severa', 'F84', '2018-06-05', 'Cadeira de rodas', '0', '', '2026-06-04 19:14:30', '2026-06-04 19:16:41', NULL, 'deferido');

-- 5. responsaveis_legais
INSERT INTO `responsaveis_legais` (`id`, `nome_completo`, `cpf`, `rg`, `data_nascimento`, `parentesco`, `telefone`, `email`, `responsavel_formal`, `criado_em`, `atualizado_em`, `deletado_em`) VALUES
('1', 'Sônia Costa', '11122233344', '998887776', '1975-08-14', 'mae', '(14) 99666-4444', 'sonia.costa@email.com', '1', '2026-06-02 07:50:35', '2026-06-02 07:50:35', NULL);

-- 6. pcd_responsaveis
INSERT INTO `pcd_responsaveis` (`pcd_id`, `responsavel_id`, `criado_em`) VALUES
('18', '1', '2026-06-02 07:50:35');

-- 7. pcd_deficiencias
INSERT INTO `pcd_deficiencias` (`pcd_id`, `tipo_deficiencia_id`, `criado_em`) VALUES
('1', '1', '2026-06-01 22:04:40'),
('1', '2', '2026-06-01 22:04:40'),
('2', '1', '2026-06-01 21:58:43'),
('6', '4', '2026-06-04 18:34:47'),
('16', '1', '2026-06-02 07:50:35'),
('16', '2', '2026-06-02 07:50:35'),
('17', '3', '2026-06-04 18:29:17'),
('17', '4', '2026-06-04 18:29:17'),
('18', '2', '2026-06-02 07:50:35'),
('18', '6', '2026-06-02 07:50:35'),
('21', '1', '2026-06-04 18:25:20'),
('21', '3', '2026-06-04 18:25:20'),
('25', '5', '2026-06-04 19:14:29'),
('26', '1', '2026-06-04 19:14:29'),
('27', '4', '2026-06-04 19:14:29'),
('27', '5', '2026-06-04 19:14:29'),
('28', '2', '2026-06-04 19:14:29'),
('29', '1', '2026-06-04 19:14:29'),
('30', '1', '2026-06-04 19:14:29'),
('30', '5', '2026-06-04 19:14:29'),
('31', '5', '2026-06-04 19:14:30'),
('32', '5', '2026-06-04 19:14:30'),
('33', '7', '2026-06-04 19:14:30'),
('34', '1', '2026-06-04 19:14:30'),
('34', '6', '2026-06-04 19:14:30'),
('35', '2', '2026-06-04 19:14:30'),
('35', '4', '2026-06-04 19:14:30'),
('36', '3', '2026-06-04 19:14:30'),
('37', '5', '2026-06-04 19:14:30'),
('37', '7', '2026-06-04 19:14:30'),
('38', '4', '2026-06-04 19:14:30'),
('39', '6', '2026-06-04 19:14:30'),
('40', '7', '2026-06-04 19:16:03'),
('41', '5', '2026-06-04 19:14:30'),
('42', '6', '2026-06-04 19:14:30'),
('42', '7', '2026-06-04 19:14:30'),
('43', '2', '2026-06-04 19:14:30'),
('43', '6', '2026-06-04 19:14:30'),
('44', '2', '2026-06-04 19:14:30');

-- 8. documentos_pcd
INSERT INTO `documentos_pcd` (`id`, `pcd_id`, `usuario_upload_id`, `tipo_documento`, `caminho_arquivo`, `data_upload`, `criado_em`, `atualizado_em`, `deletado_em`) VALUES
('22', '25', NULL, 'rg', 'storage/pcd_docs/pcd_doc_rg_25_1780611269_6a21f8c5d3b30.jpg', '2026-06-04 19:14:29', '2026-06-04 19:14:29', '2026-06-04 19:14:29', NULL),
('23', '25', NULL, 'comprovante_residencia', 'storage/pcd_docs/pcd_doc_comprovante_residencia_25_1780611269_6a21f8c5d665e.jpg', '2026-06-04 19:14:29', '2026-06-04 19:14:29', '2026-06-04 19:14:29', NULL),
('24', '25', NULL, 'laudo_medico', 'storage/pcd_docs/pcd_doc_laudo_medico_25_1780611269_6a21f8c5d75c7.jpg', '2026-06-04 19:14:29', '2026-06-04 19:14:29', '2026-06-04 19:14:29', NULL),
('25', '26', NULL, 'rg', 'storage/pcd_docs/pcd_doc_rg_26_1780611269_6a21f8c5db83d.jpg', '2026-06-04 19:14:29', '2026-06-04 19:14:29', '2026-06-04 19:14:29', NULL),
('26', '26', NULL, 'comprovante_residencia', 'storage/pcd_docs/pcd_doc_comprovante_residencia_26_1780611269_6a21f8c5dcaca.jpg', '2026-06-04 19:14:29', '2026-06-04 19:14:29', '2026-06-04 19:14:29', NULL),
('27', '26', NULL, 'laudo_medico', 'storage/pcd_docs/pcd_doc_laudo_medico_26_1780611269_6a21f8c5dda6e.jpg', '2026-06-04 19:14:29', '2026-06-04 19:14:29', '2026-06-04 19:14:29', NULL),
('28', '27', NULL, 'rg', 'storage/pcd_docs/pcd_doc_rg_27_1780611269_6a21f8c5e10bd.jpg', '2026-06-04 19:14:29', '2026-06-04 19:14:29', '2026-06-04 19:14:29', NULL),
('29', '27', NULL, 'comprovante_residencia', 'storage/pcd_docs/pcd_doc_comprovante_residencia_27_1780611269_6a21f8c5e2e7d.jpg', '2026-06-04 19:14:29', '2026-06-04 19:14:29', '2026-06-04 19:14:29', NULL),
('30', '27', NULL, 'laudo_medico', 'storage/pcd_docs/pcd_doc_laudo_medico_27_1780611269_6a21f8c5e3ff2.jpg', '2026-06-04 19:14:29', '2026-06-04 19:14:29', '2026-06-04 19:14:29', NULL),
('31', '28', NULL, 'rg', 'storage/pcd_docs/pcd_doc_rg_28_1780611269_6a21f8c5e54b2.jpg', '2026-06-04 19:14:29', '2026-06-04 19:14:29', '2026-06-04 19:14:29', NULL),
('32', '28', NULL, 'comprovante_residencia', 'storage/pcd_docs/pcd_doc_comprovante_residencia_28_1780611269_6a21f8c5e6306.jpg', '2026-06-04 19:14:29', '2026-06-04 19:14:29', '2026-06-04 19:14:29', NULL),
('33', '28', NULL, 'laudo_medico', 'storage/pcd_docs/pcd_doc_laudo_medico_28_1780611269_6a21f8c5ea028.jpg', '2026-06-04 19:14:29', '2026-06-04 19:14:29', '2026-06-04 19:14:29', NULL),
('34', '29', NULL, 'rg', 'storage/pcd_docs/pcd_doc_rg_29_1780611269_6a21f8c5ebf32.jpg', '2026-06-04 19:14:29', '2026-06-04 19:14:29', '2026-06-04 19:14:29', NULL),
('35', '29', NULL, 'comprovante_residencia', 'storage/pcd_docs/pcd_doc_comprovante_residencia_29_1780611269_6a21f8c5ecc16.jpg', '2026-06-04 19:14:29', '2026-06-04 19:14:29', '2026-06-04 19:14:29', NULL),
('36', '29', NULL, 'laudo_medico', 'storage/pcd_docs/pcd_doc_laudo_medico_29_1780611269_6a21f8c5ed806.jpg', '2026-06-04 19:14:29', '2026-06-04 19:14:29', '2026-06-04 19:14:29', NULL),
('37', '30', NULL, 'rg', 'storage/pcd_docs/pcd_doc_rg_30_1780611269_6a21f8c5ef647.jpg', '2026-06-04 19:14:29', '2026-06-04 19:14:29', '2026-06-04 19:14:29', NULL),
('38', '30', NULL, 'comprovante_residencia', 'storage/pcd_docs/pcd_doc_comprovante_residencia_30_1780611269_6a21f8c5f04aa.jpg', '2026-06-04 19:14:29', '2026-06-04 19:14:29', '2026-06-04 19:14:29', NULL),
('39', '30', NULL, 'laudo_medico', 'storage/pcd_docs/pcd_doc_laudo_medico_30_1780611269_6a21f8c5f1320.jpg', '2026-06-04 19:14:29', '2026-06-04 19:14:29', '2026-06-04 19:14:29', NULL),
('40', '31', NULL, 'rg', 'storage/pcd_docs/pcd_doc_rg_31_1780611270_6a21f8c603fa8.jpg', '2026-06-04 19:14:30', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL),
('41', '31', NULL, 'comprovante_residencia', 'storage/pcd_docs/pcd_doc_comprovante_residencia_31_1780611270_6a21f8c604eaa.jpg', '2026-06-04 19:14:30', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL),
('42', '31', NULL, 'laudo_medico', 'storage/pcd_docs/pcd_doc_laudo_medico_31_1780611270_6a21f8c605ca9.jpg', '2026-06-04 19:14:30', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL),
('43', '32', NULL, 'rg', 'storage/pcd_docs/pcd_doc_rg_32_1780611270_6a21f8c60aace.jpg', '2026-06-04 19:14:30', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL),
('44', '32', NULL, 'comprovante_residencia', 'storage/pcd_docs/pcd_doc_comprovante_residencia_32_1780611270_6a21f8c60efb0.jpg', '2026-06-04 19:14:30', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL),
('45', '32', NULL, 'laudo_medico', 'storage/pcd_docs/pcd_doc_laudo_medico_32_1780611270_6a21f8c612d72.jpg', '2026-06-04 19:14:30', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL),
('46', '33', NULL, 'rg', 'storage/pcd_docs/pcd_doc_rg_33_1780611270_6a21f8c61599c.jpg', '2026-06-04 19:14:30', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL),
('47', '33', NULL, 'comprovante_residencia', 'storage/pcd_docs/pcd_doc_comprovante_residencia_33_1780611270_6a21f8c617fd9.jpg', '2026-06-04 19:14:30', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL),
('48', '33', NULL, 'laudo_medico', 'storage/pcd_docs/pcd_doc_laudo_medico_33_1780611270_6a21f8c61a045.jpg', '2026-06-04 19:14:30', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL),
('49', '34', NULL, 'rg', 'storage/pcd_docs/pcd_doc_rg_34_1780611270_6a21f8c61ca8e.jpg', '2026-06-04 19:14:30', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL),
('50', '34', NULL, 'comprovante_residencia', 'storage/pcd_docs/pcd_doc_comprovante_residencia_34_1780611270_6a21f8c620848.jpg', '2026-06-04 19:14:30', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL),
('51', '34', NULL, 'laudo_medico', 'storage/pcd_docs/pcd_doc_laudo_medico_34_1780611270_6a21f8c621f89.jpg', '2026-06-04 19:14:30', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL),
('52', '35', NULL, 'rg', 'storage/pcd_docs/pcd_doc_rg_35_1780611270_6a21f8c62395a.jpg', '2026-06-04 19:14:30', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL),
('53', '35', NULL, 'comprovante_residencia', 'storage/pcd_docs/pcd_doc_comprovante_residencia_35_1780611270_6a21f8c624c7d.jpg', '2026-06-04 19:14:30', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL),
('54', '35', NULL, 'laudo_medico', 'storage/pcd_docs/pcd_doc_laudo_medico_35_1780611270_6a21f8c625b3b.jpg', '2026-06-04 19:14:30', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL),
('55', '36', NULL, 'rg', 'storage/pcd_docs/pcd_doc_rg_36_1780611270_6a21f8c6274b9.jpg', '2026-06-04 19:14:30', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL),
('56', '36', NULL, 'comprovante_residencia', 'storage/pcd_docs/pcd_doc_comprovante_residencia_36_1780611270_6a21f8c628fcd.jpg', '2026-06-04 19:14:30', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL),
('57', '36', NULL, 'laudo_medico', 'storage/pcd_docs/pcd_doc_laudo_medico_36_1780611270_6a21f8c62b3ae.jpg', '2026-06-04 19:14:30', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL),
('58', '37', NULL, 'rg', 'storage/pcd_docs/pcd_doc_rg_37_1780611270_6a21f8c62ca00.jpg', '2026-06-04 19:14:30', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL),
('59', '37', NULL, 'comprovante_residencia', 'storage/pcd_docs/pcd_doc_comprovante_residencia_37_1780611270_6a21f8c62d6f5.jpg', '2026-06-04 19:14:30', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL),
('60', '37', NULL, 'laudo_medico', 'storage/pcd_docs/pcd_doc_laudo_medico_37_1780611270_6a21f8c62e63a.jpg', '2026-06-04 19:14:30', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL),
('61', '38', NULL, 'rg', 'storage/pcd_docs/pcd_doc_rg_38_1780611270_6a21f8c62ffc9.jpg', '2026-06-04 19:14:30', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL),
('62', '38', NULL, 'comprovante_residencia', 'storage/pcd_docs/pcd_doc_comprovante_residencia_38_1780611270_6a21f8c631761.jpg', '2026-06-04 19:14:30', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL),
('63', '38', NULL, 'laudo_medico', 'storage/pcd_docs/pcd_doc_laudo_medico_38_1780611270_6a21f8c633b6a.jpg', '2026-06-04 19:14:30', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL),
('64', '39', NULL, 'rg', 'storage/pcd_docs/pcd_doc_rg_39_1780611270_6a21f8c635979.jpg', '2026-06-04 19:14:30', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL),
('65', '39', NULL, 'comprovante_residencia', 'storage/pcd_docs/pcd_doc_comprovante_residencia_39_1780611270_6a21f8c6369fa.jpg', '2026-06-04 19:14:30', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL),
('66', '39', NULL, 'laudo_medico', 'storage/pcd_docs/pcd_doc_laudo_medico_39_1780611270_6a21f8c6377c3.jpg', '2026-06-04 19:14:30', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL),
('67', '40', NULL, 'rg', 'storage/pcd_docs/pcd_doc_rg_40_1780611270_6a21f8c638df0.jpg', '2026-06-04 19:14:30', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL),
('68', '40', NULL, 'comprovante_residencia', 'storage/pcd_docs/pcd_doc_comprovante_residencia_40_1780611270_6a21f8c639be3.jpg', '2026-06-04 19:14:30', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL),
('69', '40', NULL, 'laudo_medico', 'storage/pcd_docs/pcd_doc_laudo_medico_40_1780611270_6a21f8c63aaee.jpg', '2026-06-04 19:14:30', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL),
('70', '41', NULL, 'rg', 'storage/pcd_docs/pcd_doc_rg_41_1780611270_6a21f8c63dcab.jpg', '2026-06-04 19:14:30', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL),
('71', '41', NULL, 'comprovante_residencia', 'storage/pcd_docs/pcd_doc_comprovante_residencia_41_1780611270_6a21f8c63eb17.jpg', '2026-06-04 19:14:30', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL),
('72', '41', NULL, 'laudo_medico', 'storage/pcd_docs/pcd_doc_laudo_medico_41_1780611270_6a21f8c63f7df.jpg', '2026-06-04 19:14:30', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL),
('73', '42', NULL, 'rg', 'storage/pcd_docs/pcd_doc_rg_42_1780611270_6a21f8c64154c.jpg', '2026-06-04 19:14:30', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL),
('74', '42', NULL, 'comprovante_residencia', 'storage/pcd_docs/pcd_doc_comprovante_residencia_42_1780611270_6a21f8c642d03.jpg', '2026-06-04 19:14:30', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL),
('75', '42', NULL, 'laudo_medico', 'storage/pcd_docs/pcd_doc_laudo_medico_42_1780611270_6a21f8c643c05.jpg', '2026-06-04 19:14:30', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL),
('76', '43', NULL, 'rg', 'storage/pcd_docs/pcd_doc_rg_43_1780611270_6a21f8c647a07.jpg', '2026-06-04 19:14:30', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL),
('77', '43', NULL, 'comprovante_residencia', 'storage/pcd_docs/pcd_doc_comprovante_residencia_43_1780611270_6a21f8c648717.jpg', '2026-06-04 19:14:30', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL),
('78', '43', NULL, 'laudo_medico', 'storage/pcd_docs/pcd_doc_laudo_medico_43_1780611270_6a21f8c64947f.jpg', '2026-06-04 19:14:30', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL),
('79', '44', NULL, 'rg', 'storage/pcd_docs/pcd_doc_rg_44_1780611270_6a21f8c64bc35.jpg', '2026-06-04 19:14:30', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL),
('80', '44', NULL, 'comprovante_residencia', 'storage/pcd_docs/pcd_doc_comprovante_residencia_44_1780611270_6a21f8c64cc46.jpg', '2026-06-04 19:14:30', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL),
('81', '44', NULL, 'laudo_medico', 'storage/pcd_docs/pcd_doc_laudo_medico_44_1780611270_6a21f8c64ddf9.jpg', '2026-06-04 19:14:30', '2026-06-04 19:14:30', '2026-06-04 19:14:30', NULL);

-- 9. documentos_publicos
INSERT INTO `documentos_publicos` (`id`, `titulo`, `tipo`, `data_publicacao`, `caminho_arquivo`, `descricao`, `status`, `criado_em`, `atualizado_em`, `deletado_em`) VALUES
('1', 'Samuel Jahu realiza audiência pública sobre acessibilidade urbana', 'ata', '2025-03-15', 'uploads/ata_reuniao_ordinaria_marco_2025.pdf', 'Deliberações sobre acessibilidade nas calçadas da região central e aprovação de novas diretrizes.', 'publicado', '2026-06-01 22:38:59', '2026-06-01 23:11:00', NULL),
('2', 'Ata da Reunião Extraordinária - Fevereiro 2025', 'ata', '2025-02-20', 'uploads/ata_reuniao_extraordinaria_fevereiro_2025.pdf', 'Discussão emergencial sobre o andamento das vistorias nos prédios públicos municipais.', 'publicado', '2026-06-01 22:38:59', '2026-06-01 22:38:59', NULL),
('3', 'Resolução nº 05/2025 - Normas de Acessibilidade', 'resolucao', '2025-03-01', 'uploads/resolucao_05_2025_acessibilidade.pdf', 'Estabelece as normas e especificações técnicas de acessibilidade para estabelecimentos comerciais de Jahu.', 'publicado', '2026-06-01 22:38:59', '2026-06-01 23:11:00', NULL),
('4', 'Resolução nº 04/2025 - Programa de Inclusão', 'resolucao', '2025-02-15', 'uploads/resolucao_04_2025_inclusao.pdf', 'Regulamenta o selo municipal de empresa inclusiva para contratação de pessoas com deficiência.', 'publicado', '2026-06-01 22:38:59', '2026-06-01 22:38:59', NULL),
('5', 'Regimento Interno do CMPCD', 'outros', '2024-06-01', 'uploads/regimento_interno_cmpcd.pdf', 'Regimento interno completo com as competências, organização e atribuições do CMPCD de Jahu.', 'publicado', '2026-06-01 22:38:59', '2026-06-01 23:11:00', NULL),
('6', 'Relatório Anual de Atividades 2024', 'relatorio', '2025-01-30', 'uploads/relatorio_anual_atividades_2024.pdf', 'Relatório consolidando todas as ações, fiscalizações, pareceres e conquistas alcançadas no ano de 2024.', 'publicado', '2026-06-01 22:38:59', '2026-06-01 22:38:59', NULL);

-- 10. membros_conselho
INSERT INTO `membros_conselho` (`id`, `nome_completo`, `foto`, `biografia`, `funcao`, `entidade_representada`, `data_inicio`, `data_fim`, `ativo`, `criado_em`, `atualizado_em`, `deletado_em`) VALUES
('1', 'Dra. Helena Souza', 'uploads/membro_helena.jpg', 'Helena é médica de reabilitação e atua na defesa dos direitos de acessibilidade no município há mais de 10 anos.', 'presidente', 'Sociedade Civil', '2025-01-01', NULL, '1', '2026-06-01 22:12:08', '2026-06-01 22:12:08', NULL),
('2', 'Marcos Silva Junior', 'uploads/membro_marcos.jpg', 'Engenheiro de tráfego urbano focado em projetos de desenho universal e rotas acessíveis para cadeirantes.', 'vice-presidente', 'Secretaria de Trânsito', '2025-01-01', NULL, '1', '2026-06-01 22:12:08', '2026-06-01 22:12:08', NULL),
('3', 'Fernanda Alves Lima', 'uploads/membro_fernanda.jpg', 'Psicopedagoga com foco em educação especial inclusiva e fonoaudiologia infantil na APAE Jahu.', 'secretario', 'APAE Jaú', '2025-01-01', NULL, '1', '2026-06-01 22:12:08', '2026-06-01 23:11:00', NULL),
('4', 'Dr. José Antônio Ferreira', 'uploads/pres_jose.jpg', 'Primeiro presidente do CMPCD Jahu. Responsável pela fundação do conselho e implantação das primeiras políticas de acessibilidade no município, pavimentando o caminho das diretrizes municipais.', 'presidente', 'Sociedade Civil', '2018-01-01', '2021-12-31', '0', '2026-06-01 22:12:08', '2026-06-01 23:11:00', NULL),
('5', 'Marta Regina Souza', 'uploads/pres_marta.jpg', 'Liderou a implantação do cadastro estatístico de PCDs de Jahu, ampliou as parcerias com entidades assistenciais e criou programas de capacitação e inserção no mercado de trabalho.', 'presidente', 'Prefeitura de Jaú', '2022-01-01', '2024-12-31', '0', '2026-06-01 22:12:08', '2026-06-01 23:11:00', NULL),
('6', 'Administrador Geral', 'uploads/content_1780364162_6a1e338283fb7.png', '', 'secretario', 'Fatec', '2022-01-12', NULL, '0', '2026-06-01 22:36:02', '2026-06-04 19:38:51', '2026-06-04 19:38:51');

-- 11. noticias
INSERT INTO `noticias` (`id`, `usuario_id`, `titulo`, `conteudo`, `imagem_capa`, `tema`, `status`, `data_publicacao`, `criado_em`, `atualizado_em`, `deletado_em`) VALUES
('1', '1', 'CMDPCD Jahu realiza audiência pública sobre acessibilidade urbana', '<p>O Conselho Municipal dos Direitos da Pessoa com Deficiência (CMPCD) de Jahu realizou, na última quarta-feira, uma audiência pública de grande importância no plenário da câmara. O evento reuniu representantes da sociedade civil, arquitetos especializados em acessibilidade e representantes do poder público.</p><p>Foram discutidas melhorias fundamentais nas calçadas das regiões comerciais da cidade, incluindo a pavimentação nivelada, implementação correta de piso tátil direcional e de alerta, e rebaixamento de guias para garantir o deslocamento autônomo e seguro das pessoas com deficiência física e visual.</p>', 'uploads/noticia_acessibilidade.jpg', 'acessibilidade', 'publicado', '2026-05-10 14:00:00', '2026-06-01 22:12:08', '2026-06-04 19:23:33', NULL),
('2', '1', 'Campanha de conscientização sobre vagas de estacionamento exclusivas', '<p>Em parceria com a Secretaria de Mobilidade Urbana, o CMPCD Jahu deu início a uma nova campanha de conscientização nos principais pontos comerciais e shoppings do município.</p><p>O objetivo é informar e educar os motoristas sobre a importância do respeito às vagas exclusivas sinalizadas para pessoas com deficiência física e autismo, além de alertar sobre a exigência da credencial visível no painel do carro e o aumento da fiscalização e da aplicação de multas por desobediência.</p>', 'uploads/noticia_vagas.jpg', 'direitos', 'publicado', '2026-05-25 09:30:00', '2026-06-01 22:12:08', '2026-06-01 23:11:00', NULL),
('3', '1', 'Diretrizes para atendimento preferencial na rede pública de saúde', '<p>O conselho deliberou e aprovou uma nova resolução em plenária que estabelece diretrizes rígidas e fiscalizáveis para garantir o atendimento preferencial qualificado às pessoas com deficiência intelectual, sensorial e autismo nas unidades de saúde de Jahu.</p><p>O documento prevê a capacitação contínua de atendentes e profissionais de saúde, além de canais diretos para relatar descumprimentos legais.</p>', 'uploads/noticia_saude.jpg', 'saude', 'publicado', '2026-05-30 11:15:00', '2026-06-01 22:12:08', '2026-06-04 19:23:49', NULL),
('4', '1', 'Parque da Cidade ganha novos brinquedos adaptados e inclusivos', '<p>O Parque da Cidade recebeu nesta semana uma nova área de lazer totalmente adaptada para crianças com deficiência. Os novos brinquedos include balanços adaptados, gira-gira inclusivo e rampas de acesso seguro, garantindo diversão e socialização para todas as famílias do município.</p>', 'uploads/content_1780612041_6a21fbc9ee204.jpg', 'acessibilidade', 'publicado', '2026-06-04 00:00:00', '2026-06-04 19:27:21', '2026-06-04 19:30:15', NULL),
('5', '1', 'Projeto de natação adaptada abre 50 vagas gratuitas em Jahu', '<p>Estão abertas as inscrições para o novo projeto municipal de natação voltado a pessoas com deficiência. As aulas são totalmente gratuitas e acompanhadas por profissionais especializados em educação física adaptada e fisioterapia.</p>', 'uploads/content_1780612041_6a21fbc9f3478.jpg', 'saude', 'publicado', '2026-06-04 00:00:00', '2026-06-04 19:27:22', '2026-06-04 19:30:15', NULL),
('6', '1', 'Empresas locais abrem contratação e capacitação para PCDs', '<p>Uma nova iniciativa da secretaria de desenvolvimento em parceria com o conselho municipal promoveu um fórum de empregabilidade. Mais de dez empresas de médio e grande porte ofereceram vagas e programas de mentoria personalizados.</p>', 'uploads/content_1780612042_6a21fbca0204d.jpg', 'emprego', 'publicado', '2026-06-04 00:00:00', '2026-06-04 19:27:22', '2026-06-04 19:30:15', NULL),
('7', '1', 'Escola municipal é destaque em práticas pedagógicas de educação inclusiva', '<p>A Escola Municipal de Jahu recebeu um prêmio estadual por seu projeto inovador de salas de recursos multifuncionais. A metodologia adapta materiais didáticos em braille e recursos sonoros, integrando plenamente alunos cegos e surdos.</p>', 'uploads/content_1780612042_6a21fbca03607.jpg', 'educacao', 'publicado', '2026-06-04 00:00:00', '2026-06-04 19:27:22', '2026-06-04 19:30:15', NULL),
('8', '1', 'Novo guia digital mapeia pontos turísticos acessíveis na região', '<p>Foi lançado oficialmente o guia digital de turismo acessível da comarca. O portal cataloga hotéis, restaurantes e pontos históricos com acessibilidade arquitetônica e de comunicação, facilitando o turismo inclusivo.</p>', 'uploads/content_1780612042_6a21fbca053ee.jpg', 'acessibilidade', 'publicado', '2026-06-04 00:00:00', '2026-06-04 19:27:22', '2026-06-04 19:38:09', '2026-06-04 19:38:09'),
('9', '1', 'Festival de Arte e Cultura Inclusiva reúne talentos locais no teatro municipal', '<p>No próximo final de semana, o teatro municipal sediará o 1º Festival de Arte Inclusiva. O evento contará com apresentações de dança em cadeiras de rodas, corais de libras e exposições de artes plásticas produzidas por artistas com deficiência.</p>', 'uploads/content_1780612042_6a21fbca06c99.jpg', 'eventos', 'publicado', '2026-06-04 00:00:00', '2026-06-04 19:27:22', '2026-06-04 19:38:15', '2026-06-04 19:38:15'),
('10', '1', 'Mutirão da Saúde realiza exames preventivos e palestras de bem-estar', '<p>No último sábado, o mutirão de saúde promovido pelo CMPCD em parceria com a rede municipal realizou atendimentos preventivos de rotina, aferição de pressão e exames voltados ao bem-estar e cuidados contínuos de pessoas com deficiência.</p>', 'uploads/content_1780612042_6a21fbca0ad0d.jpg', 'saude', 'publicado', '2026-06-04 00:00:00', '2026-06-04 19:27:22', '2026-06-04 19:30:15', NULL);

SET FOREIGN_KEY_CHECKS = 1;
