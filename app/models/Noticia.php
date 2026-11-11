<?php

class Noticia {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Retorna todas as notícias ativas (não deletadas).
     */
    public function all() {
        $stmt = $this->db->query("
            SELECT n.*, u.nome_completo as autor_nome 
            FROM noticias n
            JOIN usuarios u ON n.usuario_id = u.id
            WHERE n.deletado_em IS NULL 
            ORDER BY n.criado_em DESC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Retorna todas as notícias publicadas e cuja data de publicação já passou.
     */
    public function allPublished() {
        $stmt = $this->db->query("
            SELECT n.*, COALESCE(u.nome_completo, 'CMDPCD Jahu') as autor_nome 
            FROM noticias n
            LEFT JOIN usuarios u ON n.usuario_id = u.id
            WHERE n.deletado_em IS NULL 
              AND n.status = 'publicado'
              AND (n.data_publicacao IS NULL OR n.data_publicacao <= NOW())
            ORDER BY COALESCE(n.data_publicacao, n.criado_em) DESC, n.id DESC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Busca uma notícia pelo ID.
     */
    public function find($id) {
        $stmt = $this->db->prepare("
            SELECT n.*, COALESCE(u.nome_completo, 'CMDPCD Jahu') as autor_nome 
            FROM noticias n
            LEFT JOIN usuarios u ON n.usuario_id = u.id
            WHERE n.id = ? AND n.deletado_em IS NULL
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Busca uma notícia publicada pelo ID (público).
     */
    public function findPublished($id) {
        $stmt = $this->db->prepare("
            SELECT n.*, COALESCE(u.nome_completo, 'CMDPCD Jahu') as autor_nome 
            FROM noticias n
            LEFT JOIN usuarios u ON n.usuario_id = u.id
            WHERE n.id = ? 
              AND n.deletado_em IS NULL 
              AND n.status = 'publicado'
              AND (n.data_publicacao IS NULL OR n.data_publicacao <= NOW())
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Cria uma nova notícia.
     */
    public function create($data) {
        $sql = "INSERT INTO noticias (
            usuario_id, titulo, conteudo, imagem_capa, tema, status, data_publicacao
        ) VALUES (
            :usuario_id, :titulo, :conteudo, :imagem_capa, :tema, :status, :data_publicacao
        )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'usuario_id' => $data['usuario_id'],
            'titulo' => $data['titulo'],
            'conteudo' => $data['conteudo'],
            'imagem_capa' => $data['imagem_capa'] ?? null,
            'tema' => $data['tema'],
            'status' => $data['status'] ?? 'rascunho',
            'data_publicacao' => !empty($data['data_publicacao']) ? $data['data_publicacao'] : null
        ]);

        return $this->db->lastInsertId();
    }

    /**
     * Atualiza uma notícia.
     */
    public function update($id, $data) {
        $sql = "UPDATE noticias SET 
            titulo = :titulo,
            conteudo = :conteudo,
            imagem_capa = :imagem_capa,
            tema = :tema,
            status = :status,
            data_publicacao = :data_publicacao
        WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'titulo' => $data['titulo'],
            'conteudo' => $data['conteudo'],
            'imagem_capa' => $data['imagem_capa'], // Passado diretamente do controller (atualizada ou atual)
            'tema' => $data['tema'],
            'status' => $data['status'],
            'data_publicacao' => !empty($data['data_publicacao']) ? $data['data_publicacao'] : null
        ]);
    }

    /**
     * Realiza soft delete de uma notícia.
     */
    public function delete($id) {
        $stmt = $this->db->prepare("UPDATE noticias SET deletado_em = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
