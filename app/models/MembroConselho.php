<?php

class MembroConselho {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Retorna todos os membros ativos e históricos (não deletados).
     */
    public function all() {
        $stmt = $this->db->query("
            SELECT * FROM membros_conselho 
            WHERE deletado_em IS NULL 
            ORDER BY ativo DESC, 
              CASE funcao
                WHEN 'presidente' THEN 1
                WHEN 'vice-presidente' THEN 2
                WHEN 'secretario' THEN 3
                WHEN 'titular' THEN 4
                WHEN 'suplente' THEN 5
                ELSE 6
              END ASC,
              nome_completo ASC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Retorna apenas os membros atuais ativos.
     */
    public function allActive() {
        $stmt = $this->db->query("
            SELECT * FROM membros_conselho 
            WHERE deletado_em IS NULL AND ativo = 1
            ORDER BY 
              CASE funcao
                WHEN 'presidente' THEN 1
                WHEN 'vice-presidente' THEN 2
                WHEN 'secretario' THEN 3
                WHEN 'titular' THEN 4
                WHEN 'suplente' THEN 5
                ELSE 6
              END ASC,
              nome_completo ASC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Retorna todos os presidentes na história do conselho (ativos ou inativos).
     */
    public function allPresidents() {
        $stmt = $this->db->query("
            SELECT * FROM membros_conselho 
            WHERE deletado_em IS NULL AND funcao = 'presidente'
            ORDER BY data_inicio DESC, nome_completo ASC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Busca um membro pelo ID.
     */
    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM membros_conselho WHERE id = ? AND deletado_em IS NULL");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Cria um novo membro.
     */
    public function create($data) {
        $sql = "INSERT INTO membros_conselho (
            nome_completo, foto, biografia, funcao, entidade_representada, data_inicio, data_fim, ativo
        ) VALUES (
            :nome_completo, :foto, :biografia, :funcao, :entidade_representada, :data_inicio, :data_fim, :ativo
        )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'nome_completo' => $data['nome_completo'],
            'foto' => $data['foto'] ?? null,
            'biografia' => $data['biografia'] ?? null,
            'funcao' => $data['funcao'],
            'entidade_representada' => $data['entidade_representada'] ?? null,
            'data_inicio' => $data['data_inicio'],
            'data_fim' => !empty($data['data_fim']) ? $data['data_fim'] : null,
            'ativo' => isset($data['ativo']) ? (int)$data['ativo'] : 1
        ]);

        return $this->db->lastInsertId();
    }

    /**
     * Atualiza um membro.
     */
    public function update($id, $data) {
        $sql = "UPDATE membros_conselho SET 
            nome_completo = :nome_completo,
            foto = :foto,
            biografia = :biografia,
            funcao = :funcao,
            entidade_representada = :entidade_representada,
            data_inicio = :data_inicio,
            data_fim = :data_fim,
            ativo = :ativo
        WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'nome_completo' => $data['nome_completo'],
            'foto' => $data['foto'], // Mantido ou atualizado pelo controller
            'biografia' => $data['biografia'] ?? null,
            'funcao' => $data['funcao'],
            'entidade_representada' => $data['entidade_representada'] ?? null,
            'data_inicio' => $data['data_inicio'],
            'data_fim' => !empty($data['data_fim']) ? $data['data_fim'] : null,
            'ativo' => isset($data['ativo']) ? (int)$data['ativo'] : 1
        ]);
    }

    /**
     * Realiza soft delete de um membro.
     */
    public function delete($id) {
        $stmt = $this->db->prepare("UPDATE membros_conselho SET deletado_em = NOW(), ativo = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
