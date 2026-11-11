<?php

class MensagemController {
    private $contatoModel;

    public function __construct() {
        checkAuth(); // Protege todas as ações deste controller
        $this->contatoModel = new Contato();
    }

    /**
     * Lista todas as mensagens.
     */
    public function index() {
        checkPermission(['admin_total', 'admin_parcial']);
        $mensagens = $this->contatoModel->all();
        
        $success = $_SESSION['success_msg'] ?? null;
        $error = $_SESSION['error_msg'] ?? null;
        unset($_SESSION['success_msg'], $_SESSION['error_msg']);

        view('admin/mensagens/index', [
            'activeTab' => 'mensagens',
            'mensagens' => $mensagens,
            'success' => $success,
            'error' => $error
        ]);
    }

    /**
     * Marca uma mensagem como lida.
     */
    public function markAsRead() {
        checkPermission(['admin_total', 'admin_parcial']);
        $id = $_POST['id'] ?? $_GET['id'] ?? null;
        
        if ($id) {
            try {
                $this->contatoModel->markAsRead($id);
                
                AuditLog::registrar('mensagem_lida', 'mensagens', [
                    'mensagem_id' => $id,
                ], 'Mensagem de contato marcada como lida.');
                
                // Se for requisição AJAX/JSON
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' || isset($_POST['ajax'])) {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 'success', 'message' => 'Mensagem marcada como lida.']);
                    exit;
                }
                
                $_SESSION['success_msg'] = "Mensagem marcada como lida!";
            } catch (Throwable $e) {
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' || isset($_POST['ajax'])) {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 'error', 'message' => safeError($e)]);
                    exit;
                }
                $_SESSION['error_msg'] = "Erro ao marcar como lida: " . safeError($e);
            }
        }
        
        redirect('/admin/mensagens');
    }

    /**
     * Exclui uma mensagem.
     */
    public function delete() {
        checkPermission(['admin_total']);
        $id = $_POST['id'] ?? $_GET['id'] ?? null;
        
        if ($id) {
            try {
                $this->contatoModel->delete($id);

                AuditLog::registrar('mensagem_excluida', 'mensagens', [
                    'mensagem_id' => $id,
                ], 'Mensagem de contato excluída.');

                $_SESSION['success_msg'] = "Mensagem excluída com sucesso!";
            } catch (Throwable $e) {
                $_SESSION['error_msg'] = "Erro ao excluir mensagem: " . safeError($e);
            }
        }
        
        redirect('/admin/mensagens');
    }
}
