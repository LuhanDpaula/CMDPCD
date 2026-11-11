<?php

class DocumentoPublico {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Retorna todos os documentos não excluídos.
     */
    public function all() {
        $stmt = $this->db->query("
            SELECT * FROM documentos_publicos 
            WHERE deletado_em IS NULL 
            ORDER BY data_publicacao DESC, criado_em DESC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Retorna todos os documentos publicados ativos.
     */
    public function allPublished() {
        $stmt = $this->db->query("
            SELECT * FROM documentos_publicos 
            WHERE deletado_em IS NULL 
              AND status = 'publicado' 
            ORDER BY data_publicacao DESC, criado_em DESC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Busca um documento pelo ID.
     */
    public function find($id) {
        $stmt = $this->db->prepare("
            SELECT * FROM documentos_publicos 
            WHERE id = ? AND deletado_em IS NULL
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Cria um novo documento público.
     */
    public function create($data) {
        $sql = "INSERT INTO documentos_publicos (
            titulo, tipo, data_publicacao, caminho_arquivo, descricao, status
        ) VALUES (
            :titulo, :tipo, :data_publicacao, :caminho_arquivo, :descricao, :status
        )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'titulo' => $data['titulo'],
            'tipo' => $data['tipo'],
            'data_publicacao' => $data['data_publicacao'],
            'caminho_arquivo' => $data['caminho_arquivo'],
            'descricao' => $data['descricao'] ?? null,
            'status' => $data['status'] ?? 'publicado'
        ]);

        return $this->db->lastInsertId();
    }

    /**
     * Atualiza um documento público.
     */
    public function update($id, $data) {
        $sql = "UPDATE documentos_publicos SET 
            titulo = :titulo,
            tipo = :tipo,
            data_publicacao = :data_publicacao,
            caminho_arquivo = :caminho_arquivo,
            descricao = :descricao,
            status = :status
        WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'titulo' => $data['titulo'],
            'tipo' => $data['tipo'],
            'data_publicacao' => $data['data_publicacao'],
            'caminho_arquivo' => $data['caminho_arquivo'],
            'descricao' => $data['descricao'] ?? null,
            'status' => $data['status']
        ]);
    }

    /**
     * Realiza soft delete de um documento público.
     */
    public function delete($id) {
        $stmt = $this->db->prepare("UPDATE documentos_publicos SET deletado_em = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
