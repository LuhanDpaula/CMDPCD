<?php

class Pcd {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Retorna todos os PCDs ativos (não deletados).
     */
    public function all() {
        $stmt = $this->db->query("
            SELECT p.*, GROUP_CONCAT(td.nome SEPARATOR ', ') as deficiencias
            FROM pcds p
            LEFT JOIN pcd_deficiencias pd ON p.id = pd.pcd_id
            LEFT JOIN tipos_deficiencia td ON pd.tipo_deficiencia_id = td.id
            WHERE p.deletado_em IS NULL 
            GROUP BY p.id
            ORDER BY p.nome_completo ASC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Busca um PCD pelo ID, incluindo deficiências e responsável.
     */
    public function find($id) {
        // Busca dados básicos do PCD
        $stmt = $this->db->prepare("SELECT * FROM pcds WHERE id = ? AND deletado_em IS NULL");
        $stmt->execute([$id]);
        $pcd = $stmt->fetch();

        if ($pcd) {
            // Busca deficiências associadas
            $stmtDef = $this->db->prepare("
                SELECT td.id, td.nome 
                FROM pcd_deficiencias pd
                JOIN tipos_deficiencia td ON pd.tipo_deficiencia_id = td.id
                WHERE pd.pcd_id = ?
            ");
            $stmtDef->execute([$id]);
            $pcd['deficiencias'] = $stmtDef->fetchAll();

            // Busca responsável legal associado
            $stmtResp = $this->db->prepare("
                SELECT rl.* 
                FROM pcd_responsaveis pr
                JOIN responsaveis_legais rl ON pr.responsavel_id = rl.id
                WHERE pr.pcd_id = ? AND rl.deletado_em IS NULL
            ");
            $stmtResp->execute([$id]);
            $pcd['responsavel'] = $stmtResp->fetch();

            // Busca documentos associados
            $stmtDocs = $this->db->prepare("
                SELECT * 
                FROM documentos_pcd 
                WHERE pcd_id = ? AND deletado_em IS NULL
            ");
            $stmtDocs->execute([$id]);
            $pcd['documentos'] = $stmtDocs->fetchAll();
        }

        return $pcd;
    }

    /**
     * Cria um novo PCD no banco com transação.
     */
    public function create($data) {
        try {
            $this->db->beginTransaction();

            // 1. Limpa formatação de CPF e CEP e normaliza CID array
            $cpf = preg_replace('/\D/', '', $data['cpf']);
            $cep = preg_replace('/\D/', '', $data['cep']);
            $rg = preg_replace('/\D/', '', $data['rg']);

            if (isset($data['cid']) && is_array($data['cid'])) {
                $data['cid'] = implode(', ', array_filter(array_map('trim', $data['cid'])));
            }

            // 2. Insere na tabela pcds
            $sql = "INSERT INTO pcds (
                nome_completo, nome_social, cpf, rg, data_nascimento, sexo_biologico, genero, estado_civil, raca_cor, nacionalidade,
                cep, logradouro, numero, complemento, bairro, cidade, uf,
                telefone_principal, telefone_secundario, email, contato_emergencia_nome, contato_emergencia_telefone,
                renda_familiar, numero_dependentes, situacao_habitacional, escolaridade, ocupacao_atual,
                recebe_bpc_loas, beneficio_bpc_loas, grau_deficiencia, cid, data_diagnostico, tecnologia_assistiva,
                necessita_acompanhante, medicacao_continua, status
            ) VALUES (
                :nome_completo, :nome_social, :cpf, :rg, :data_nascimento, :sexo_biologico, :genero, :estado_civil, :raca_cor, :nacionalidade,
                :cep, :logradouro, :numero, :complemento, :bairro, :cidade, :uf,
                :telefone_principal, :telefone_secundario, :email, :contato_emergencia_nome, :contato_emergencia_telefone,
                :renda_familiar, :numero_dependentes, :situacao_habitacional, :escolaridade, :ocupacao_atual,
                :recebe_bpc_loas, :beneficio_bpc_loas, :grau_deficiencia, :cid, :data_diagnostico, :tecnologia_assistiva,
                :necessita_acompanhante, :medicacao_continua, :status
            )";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'nome_completo' => $data['nome_completo'],
                'nome_social' => $data['nome_social'] ?? null,
                'cpf' => $cpf,
                'rg' => $rg,
                'data_nascimento' => $data['data_nascimento'],
                'sexo_biologico' => strtolower($data['sexo_biologico']),
                'genero' => $data['genero'] ?? null,
                'estado_civil' => $data['estado_civil'] ?? null,
                'raca_cor' => $data['raca_cor'],
                'nacionalidade' => $data['nacionalidade'],
                'cep' => $cep,
                'logradouro' => $data['logradouro'],
                'numero' => $data['numero'],
                'complemento' => $data['complemento'] ?? null,
                'bairro' => $data['bairro'],
                'cidade' => $data['cidade'],
                'uf' => $data['uf'],
                'telefone_principal' => $data['telefone_principal'],
                'telefone_secundario' => $data['telefone_secundario'] ?? null,
                'email' => $data['email'] ?? null,
                'contato_emergencia_nome' => $data['contato_emergencia_nome'],
                'contato_emergencia_telefone' => $data['contato_emergencia_telefone'],
                'renda_familiar' => floatval($data['renda_familiar']),
                'numero_dependentes' => intval($data['numero_dependentes']),
                'situacao_habitacional' => $data['situacao_habitacional'],
                'escolaridade' => $data['escolaridade'],
                'ocupacao_atual' => $data['ocupacao_atual'] ?? null,
                'recebe_bpc_loas' => !empty($data['recebe_bpc_loas']) ? 1 : 0,
                'beneficio_bpc_loas' => $data['beneficio_bpc_loas'] ?? null,
                'grau_deficiencia' => $data['grau_deficiencia'] ?? null,
                'cid' => $data['cid'] ?? null,
                'data_diagnostico' => !empty($data['data_diagnostico']) ? $data['data_diagnostico'] : null,
                'tecnologia_assistiva' => $data['tecnologia_assistiva'] ?? null,
                'necessita_acompanhante' => !empty($data['necessita_acompanhante']) ? 1 : 0,
                'medicacao_continua' => $data['medicacao_continua'] ?? null,
                'status' => $data['status'] ?? 'pendente',
            ]);

            $pcdId = $this->db->lastInsertId();

            // 3. Associa Responsável Legal se fornecido
            if (!empty($data['possui_responsavel']) && !empty($data['responsavel_nome'])) {
                $respCpf = preg_replace('/\D/', '', $data['responsavel_cpf']);
                $respRg = preg_replace('/\D/', '', $data['responsavel_rg']);

                // Insere responsável legal
                $stmtResp = $this->db->prepare("INSERT INTO responsaveis_legais (
                    nome_completo, cpf, rg, data_nascimento, parentesco, telefone, email, responsavel_formal
                ) VALUES (
                    :nome, :cpf, :rg, :data_nascimento, :parentesco, :telefone, :email, :formal
                )");
                
                $stmtResp->execute([
                    'nome' => $data['responsavel_nome'],
                    'cpf' => $respCpf,
                    'rg' => $respRg,
                    'data_nascimento' => $data['responsavel_data_nascimento'],
                    'parentesco' => $data['responsavel_parentesco'],
                    'telefone' => $data['responsavel_telefone'],
                    'email' => $data['responsavel_email'] ?? null,
                    'formal' => !empty($data['responsavel_formal']) ? 1 : 0
                ]);
                
                $respId = $this->db->lastInsertId();

                // Associa na tabela intermediária
                $stmtLink = $this->db->prepare("INSERT INTO pcd_responsaveis (pcd_id, responsavel_id) VALUES (?, ?)");
                $stmtLink->execute([$pcdId, $respId]);
            }

            // 4. Associa as Deficiências selecionadas
            if (!empty($data['deficiencias_ids'])) {
                $stmtDef = $this->db->prepare("INSERT INTO pcd_deficiencias (pcd_id, tipo_deficiencia_id) VALUES (?, ?)");
                foreach ($data['deficiencias_ids'] as $defId) {
                    $stmtDef->execute([$pcdId, $defId]);
                }
            }

            $this->db->commit();
            return $pcdId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Atualiza um PCD existente com transação.
     */
    public function update($id, $data) {
        try {
            $this->db->beginTransaction();

            $cpf = preg_replace('/\D/', '', $data['cpf']);
            $cep = preg_replace('/\D/', '', $data['cep']);
            $rg = preg_replace('/\D/', '', $data['rg']);

            if (isset($data['cid']) && is_array($data['cid'])) {
                $data['cid'] = implode(', ', array_filter(array_map('trim', $data['cid'])));
            }

            // 1. Atualiza pcds
            $sql = "UPDATE pcds SET 
                nome_completo = :nome_completo,
                nome_social = :nome_social,
                cpf = :cpf,
                rg = :rg,
                data_nascimento = :data_nascimento,
                sexo_biologico = :sexo_biologico,
                genero = :genero,
                estado_civil = :estado_civil,
                raca_cor = :raca_cor,
                nacionalidade = :nacionalidade,
                cep = :cep,
                logradouro = :logradouro,
                numero = :numero,
                complemento = :complemento,
                bairro = :bairro,
                cidade = :cidade,
                uf = :uf,
                telefone_principal = :telefone_principal,
                telefone_secundario = :telefone_secundario,
                email = :email,
                contato_emergencia_nome = :contato_emergencia_nome,
                contato_emergencia_telefone = :contato_emergencia_telefone,
                renda_familiar = :renda_familiar,
                numero_dependentes = :numero_dependentes,
                situacao_habitacional = :situacao_habitacional,
                escolaridade = :escolaridade,
                ocupacao_atual = :ocupacao_atual,
                recebe_bpc_loas = :recebe_bpc_loas,
                beneficio_bpc_loas = :beneficio_bpc_loas,
                grau_deficiencia = :grau_deficiencia,
                cid = :cid,
                data_diagnostico = :data_diagnostico,
                tecnologia_assistiva = :tecnologia_assistiva,
                necessita_acompanhante = :necessita_acompanhante,
                medicacao_continua = :medicacao_continua,
                status = :status
            WHERE id = :id";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'id' => $id,
                'nome_completo' => $data['nome_completo'],
                'nome_social' => $data['nome_social'] ?? null,
                'cpf' => $cpf,
                'rg' => $rg,
                'data_nascimento' => $data['data_nascimento'],
                'sexo_biologico' => strtolower($data['sexo_biologico']),
                'genero' => $data['genero'] ?? null,
                'estado_civil' => $data['estado_civil'] ?? null,
                'raca_cor' => $data['raca_cor'],
                'nacionalidade' => $data['nacionalidade'],
                'cep' => $cep,
                'logradouro' => $data['logradouro'],
                'numero' => $data['numero'],
                'complemento' => $data['complemento'] ?? null,
                'bairro' => $data['bairro'],
                'cidade' => $data['cidade'],
                'uf' => $data['uf'],
                'telefone_principal' => $data['telefone_principal'],
                'telefone_secundario' => $data['telefone_secundario'] ?? null,
                'email' => $data['email'] ?? null,
                'contato_emergencia_nome' => $data['contato_emergencia_nome'],
                'contato_emergencia_telefone' => $data['contato_emergencia_telefone'],
                'renda_familiar' => floatval($data['renda_familiar']),
                'numero_dependentes' => intval($data['numero_dependentes']),
                'situacao_habitacional' => $data['situacao_habitacional'],
                'escolaridade' => $data['escolaridade'],
                'ocupacao_atual' => $data['ocupacao_atual'] ?? null,
                'recebe_bpc_loas' => !empty($data['recebe_bpc_loas']) ? 1 : 0,
                'beneficio_bpc_loas' => $data['beneficio_bpc_loas'] ?? null,
                'grau_deficiencia' => $data['grau_deficiencia'] ?? null,
                'cid' => $data['cid'] ?? null,
                'data_diagnostico' => !empty($data['data_diagnostico']) ? $data['data_diagnostico'] : null,
                'tecnologia_assistiva' => $data['tecnologia_assistiva'] ?? null,
                'necessita_acompanhante' => !empty($data['necessita_acompanhante']) ? 1 : 0,
                'medicacao_continua' => $data['medicacao_continua'] ?? null,
                'status' => $data['status'] ?? 'deferido',
            ]);

            // 2. Atualiza Responsável Legal (Remocao completa de responsavel anterior se desmarcado, ou atualizacao/criacao)
            // Primeiro busca o link atual
            $stmtCheck = $this->db->prepare("SELECT responsavel_id FROM pcd_responsaveis WHERE pcd_id = ?");
            $stmtCheck->execute([$id]);
            $respId = $stmtCheck->fetchColumn();

            if (!empty($data['possui_responsavel']) && !empty($data['responsavel_nome'])) {
                $respCpf = preg_replace('/\D/', '', $data['responsavel_cpf']);
                $respRg = preg_replace('/\D/', '', $data['responsavel_rg']);

                if ($respId) {
                    // Atualiza responsável existente
                    $stmtResp = $this->db->prepare("UPDATE responsaveis_legais SET 
                        nome_completo = :nome, cpf = :cpf, rg = :rg, data_nascimento = :data_nascimento, 
                        parentesco = :parentesco, telefone = :telefone, email = :email, responsavel_formal = :formal 
                        WHERE id = :id");
                    $stmtResp->execute([
                        'id' => $respId,
                        'nome' => $data['responsavel_nome'],
                        'cpf' => $respCpf,
                        'rg' => $respRg,
                        'data_nascimento' => $data['responsavel_data_nascimento'],
                        'parentesco' => $data['responsavel_parentesco'],
                        'telefone' => $data['responsavel_telefone'],
                        'email' => $data['responsavel_email'] ?? null,
                        'formal' => !empty($data['responsavel_formal']) ? 1 : 0
                    ]);
                } else {
                    // Cria novo responsável
                    $stmtResp = $this->db->prepare("INSERT INTO responsaveis_legais (
                        nome_completo, cpf, rg, data_nascimento, parentesco, telefone, email, responsavel_formal
                    ) VALUES (
                        :nome, :cpf, :rg, :data_nascimento, :parentesco, :telefone, :email, :formal
                    )");
                    $stmtResp->execute([
                        'nome' => $data['responsavel_nome'],
                        'cpf' => $respCpf,
                        'rg' => $respRg,
                        'data_nascimento' => $data['responsavel_data_nascimento'],
                        'parentesco' => $data['responsavel_parentesco'],
                        'telefone' => $data['responsavel_telefone'],
                        'email' => $data['responsavel_email'] ?? null,
                        'formal' => !empty($data['responsavel_formal']) ? 1 : 0
                    ]);
                    $newRespId = $this->db->lastInsertId();
                    
                    $stmtLink = $this->db->prepare("INSERT INTO pcd_responsaveis (pcd_id, responsavel_id) VALUES (?, ?)");
                    $stmtLink->execute([$id, $newRespId]);
                }
            } else {
                // Se o responsável legal foi desmarcado, removemos a associação
                if ($respId) {
                    $this->db->prepare("DELETE FROM pcd_responsaveis WHERE pcd_id = ?")->execute([$id]);
                    // Realiza soft delete do responsavel
                    $this->db->prepare("UPDATE responsaveis_legais SET deletado_em = NOW() WHERE id = ?")->execute([$respId]);
                }
            }

            // 3. Atualiza Deficiências: deleta as antigas e insere as novas
            $this->db->prepare("DELETE FROM pcd_deficiencias WHERE pcd_id = ?")->execute([$id]);
            if (!empty($data['deficiencias_ids'])) {
                $stmtDef = $this->db->prepare("INSERT INTO pcd_deficiencias (pcd_id, tipo_deficiencia_id) VALUES (?, ?)");
                foreach ($data['deficiencias_ids'] as $defId) {
                    $stmtDef->execute([$id, $defId]);
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Realiza soft delete de um PCD.
     */
    public function delete($id) {
        $stmt = $this->db->prepare("UPDATE pcds SET deletado_em = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Verifica se o CPF já está cadastrado para outro PCD.
     */
    public function existsCpf($cpf, $excludeId = null) {
        $cpf = preg_replace('/\D/', '', $cpf);
        $sql = "SELECT COUNT(*) FROM pcds WHERE cpf = ? AND deletado_em IS NULL";
        $params = [$cpf];
        
        if ($excludeId !== null) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Obtém os tipos de deficiência disponíveis no banco.
     */
    public function getTiposDeficiencia() {
        $stmt = $this->db->query("SELECT * FROM tipos_deficiencia ORDER BY nome ASC");
        return $stmt->fetchAll();
    }

    /**
     * Insere um registro na tabela documentos_pcd.
     */
    public function addDocument($pcdId, $userId, $type, $filePath) {
        $stmt = $this->db->prepare("
            INSERT INTO documentos_pcd (pcd_id, usuario_upload_id, tipo_documento, caminho_arquivo) 
            VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute([$pcdId, $userId ?: null, $type, $filePath]);
    }

    /**
     * Busca um documento ativo pelo ID (usado na rota protegida de download).
     */
    public function findDocument($docId) {
        $stmt = $this->db->prepare("
            SELECT d.* FROM documentos_pcd d
            JOIN pcds p ON p.id = d.pcd_id
            WHERE d.id = ? AND d.deletado_em IS NULL AND p.deletado_em IS NULL
        ");
        $stmt->execute([$docId]);
        return $stmt->fetch();
    }

    /**
     * Marca documentos de um determinado tipo do PCD como deletados.
     */
    public function softDeleteDocumentByType($pcdId, $type) {
        $stmt = $this->db->prepare("
            UPDATE documentos_pcd 
            SET deletado_em = NOW() 
            WHERE pcd_id = ? AND tipo_documento = ? AND deletado_em IS NULL
        ");
        return $stmt->execute([$pcdId, $type]);
    }

    /**
     * Retorna estatísticas gerais para a página inicial baseadas no banco.
     */
    public function getHomeStats() {
        $total = $this->db->query("SELECT COUNT(*) FROM pcds WHERE status = 'deferido' AND deletado_em IS NULL")->fetchColumn();
        $bpc = $this->db->query("SELECT COUNT(*) FROM pcds WHERE recebe_bpc_loas = 1 AND status = 'deferido' AND deletado_em IS NULL")->fetchColumn();
        $tecnologia = $this->db->query("SELECT COUNT(*) FROM pcds WHERE tecnologia_assistiva IS NOT NULL AND tecnologia_assistiva != '' AND status = 'deferido' AND deletado_em IS NULL")->fetchColumn();
        
        return [
            'total_pcds' => $total,
            'total_bpc' => $bpc,
            'total_tecnologia' => $tecnologia
        ];
    }

    /**
     * Retorna estatísticas detalhadas para a aba administrativa de Estatísticas.
     */
    public function getDetailedStats() {
        // 1. Totais básicos
        $total = (int)$this->db->query("SELECT COUNT(*) FROM pcds WHERE status = 'deferido' AND deletado_em IS NULL")->fetchColumn();
        $bpc = (int)$this->db->query("SELECT COUNT(*) FROM pcds WHERE recebe_bpc_loas = 1 AND status = 'deferido' AND deletado_em IS NULL")->fetchColumn();
        $tecnologia = (int)$this->db->query("SELECT COUNT(*) FROM pcds WHERE tecnologia_assistiva IS NOT NULL AND tecnologia_assistiva != '' AND tecnologia_assistiva != 'Nenhuma' AND status = 'deferido' AND deletado_em IS NULL")->fetchColumn();
        $entidades = (int)$this->db->query("SELECT COUNT(*) FROM pcds WHERE origem_cadastro_id IS NOT NULL AND status = 'deferido' AND deletado_em IS NULL")->fetchColumn();
        
        $defQuery = $this->db->query("
            SELECT td.nome, COUNT(p.id) as total 
            FROM tipos_deficiencia td
            JOIN pcd_deficiencias pd ON td.id = pd.tipo_deficiencia_id
            JOIN pcds p ON pd.pcd_id = p.id
            WHERE p.status = 'deferido' AND p.deletado_em IS NULL
            GROUP BY td.id, td.nome
            ORDER BY total DESC
        ");
        $defStats = $defQuery->fetchAll(PDO::FETCH_ASSOC);

        // 3. Faixa Etária
        $ageQuery = $this->db->query("
            SELECT 
              SUM(CASE WHEN TIMESTAMPDIFF(YEAR, data_nascimento, CURDATE()) < 18 THEN 1 ELSE 0 END) as '0-17',
              SUM(CASE WHEN TIMESTAMPDIFF(YEAR, data_nascimento, CURDATE()) BETWEEN 18 AND 29 THEN 1 ELSE 0 END) as '18-29',
              SUM(CASE WHEN TIMESTAMPDIFF(YEAR, data_nascimento, CURDATE()) BETWEEN 30 AND 44 THEN 1 ELSE 0 END) as '30-44',
              SUM(CASE WHEN TIMESTAMPDIFF(YEAR, data_nascimento, CURDATE()) BETWEEN 45 AND 59 THEN 1 ELSE 0 END) as '45-59',
              SUM(CASE WHEN TIMESTAMPDIFF(YEAR, data_nascimento, CURDATE()) >= 60 THEN 1 ELSE 0 END) as '60+'
            FROM pcds
            WHERE status = 'deferido' AND deletado_em IS NULL
        ");
        $ageStats = $ageQuery->fetch(PDO::FETCH_ASSOC);

        // 4. Escolaridade
        $schoolQuery = $this->db->query("
            SELECT escolaridade, COUNT(*) as total
            FROM pcds
            WHERE status = 'deferido' AND deletado_em IS NULL
            GROUP BY escolaridade
        ");
        $schoolStatsRaw = $schoolQuery->fetchAll(PDO::FETCH_ASSOC);

        $schoolLabelsMap = [
            'sem_instrucao' => 'Não alfab.',
            'fundamental_incompleto' => 'Fund. Incomp.',
            'fundamental_completo' => 'Fund. Comp.',
            'medio_incompleto' => 'Médio Incomp.',
            'medio_completo' => 'Médio Comp.',
            'superior_incompleto' => 'Superior Incomp.',
            'superior_completo' => 'Superior Comp.'
        ];
        
        $schoolStats = [];
        foreach ($schoolLabelsMap as $key => $label) {
            $schoolStats[$label] = 0;
        }
        foreach ($schoolStatsRaw as $row) {
            $label = $schoolLabelsMap[$row['escolaridade']] ?? $row['escolaridade'];
            $schoolStats[$label] = (int)$row['total'];
        }

        // 5. Por Bairro/Região (Top 6 e Outros)
        $bairroQuery = $this->db->query("
            SELECT bairro, COUNT(*) as total
            FROM pcds
            WHERE status = 'deferido' AND deletado_em IS NULL
            GROUP BY bairro
            ORDER BY total DESC
        ");
        $bairroStatsRaw = $bairroQuery->fetchAll(PDO::FETCH_ASSOC);

        $bairroStats = [];
        $outrosCount = 0;
        foreach ($bairroStatsRaw as $idx => $row) {
            if ($idx < 6) {
                $bairroStats[$row['bairro']] = (int)$row['total'];
            } else {
                $outrosCount += (int)$row['total'];
            }
        }
        if ($outrosCount > 0) {
            $bairroStats['Outros'] = $outrosCount;
        }

        return [
            'total_pcds' => $total,
            'total_bpc' => $bpc,
            'total_tecnologia' => $tecnologia,
            'total_entidades' => $entidades,
            'deficiencias' => $defStats,
            'idades' => $ageStats,
            'escolaridade' => $schoolStats,
            'bairros' => $bairroStats
        ];
    }

    /**
     * Atualiza o status de um PCD.
     */
    public function updateStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE pcds SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    /**
     * Retorna a contagem de registros pendentes de moderação.
     */
    public function getPendingCount() {
        $stmt = $this->db->query("SELECT COUNT(*) FROM pcds WHERE status = 'pendente' AND deletado_em IS NULL");
        return (int)$stmt->fetchColumn();
    }

    /**
     * Retorna a contagem de cadastros de PCDs deferidos (aprovados).
     */
    public function countApproved() {
        $stmt = $this->db->query("SELECT COUNT(*) FROM pcds WHERE status = 'deferido' AND deletado_em IS NULL");
        return (int)$stmt->fetchColumn();
    }
}
