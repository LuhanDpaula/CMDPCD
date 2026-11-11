<?php

class Contato {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Insere uma nova mensagem de contato no banco de dados.
     */
    public function create($data) {
        $sql = "INSERT INTO forms_contato (nome, email, telefone, assunto, mensagem, lido) 
                VALUES (:nome, :email, :telefone, :assunto, :mensagem, :lido)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'nome' => $data['nome'],
            'email' => $data['email'],
            'telefone' => $data['telefone'] ?? null,
            'assunto' => $data['assunto'],
            'mensagem' => $data['mensagem'],
            'lido' => 0
        ]);
    }

    /**
     * Retorna todas as mensagens enviadas ordenadas pela data de criação.
     */
    public function all() {
        $stmt = $this->db->query("SELECT * FROM forms_contato ORDER BY criado_em DESC");
        return $stmt->fetchAll();
    }

    /**
     * Retorna uma mensagem específica pelo ID.
     */
    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM forms_contato WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Marca uma mensagem como lida.
     */
    public function markAsRead($id) {
        $stmt = $this->db->prepare("UPDATE forms_contato SET lido = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Exclui fisicamente uma mensagem do banco.
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM forms_contato WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Retorna o total de mensagens não lidas.
     */
    public function getUnreadCount() {
        $stmt = $this->db->query("SELECT COUNT(*) FROM forms_contato WHERE lido = 0");
        return (int)$stmt->fetchColumn();
    }
}
