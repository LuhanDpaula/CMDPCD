<?php

class HomeController {
    public function index() {
        $noticiaModel = new Noticia();
        $pcdModel = new Pcd();
        
        $noticias = $noticiaModel->allPublished();
        $ultimasNoticias = array_slice($noticias, 0, 3);
        
        $stats = $pcdModel->getHomeStats();

        // Carrega publicações do Instagram cacheado
        $cacheFile = __DIR__ . '/../../config/instagram_cache.json';
        $instagramPosts = [];
        if (file_exists($cacheFile)) {
            $instagramPosts = json_decode(file_get_contents($cacheFile), true);
        }
        
        view('home', [
            'ultimasNoticias' => $ultimasNoticias,
            'stats' => $stats,
            'instagramPosts' => $instagramPosts
        ]);
    }

    public function quemSomos() {
        $membroModel = new MembroConselho();
        $membrosAtuais = $membroModel->allActive();
        $presidentesHistoricos = $membroModel->allPresidents();
        view('quem-somos', [
            'membrosAtuais' => $membrosAtuais,
            'presidentesHistoricos' => $presidentesHistoricos
        ]);
    }

    public function iniciativas() {
        view('iniciativas');
    }

    public function noticias() {
        $noticiaModel = new Noticia();
        $noticias = $noticiaModel->allPublished();
        view('noticias', [
            'noticias' => $noticias
        ]);
    }

    /**
     * Exibe a página individual de uma notícia.
     */
    public function noticia() {
        $id = $_GET['id'] ?? null;
        $noticiaModel = new Noticia();
        $noticia = null;

        if ($id) {
            $noticia = $noticiaModel->findPublished($id);
            // Se logado no painel administrativo, permite pré-visualizar notícia mesmo em rascunho
            if (!$noticia && isset($_SESSION['user_id'])) {
                $noticia = $noticiaModel->find($id);
            }
        }

        // Se não encontrou no banco, verifica os dados de demonstração estáticos
        if (!$noticia && $id) {
            $noticia = $this->getStaticNoticia($id);
        }

        if (!$noticia) {
            http_response_code(404);
            view('404');
            return;
        }

        // Busca outras notícias para a seção "Mais Notícias"
        $all = $noticiaModel->allPublished();
        $outrasNoticias = [];
        foreach ($all as $item) {
            if ((string)$item['id'] !== (string)$noticia['id']) {
                $outrasNoticias[] = $item;
            }
            if (count($outrasNoticias) >= 3) break;
        }

        // Se o banco tiver poucas notícias, complementa com as de demonstração
        if (count($outrasNoticias) < 3) {
            $staticList = $this->getAllStaticNoticias();
            foreach ($staticList as $item) {
                if ((string)$item['id'] !== (string)$noticia['id']) {
                    $jaExiste = false;
                    foreach ($outrasNoticias as $o) {
                        if ($o['titulo'] === $item['titulo']) {
                            $jaExiste = true;
                            break;
                        }
                    }
                    if (!$jaExiste) {
                        $outrasNoticias[] = $item;
                    }
                }
                if (count($outrasNoticias) >= 3) break;
            }
        }

        view('noticia_detalhe', [
            'noticia' => $noticia,
            'outrasNoticias' => $outrasNoticias
        ]);
    }

    /**
     * Notícias estáticas para fallback resiliente
     */
    private function getAllStaticNoticias() {
        return [
            [
                'id' => 1,
                'titulo' => 'CMDPCD Jahu realiza audiência pública sobre acessibilidade urbana',
                'tema' => 'acessibilidade',
                'imagem_capa' => 'https://images.unsplash.com/photo-1573497620053-ea5300f94f21?w=800&h=400&fit=crop',
                'data_publicacao' => '2026-03-15 14:00:00',
                'criado_em' => '2026-03-15 14:00:00',
                'autor_nome' => 'Comunicação CMDPCD',
                'conteudo' => '<p>O Conselho Municipal dos Direitos da Pessoa com Deficiência (CMDPCD) de Jahu realizou, na última quarta-feira, uma audiência pública de grande importância no plenário da câmara. O evento reuniu representantes da sociedade civil, arquitetos especializados em acessibilidade e representantes do poder público.</p><p>Foram discutidas melhorias fundamentais nas calçadas das regiões comerciais da cidade, incluindo a pavimentação nivelada, implementação correta de piso tátil direcional e de alerta, e rebaixamento de guias para garantir o deslocamento autônomo e seguro das pessoas com deficiência física e visual.</p><p>Durante os debates, munícipes compartilharam suas vivências e desafios diários ao transitar pela cidade, destacando pontos críticos onde a ausência de rampas ou a presença de desníveis acentuados impede o direito de ir e vir de cadeirantes e pessoas idosas.</p><p>Como encaminhamento da plenária, foi aprovada a criação de um grupo de trabalho conjunto entre o CMDPCD e a Secretaria de Obras para elaborar um cronograma de intervenções prioritárias nas áreas de maior circulação do município.</p>'
            ],
            [
                'id' => 2,
                'titulo' => 'Parceria com APAE amplia atendimento a pessoas com deficiência intelectual',
                'tema' => 'inclusao',
                'imagem_capa' => 'https://images.unsplash.com/photo-1582213782179-e0d53f98f2ca?w=800&h=400&fit=crop',
                'data_publicacao' => '2026-03-10 10:00:00',
                'criado_em' => '2026-03-10 10:00:00',
                'autor_nome' => 'Comunicação CMDPCD',
                'conteudo' => '<p>Um novo convênio firmado entre o conselho e a APAE visa expandir significativamente os serviços de reabilitação e inclusão social no município de Jahu.</p><p>A parceria prevê a ampliação do atendimento a pessoas com deficiência intelectual, contando com novas vagas para oficinas pedagógicas, atendimento terapêutico e cursos de capacitação voltados para a inserção no mercado de trabalho local.</p><p>Segundo a coordenação técnica, as novas instalações contam com salas multifuncionais equipadas com tecnologia assistiva de ponta e equipe interdisciplinar composta por psicólogos, terapeutas ocupacionais e fonoaudiólogos.</p><p>As famílias interessadas em obter vagas ou maiores esclarecimentos devem procurar a sede do conselho ou o centro de atendimento da APAE com documentos pessoais e laudo médico comprobatório.</p>'
            ],
            [
                'id' => 3,
                'titulo' => 'Campanha de conscientização sobre direitos das pessoas com deficiência',
                'tema' => 'direitos',
                'imagem_capa' => 'https://images.unsplash.com/photo-1491438590914-bc09fcaaf77a?w=800&h=400&fit=crop',
                'data_publicacao' => '2026-02-28 09:30:00',
                'criado_em' => '2026-02-28 09:30:00',
                'autor_nome' => 'Comunicação CMDPCD',
                'conteudo' => '<p>A campanha de conscientização promovida pelo CMDPCD de Jahu visa informar a população local sobre os direitos garantidos por lei para pessoas com deficiência.</p><p>As ações englobam a distribuição de panfletos informativos em pontos comerciais estratégicos, além de ciclos de palestras em escolas públicas e empresas sobre o respeito às vagas exclusivas de estacionamento e atendimento prioritário.</p><p>A iniciativa reforça que a inclusão é uma responsabilidade coletiva e que o cumprimento das normas vigentes promove uma cidade mais justa, humana e acolhedora para todos.</p><p>Material informativo digital também está sendo disponibilizado nas redes sociais oficiais do conselho com orientações práticas para denunciar casos de discriminação ou desrespeito às vagas preferenciais.</p>'
            ]
        ];
    }

    private function getStaticNoticia($id) {
        $list = $this->getAllStaticNoticias();
        foreach ($list as $n) {
            if ((string)$n['id'] === (string)$id) {
                return $n;
            }
        }
        return null;
    }

    public function transparencia() {
        $docModel = new DocumentoPublico();
        $membroModel = new MembroConselho();
        
        $documentos = $docModel->allPublished();
        $membrosAtuais = $membroModel->allActive();
        
        view('transparencia', [
            'documentos' => $documentos,
            'membrosAtuais' => $membrosAtuais
        ]);
    }

    public function apoio() {
        view('apoio');
    }

    public function contato() {
        view('contato');
    }

    public function submitContato() {
        header('Content-Type: application/json; charset=utf-8');

        // Anti-spam: até 5 mensagens a cada 10 minutos por IP
        $rlKey = 'contato';
        if (!rate_limit_check($rlKey, 5, 600)) {
            echo json_encode(['status' => 'error', 'message' => 'Você enviou muitas mensagens em pouco tempo. Aguarde alguns minutos.']);
            exit;
        }

        // Limita o tamanho de cada campo
        $data = [
            'nome' => limit_str($_POST['nome'] ?? '', 150),
            'email' => limit_str($_POST['email'] ?? '', 150),
            'telefone' => limit_str($_POST['telefone'] ?? '', 20),
            'assunto' => limit_str($_POST['assunto'] ?? '', 150),
            'mensagem' => limit_str($_POST['mensagem'] ?? '', 5000)
        ];

        // Campo "armadilha" invisível: humanos não preenchem, robôs sim
        if (!empty($_POST['website'])) {
            echo json_encode(['status' => 'success', 'message' => 'Sua mensagem foi enviada com sucesso!']);
            exit;
        }

        if (empty($data['nome']) || empty($data['email']) || empty($data['assunto']) || empty($data['mensagem'])) {
            echo json_encode(['status' => 'error', 'message' => 'Por favor, preencha todos os campos obrigatórios.']);
            exit;
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['status' => 'error', 'message' => 'O endereço de e-mail informado é inválido.']);
            exit;
        }

        try {
            rate_limit_hit($rlKey, 600);
            $contatoModel = new Contato();
            $contatoModel->create($data);
            echo json_encode(['status' => 'success', 'message' => 'Sua mensagem foi enviada com sucesso!']);
            exit;
        } catch (Throwable $e) {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao salvar a mensagem: ' . safeError($e)]);
            exit;
        }
    }
}
