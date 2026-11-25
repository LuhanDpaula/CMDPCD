<?php
/** Dados explicitamente fictícios para demonstração. Não representam pessoas reais. */
function cmdpcdDemoPcdRows(): array
{
    $first = ['Ana','Bruno','Carla','Daniel','Elisa','Felipe','Gabriela','Heitor','Isabela','João','Karen','Lucas','Marina','Nicolas','Olívia','Pedro','Renata','Samuel','Talita','Vitor'];
    $last = ['Silva','Souza','Oliveira','Santos','Costa','Pereira','Almeida','Ribeiro','Ferreira','Gomes'];
    $neighborhoods = ['Centro','Jardim América','Vila Nova','Jardim Bela Vista','Vila Maria','Jardim São José','Chácara Braz Miraglia','Jardim Padre Augusto Sani','Vila Netinho','Jardim das Paineiras'];
    $streets = ['Rua das Acácias','Avenida das Palmeiras','Rua do Ipê','Rua das Flores','Rua da Inclusão','Rua das Andorinhas','Avenida Central','Rua do Sol','Rua Esperança','Rua da Amizade'];
    $types = ['Física','Auditiva','Visual','Intelectual','Múltipla','TEA','Outros'];
    $rows = [];
    for ($i = 1; $i <= 50; $i++) {
        $n = $i - 1;
        $name = sprintf('[TESTE %02d] %s %s', $i, $first[$n % count($first)], $last[(int)floor($n / 2) % count($last)]);
        $cpf = '9999999' . str_pad((string)$i, 4, '0', STR_PAD_LEFT);
        $row = [
            'nome_completo' => $name,
            'nome_social' => '',
            'cpf' => $cpf,
            'rg' => 'RGTESTE' . str_pad((string)$i, 4, '0', STR_PAD_LEFT),
            'data_nascimento' => date('Y-m-d', strtotime('-' . (18 + ($i % 55)) . ' years -' . ($i % 11) . ' months')),
            'sexo_biologico' => $i % 2 ? 'feminino' : 'masculino',
            'genero' => 'Não informado (demonstração)',
            'estado_civil' => ['solteiro','casado','viúvo','divorciado'][$i % 4],
            'raca_cor' => ['branca','parda','preta','nao_declarado'][$i % 4],
            'nacionalidade' => 'Brasileira',
            'cep' => '00000000',
            'logradouro' => $streets[$n % count($streets)],
            'numero' => (string)(100 + $i),
            'complemento' => '',
            'bairro' => $neighborhoods[$n % count($neighborhoods)],
            'cidade' => 'Jaú',
            'uf' => 'SP',
            'telefone_principal' => '(14) 90000-' . str_pad((string)$i, 4, '0', STR_PAD_LEFT),
            'telefone_secundario' => '',
            'email' => sprintf('teste.pcd.%02d@example.test', $i),
            'contato_emergencia_nome' => '[TESTE] Contato de Emergência',
            'contato_emergencia_telefone' => '(14) 91111-' . str_pad((string)$i, 4, '0', STR_PAD_LEFT),
            'renda_familiar' => number_format(1400 + ($i * 73.25), 2, '.', ''),
            'numero_dependentes' => $i % 4,
            'situacao_habitacional' => ['alugada','propria_quitada','cedida','propria_financiada'][$i % 4],
            'escolaridade' => ['fundamental_completo','medio_completo','superior_incompleto','superior_completo'][$i % 4],
            'ocupacao_atual' => ['Estudante','Autônomo(a)','Assistente administrativo(a)','Não informado'][$i % 4],
            'recebe_bpc_loas' => $i % 3 === 0 ? 1 : 0,
            'beneficio_bpc_loas' => $i % 3 === 0 ? 'BPC/LOAS (valor fictício de teste)' : '',
            'grau_deficiencia' => ['leve','moderada','severa','profunda'][$i % 4],
            'cid' => 'CID-TESTE-' . str_pad((string)$i, 2, '0', STR_PAD_LEFT),
            'data_diagnostico' => date('Y-m-d', strtotime('-' . (1 + ($i % 15)) . ' years')),
            'tecnologia_assistiva' => ['Nenhuma (teste)','Cadeira de rodas (teste)','Leitor de tela (teste)','Aparelho auditivo (teste)'][$i % 4],
            'necessita_acompanhante' => $i % 5 === 0 ? 1 : 0,
            'medicacao_continua' => 'Informação fictícia para testes; não utilizar como dado clínico.',
            'status' => $i % 7 === 0 ? 'pendente' : 'deferido',
            '_tipo_deficiencia_nome' => $types[($i - 1) % count($types)],
        ];
        $rows[] = $row;
    }
    return $rows;
}

function cmdpcdSeedDemoPcds(PDO $pdo): int
{
    $rows = cmdpcdDemoPcdRows();
    $added = 0;
    $typeIds = [];
    $findType = $pdo->prepare('SELECT id FROM tipos_deficiencia WHERE nome = ? LIMIT 1');
    $addType = $pdo->prepare('INSERT INTO tipos_deficiencia (nome, descricao) VALUES (?, ?)');
    foreach (['Física','Auditiva','Visual','Intelectual','Múltipla','TEA','Outros'] as $typeName) {
        $findType->execute([$typeName]);
        $typeId = $findType->fetchColumn();
        if (!$typeId) { $addType->execute([$typeName, 'Tipo de deficiência - catálogo do CMDPCD']); $typeId = $pdo->lastInsertId(); }
        $typeIds[$typeName] = (int)$typeId;
    }
    $insert = $pdo->prepare("INSERT INTO pcds (
        nome_completo,nome_social,cpf,rg,data_nascimento,sexo_biologico,genero,estado_civil,raca_cor,nacionalidade,
        cep,logradouro,numero,complemento,bairro,cidade,uf,telefone_principal,telefone_secundario,email,
        contato_emergencia_nome,contato_emergencia_telefone,renda_familiar,numero_dependentes,situacao_habitacional,
        escolaridade,ocupacao_atual,recebe_bpc_loas,beneficio_bpc_loas,grau_deficiencia,cid,data_diagnostico,
        tecnologia_assistiva,necessita_acompanhante,medicacao_continua,status
    ) VALUES (
        :nome_completo,:nome_social,:cpf,:rg,:data_nascimento,:sexo_biologico,:genero,:estado_civil,:raca_cor,:nacionalidade,
        :cep,:logradouro,:numero,:complemento,:bairro,:cidade,:uf,:telefone_principal,:telefone_secundario,:email,
        :contato_emergencia_nome,:contato_emergencia_telefone,:renda_familiar,:numero_dependentes,:situacao_habitacional,
        :escolaridade,:ocupacao_atual,:recebe_bpc_loas,:beneficio_bpc_loas,:grau_deficiencia,:cid,:data_diagnostico,
        :tecnologia_assistiva,:necessita_acompanhante,:medicacao_continua,:status
    )");
    $find = $pdo->prepare('SELECT id FROM pcds WHERE cpf = ? LIMIT 1');
    $link = $pdo->prepare('INSERT IGNORE INTO pcd_deficiencias (pcd_id, tipo_deficiencia_id) VALUES (?, ?)');
    foreach ($rows as $row) {
        $find->execute([$row['cpf']]);
        $existing = $find->fetchColumn();
        $typeName = $row['_tipo_deficiencia_nome'];
        unset($row['_tipo_deficiencia_nome']);
        $typeId = $typeIds[$typeName];
        if (!$existing) {
            $insert->execute($row);
            $existing = (int)$pdo->lastInsertId();
            $added++;
        }
        $link->execute([(int)$existing, $typeId]);
    }
    return $added;
}

function cmdpcdWriteDemoCsv(string $path): void
{
    $rows = cmdpcdDemoPcdRows();
    $fp = fopen($path, 'wb');
    if (!$fp) throw new RuntimeException('Não foi possível criar o CSV de demonstração.');
    fwrite($fp, "\xEF\xBB\xBF");
    $headers = array_values(array_filter(array_keys($rows[0]), fn($k) => $k !== '_tipo_deficiencia_nome'));
    fputcsv($fp, $headers, ';');
    foreach ($rows as $row) { unset($row['_tipo_deficiencia_nome']); fputcsv($fp, array_map(fn($k) => $row[$k] ?? '', $headers), ';'); }
    fclose($fp);
}
