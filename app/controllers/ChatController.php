<?php
/**
 * Chat "Posso ajudar?" - rotas públicas (respostas em JSON).
 * Dados no MongoDB. Se o Mongo estiver fora do ar, responde com
 * uma mensagem amigável e o telefone do Conselho.
 */
class ChatController
{
    const MSG_INDISPONIVEL = 'O chat está temporariamente indisponível. Ligue para (14) 3624-5077, de segunda a sexta, das 8h às 17h.';

    private function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function exigirMongo(): void
    {
        if (!Mongo::available()) {
            $this->json(['success' => false, 'indisponivel' => true, 'message' => self::MSG_INDISPONIVEL], 503);
        }
    }

    /** Limite simples por IP para as consultas do chat */
    private function limitar(string $chave, int $max, int $janela): void
    {
        if (!rate_limit_check($chave, $max, $janela)) {
            $this->json(['success' => false, 'message' => 'Muitas solicitações seguidas. Aguarde um instante e tente de novo.'], 429);
        }
        rate_limit_hit($chave, $janela);
    }

    /** GET /chat/categorias */
    public function categorias()
    {
        $this->exigirMongo();
        try {
            $faq = new Faq();
            $this->json([
                'success' => true,
                'categorias' => $faq->categoriasComTotal(),
                'assuntos' => PedidoAjuda::ASSUNTOS,
                'consentimento' => PedidoAjuda::TEXTO_CONSENTIMENTO,
            ]);
        } catch (Throwable $e) {
            $this->json(['success' => false, 'message' => safeError($e)], 500);
        }
    }

    /** GET /chat/perguntas?categoria=direitos */
    public function perguntas()
    {
        $this->exigirMongo();
        $this->limitar('chat_consulta', 120, 60);
        try {
            $faq = new Faq();
            $cat = is_string($_GET['categoria'] ?? null) ? $_GET['categoria'] : '';
            $this->json(['success' => true, 'perguntas' => $faq->porCategoria($cat)]);
        } catch (Throwable $e) {
            $this->json(['success' => false, 'message' => safeError($e)], 500);
        }
    }

    /** GET /chat/resposta?id=... */
    public function resposta()
    {
        $this->exigirMongo();
        $this->limitar('chat_consulta', 120, 60);
        try {
            $faq = new Faq();
            $id = is_string($_GET['id'] ?? null) ? $_GET['id'] : '';
            $r = $faq->resposta($id);
            if (!$r) $this->json(['success' => false, 'message' => 'Pergunta não encontrada.'], 404);
            $this->json(['success' => true] + $r);
        } catch (Throwable $e) {
            $this->json(['success' => false, 'message' => safeError($e)], 500);
        }
    }

    /** GET /chat/buscar?q=texto digitado */
    public function buscar()
    {
        $this->exigirMongo();
        $this->limitar('chat_busca', 40, 60);
        try {
            $faq = new Faq();
            $q = is_string($_GET['q'] ?? null) ? $_GET['q'] : '';
            $resultados = $faq->buscar($q);
            if (!$resultados) {
                // Guarda o que não foi encontrado para o Conselho criar novas perguntas
                (new PerguntaSemResposta())->registrar($q);
            }
            $this->json(['success' => true, 'resultados' => $resultados]);
        } catch (Throwable $e) {
            $this->json(['success' => false, 'message' => safeError($e)], 500);
        }
    }

    /** POST /chat/avaliar  (id, util=1|0) - "Isso ajudou?" */
    public function avaliar()
    {
        $this->exigirMongo();
        $this->limitar('chat_avaliar', 30, 600);
        $id = is_string($_POST['id'] ?? null) ? $_POST['id'] : '';

        // Cada pessoa avalia cada resposta uma vez por sessão
        $_SESSION['chat_avaliadas'] = $_SESSION['chat_avaliadas'] ?? [];
        if (in_array($id, $_SESSION['chat_avaliadas'], true)) {
            $this->json(['success' => true, 'message' => 'Obrigado! Sua avaliação já foi registrada.']);
        }
        try {
            $ok = (new Faq())->avaliar($id, ($_POST['util'] ?? '') === '1');
            if ($ok) $_SESSION['chat_avaliadas'][] = $id;
            $this->json(['success' => $ok, 'message' => 'Obrigado pela avaliação!']);
        } catch (Throwable $e) {
            $this->json(['success' => false, 'message' => safeError($e)], 500);
        }
    }

    /** POST /chat/pedido - "Quero ajuda do Conselho" */
    public function pedido()
    {
        $this->exigirMongo();

        // Campo armadilha anti-robô
        if (!empty($_POST['website'])) {
            $this->json(['success' => true, 'protocolo' => date('Y') . '-0000']);
        }

        // Anti-spam: até 3 pedidos por hora por IP
        if (!rate_limit_check('chat_pedido', 3, 3600)) {
            $this->json(['success' => false, 'message' => 'Você já enviou pedidos recentemente. Se for urgente, ligue para (14) 3624-5077.'], 429);
        }

        try {
            $model = new PedidoAjuda();
            $dados = $model->validar($_POST);
            rate_limit_hit('chat_pedido', 3600);
            $protocolo = $model->create($dados);
            $this->json(['success' => true, 'protocolo' => $protocolo]);
        } catch (AppException $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (Throwable $e) {
            $this->json(['success' => false, 'message' => safeError($e)], 500);
        }
    }
}
