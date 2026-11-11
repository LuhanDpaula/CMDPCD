<?php
/**
 * Painel > Pedidos de Ajuda (MongoDB)
 * Acesso EXCLUSIVO do perfil admin_total (contém dados pessoais).
 */
class PedidoController
{
    public function __construct()
    {
        checkAuth();
        checkPermission(['admin_total']);
    }

    private function semMongo()
    {
        view('admin/conteudos/faq/indisponivel', ['activeTab' => 'pedidos']);
        exit;
    }

    private function flash(): array
    {
        $out = ['success' => $_SESSION['success_msg'] ?? null, 'error' => $_SESSION['error_msg'] ?? null];
        unset($_SESSION['success_msg'], $_SESSION['error_msg']);
        return $out;
    }

    private function usuario(): string
    {
        return (string)($_SESSION['user_name'] ?? 'Administrador');
    }

    public function index()
    {
        if (!Mongo::available()) $this->semMongo();
        $model = new PedidoAjuda();
        $model->atualizarResumoMensal();

        $filtros = [
            'status' => is_string($_GET['status'] ?? null) ? $_GET['status'] : 'abertos',
            'assunto' => is_string($_GET['assunto'] ?? null) ? $_GET['assunto'] : '',
            'bairro' => is_string($_GET['bairro'] ?? null) ? $_GET['bairro'] : '',
            'q' => is_string($_GET['q'] ?? null) ? $_GET['q'] : '',
        ];

        view('admin/pedidos/index', [
            'activeTab' => 'pedidos',
            'pedidos' => $model->lista($filtros),
            'filtros' => $filtros,
            'stats' => $model->estatisticas(),
            'resumo' => $model->resumoMensal(),
        ] + $this->flash());
    }

    public function ver()
    {
        if (!Mongo::available()) $this->semMongo();
        $pedido = (new PedidoAjuda())->find(is_string($_GET['id'] ?? null) ? $_GET['id'] : '');
        if (!$pedido) {
            $_SESSION['error_msg'] = 'Pedido não encontrado. Ele pode ter sido excluído automaticamente após 1 ano do encerramento.';
            redirect('/admin/pedidos');
        }
        view('admin/pedidos/ver', ['activeTab' => 'pedidos', 'pedido' => $pedido] + $this->flash());
    }

    public function registrar()
    {
        if (!Mongo::available()) $this->semMongo();
        $id = is_string($_POST['id'] ?? null) ? $_POST['id'] : '';
        try {
            (new PedidoAjuda())->registrar(
                $id,
                is_string($_POST['acao'] ?? null) ? $_POST['acao'] : '',
                is_string($_POST['obs'] ?? null) ? $_POST['obs'] : '',
                is_string($_POST['status'] ?? null) ? $_POST['status'] : '',
                $this->usuario()
            );

            AuditLog::registrar('pedido_atendimento_registrado', 'pedidos', [
                'pedido_id'   => $id,
                'acao'        => is_string($_POST['acao'] ?? null) ? $_POST['acao'] : '',
                'novo_status' => is_string($_POST['status'] ?? null) ? $_POST['status'] : '',
            ], 'Atendimento registrado no pedido de ajuda.');

            $_SESSION['success_msg'] = 'Atendimento registrado no histórico.';
        } catch (Throwable $e) {
            $_SESSION['error_msg'] = safeError($e);
        }
        redirect('/admin/pedidos/ver?id=' . urlencode($id));
    }

    public function prioridade()
    {
        if (!Mongo::available()) $this->semMongo();
        $id = is_string($_POST['id'] ?? null) ? $_POST['id'] : '';
        $novo = (new PedidoAjuda())->alternarPrioridade($id, $this->usuario());
        if ($novo === null) {
            $_SESSION['error_msg'] = 'Pedido não encontrado.';
            redirect('/admin/pedidos');
        }

        AuditLog::registrar('pedido_prioridade_alterada', 'pedidos', [
            'pedido_id'   => $id,
            'prioritario' => $novo,
        ], $novo ? 'Pedido marcado como prioritário pelo Conselho.' : 'Prioridade do pedido removida.');

        $_SESSION['success_msg'] = $novo ? 'Pedido marcado como prioritário.' : 'Prioridade removida.';
        redirect('/admin/pedidos/ver?id=' . urlencode($id));
    }

    public function excluir()
    {
        if (!Mongo::available()) $this->semMongo();
        $id = is_string($_POST['id'] ?? null) ? $_POST['id'] : (is_string($_GET['id'] ?? null) ? $_GET['id'] : '');
        $ok = (new PedidoAjuda())->delete($id);

        if ($ok) {
            AuditLog::registrar('pedido_excluido', 'pedidos', [
                'pedido_id' => $id,
            ], 'Pedido de ajuda excluído definitivamente a pedido do titular (LGPD).');
        }

        $_SESSION[$ok ? 'success_msg' : 'error_msg'] = $ok ? 'Pedido e dados pessoais excluídos definitivamente.' : 'Pedido não encontrado.';
        redirect('/admin/pedidos');
    }
}
