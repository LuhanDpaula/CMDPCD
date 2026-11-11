<?php

class Usuario {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Retorna todos os usuários ativos (não deletados).
     */
    public function all() {
        $stmt = $this->db->query("
            SELECT u.id, u.perfil_id, u.nome_completo, u.cpf, u.celular, u.email, u.status,
                   u.ultimo_login, u.criado_em, p.nome as perfil_nome 
            FROM usuarios u
            JOIN perfis_acesso p ON u.perfil_id = p.id
            WHERE u.deletado_em IS NULL
            ORDER BY u.nome_completo ASC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Busca um usuário pelo ID.
     */
    public function find($id) {
        $stmt = $this->db->prepare("
            SELECT u.*, p.nome as perfil_nome 
            FROM usuarios u
            JOIN perfis_acesso p ON u.perfil_id = p.id
            WHERE u.id = ? AND u.deletado_em IS NULL
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Busca um usuário por E-mail ou CPF para autenticação.
     */
    public function findByEmailOrCpf($login) {
        // Limpa formatação do CPF para buscar
        $cleanLogin = preg_replace('/\D/', '', $login);
        
        $stmt = $this->db->prepare("
            SELECT u.*, p.nome as perfil_nome 
            FROM usuarios u
            JOIN perfis_acesso p ON u.perfil_id = p.id
            WHERE (u.email = :login OR u.cpf = :cpf) AND u.deletado_em IS NULL
        ");
        $stmt->execute([
            'login' => $login,
            'cpf' => $cleanLogin
        ]);
        return $stmt->fetch();
    }

    /**
     * Cria um novo usuário.
     */
    public function create($data) {
        // Sanitiza CPF
        $data['cpf'] = preg_replace('/\D/', '', $data['cpf']);
        $data['senha'] = password_hash($data['senha'], PASSWORD_DEFAULT);

        $stmt = $this->db->prepare("
            INSERT INTO usuarios (perfil_id, nome_completo, cpf, celular, email, senha, status) 
            VALUES (:perfil_id, :nome_completo, :cpf, :celular, :email, :senha, :status)
        ");
        
        return $stmt->execute([
            'perfil_id' => $data['perfil_id'],
            'nome_completo' => $data['nome_completo'],
            'cpf' => $data['cpf'],
            'celular' => $data['celular'],
            'email' => $data['email'],
            'senha' => $data['senha'],
            'status' => $data['status'] ?? 'ativo'
        ]);
    }

    /**
     * Atualiza um usuário existente.
     */
    public function update($id, $data) {
        $data['cpf'] = preg_replace('/\D/', '', $data['cpf']);
        
        // Se a senha foi fornecida, atualiza a senha
        if (!empty($data['senha'])) {
            $data['senha'] = password_hash($data['senha'], PASSWORD_DEFAULT);
            $sql = "UPDATE usuarios SET 
                        perfil_id = :perfil_id, 
                        nome_completo = :nome_completo, 
                        cpf = :cpf, 
                        celular = :celular, 
                        email = :email, 
                        senha = :senha, 
                        status = :status 
                    WHERE id = :id";
        } else {
            // Se não, mantém a senha atual
            $sql = "UPDATE usuarios SET 
                        perfil_id = :perfil_id, 
                        nome_completo = :nome_completo, 
                        cpf = :cpf, 
                        celular = :celular, 
                        email = :email, 
                        status = :status 
                    WHERE id = :id";
            unset($data['senha']);
        }

        $stmt = $this->db->prepare($sql);
        $data['id'] = $id;
        return $stmt->execute($data);
    }

    /**
     * Realiza soft delete de um usuário.
     */
    public function delete($id) {
        $stmt = $this->db->prepare("UPDATE usuarios SET deletado_em = NOW(), status = 'inativo' WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Atualiza somente o hash da senha (usado no rehash automático).
     */
    public function updatePasswordHash($id, $hash) {
        $stmt = $this->db->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
        return $stmt->execute([$hash, $id]);
    }

    /**
     * Atualiza a data e hora do último login.
     */
    public function updateUltimoLogin($id) {
        $stmt = $this->db->prepare("UPDATE usuarios SET ultimo_login = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Verifica se o CPF já está cadastrado para outro usuário.
     */
    public function existsCpf($cpf, $excludeId = null) {
        $cpf = preg_replace('/\D/', '', $cpf);
        $sql = "SELECT COUNT(*) FROM usuarios WHERE cpf = ? AND deletado_em IS NULL";
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
     * Verifica se o E-mail já está cadastrado para outro usuário.
     */
    public function existsEmail($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM usuarios WHERE email = ? AND deletado_em IS NULL";
        $params = [$email];
        
        if ($excludeId !== null) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }
}
