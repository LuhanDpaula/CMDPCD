<?php

class LogController
{
    public function __construct()
    {
        checkAuth();
        checkPermission(['admin_total']); // Somente Administrador Total tem acesso a logs de auditoria
    }

    /**
     * Tela de listagem e pesquisa de logs de auditoria.
     */
    public function index()
    {
        $filtros = [
            'modulo'     => is_string($_GET['modulo'] ?? null) ? trim($_GET['modulo']) : '',
            'acao'       => is_string($_GET['acao'] ?? null) ? trim($_GET['acao']) : '',
            'usuario_id' => !empty($_GET['usuario_id']) ? (int)$_GET['usuario_id'] : '',
            'busca'      => is_string($_GET['busca'] ?? null) ? trim($_GET['busca']) : '',
            'limit'      => !empty($_GET['limit']) ? min(300, max(20, (int)$_GET['limit'])) : 100,
        ];

        $logs = AuditLog::listar($filtros);
        $stats = AuditLog::estatisticas();

        // Carrega usuários para o dropdown de filtro
        $usuarios = [];
        try {
            $usuarios = (new Usuario())->all();
        } catch (Throwable $e) {}

        view('admin/logs/index', [
            'activeTab' => 'logs',
            'logs'      => $logs,
            'stats'     => $stats,
            'filtros'   => $filtros,
            'usuarios'  => $usuarios,
            'acoes'     => AuditLog::ACOES_LABELS,
            'modulos'   => AuditLog::MODULOS_LABELS,
        ]);
    }
}
