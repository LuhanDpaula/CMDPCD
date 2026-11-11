<?php
/**
 * Painel > Conteúdos > Perguntas Frequentes (MongoDB)
 * Acesso: admin_total e editor.
 */
class FaqController
{
    const PERFIS = ['admin_total', 'editor'];

    public function __construct()
    {
        checkAuth();
        checkPermission(self::PERFIS);
    }

    private function semMongo()
    {
        view('admin/conteudos/faq/indisponivel', ['activeTab' => 'conteudos']);
        exit;
    }

    private function flash(): array
    {
        $out = ['success' => $_SESSION['success_msg'] ?? null, 'error' => $_SESSION['error_msg'] ?? null];
        unset($_SESSION['success_msg'], $_SESSION['error_msg']);
        return $out;
    }

    public function index()
    {
        if (!Mongo::available()) $this->semMongo();
        $faq = new Faq();
        $categoria = is_string($_GET['categoria'] ?? null) ? $_GET['categoria'] : '';
        $busca = limit_str(is_string($_GET['q'] ?? null) ? $_GET['q'] : '', 80);

        view('admin/conteudos/faq/index', [
            'activeTab' => 'conteudos',
            'perguntas' => $faq->todas($categoria, $busca),
            'stats' => $faq->estatisticas(),
            'semRespostaTotal' => (new PerguntaSemResposta())->total(),
            'categoria' => $categoria,
            'busca' => $busca,
        ] + $this->flash());
    }

    public function create()
    {
        if (!Mongo::available()) $this->semMongo();
        $old = $_SESSION['old_input'] ?? [];
        unset($_SESSION['old_input']);

        // Vindo da tela "sem resposta": já preenche a pergunta
        if (empty($old) && is_string($_GET['pergunta'] ?? null)) {
            $old = ['pergunta' => limit_str($_GET['pergunta'], 300), 'origem' => limit_str($_GET['origem'] ?? '', 200)];
        }

        view('admin/conteudos/faq/form', [
            'activeTab' => 'conteudos',
            'modo' => 'create',
            'faq' => $old + ['ativo' => true, 'ordem' => 0],
        ] + $this->flash());
    }

    public function store()
    {
        if (!Mongo::available()) $this->semMongo();
        $faq = new Faq();
        try {
            $dados = $faq->dadosDoFormulario($_POST);
            $faq->create($dados);

            AuditLog::registrar('faq_criada', 'faq', [
                'pergunta'  => $dados['pergunta'],
                'categoria' => $dados['categoria'],
            ], 'Nova pergunta frequente adicionada ao FAQ/Chat.');

            // Se a pergunta veio da lista "sem resposta", remove de lá
            $origem = is_string($_POST['origem'] ?? null) ? $_POST['origem'] : '';
            if ($origem !== '') (new PerguntaSemResposta())->descartar($origem);

            $_SESSION['success_msg'] = 'Pergunta cadastrada com sucesso!';
            redirect('/admin/conteudos/faq');
        } catch (Throwable $e) {
            $_SESSION['error_msg'] = safeError($e);
            $_SESSION['old_input'] = array_intersect_key($_POST, array_flip(['pergunta', 'resposta', 'categoria', 'palavras_chave', 'ordem', 'ativo', 'origem']));
            redirect('/admin/conteudos/faq/create');
        }
    }

    public function edit()
    {
        if (!Mongo::available()) $this->semMongo();
        $item = (new Faq())->find(is_string($_GET['id'] ?? null) ? $_GET['id'] : '');
        if (!$item) {
            $_SESSION['error_msg'] = 'Pergunta não encontrada.';
            redirect('/admin/conteudos/faq');
        }
        $item['palavras_chave'] = implode(', ', $item['palavras_chave'] ?? []);
        $item['id'] = (string)$item['_id'];
        view('admin/conteudos/faq/form', ['activeTab' => 'conteudos', 'modo' => 'edit', 'faq' => $item] + $this->flash());
    }

    public function update()
    {
        if (!Mongo::available()) $this->semMongo();
        $id = is_string($_POST['id'] ?? null) ? $_POST['id'] : '';
        $faq = new Faq();
        if (!$faq->find($id)) {
            $_SESSION['error_msg'] = 'Pergunta não encontrada.';
            redirect('/admin/conteudos/faq');
        }
        try {
            $dados = $faq->dadosDoFormulario($_POST);
            $faq->update($id, $dados);

            AuditLog::registrar('faq_atualizada', 'faq', [
                'faq_id'   => $id,
                'pergunta' => $dados['pergunta'],
            ], 'Pergunta frequente do FAQ/Chat atualizada.');

            $_SESSION['success_msg'] = 'Pergunta atualizada com sucesso!';
            redirect('/admin/conteudos/faq');
        } catch (Throwable $e) {
            $_SESSION['error_msg'] = safeError($e);
            redirect('/admin/conteudos/faq/edit?id=' . urlencode($id));
        }
    }

    public function toggle()
    {
        if (!Mongo::available()) $this->semMongo();
        $id = is_string($_POST['id'] ?? null) ? $_POST['id'] : (is_string($_GET['id'] ?? null) ? $_GET['id'] : '');
        $novo = (new Faq())->alternarAtivo($id);
        if ($novo === null) {
            $_SESSION['error_msg'] = 'Pergunta não encontrada.';
        } else {
            $_SESSION['success_msg'] = $novo ? 'Pergunta ativada: já aparece no chat.' : 'Pergunta desativada: não aparece mais no chat.';
        }
        redirect('/admin/conteudos/faq');
    }

    public function delete()
    {
        if (!Mongo::available()) $this->semMongo();
        $id = is_string($_POST['id'] ?? null) ? $_POST['id'] : (is_string($_GET['id'] ?? null) ? $_GET['id'] : '');
        $item = (new Faq())->find($id);
        $ok = (new Faq())->delete($id);

        if ($ok) {
            AuditLog::registrar('faq_excluida', 'faq', [
                'faq_id'   => $id,
                'pergunta' => $item['pergunta'] ?? '',
            ], 'Pergunta frequente excluída do FAQ/Chat.');
        }

        $_SESSION[$ok ? 'success_msg' : 'error_msg'] = $ok ? 'Pergunta excluída.' : 'Pergunta não encontrada.';
        redirect('/admin/conteudos/faq');
    }

    /** Perguntas que o chat não soube responder */
    public function semResposta()
    {
        if (!Mongo::available()) $this->semMongo();
        view('admin/conteudos/faq/sem-resposta', [
            'activeTab' => 'conteudos',
            'itens' => (new PerguntaSemResposta())->agrupadas(),
        ] + $this->flash());
    }

    public function descartarSemResposta()
    {
        if (!Mongo::available()) $this->semMongo();
        $texto = is_string($_POST['texto'] ?? null) ? $_POST['texto'] : (is_string($_GET['texto'] ?? null) ? $_GET['texto'] : '');
        $n = (new PerguntaSemResposta())->descartar($texto);
        $_SESSION['success_msg'] = $n ? 'Pergunta descartada da lista.' : 'Nada para descartar.';
        redirect('/admin/conteudos/faq/sem-resposta');
    }
}
