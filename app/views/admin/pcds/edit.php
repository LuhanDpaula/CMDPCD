<?php require_once __DIR__ . '/../../partials/admin_header.php'; ?>

<div class="bg-card rounded-2xl border border-border overflow-hidden max-w-4xl mx-auto mb-10" style="box-shadow: var(--card-shadow)">
  <div class="p-6 border-b border-border">
    <h2 class="text-xl font-bold">Editar Cadastro PCD: <?= htmlspecialchars($pcd['nome_completo']) ?></h2>
    <p class="text-sm text-muted-foreground">Atualize as informações do cadastro e do responsável legal.</p>
  </div>

  <?php if (isAdminParcial()): ?>
    <div class="bg-primary/5 text-primary text-sm p-4 mx-6 mt-6 rounded-xl border border-primary/20 flex items-center gap-3">
      <i data-lucide="eye" class="w-5 h-5 shrink-0"></i>
      <div>
        <strong class="font-bold">Modo de Apenas Leitura</strong>
        <p class="text-xs text-muted-foreground mt-0.5">Como Administrador Parcial, você possui permissão para visualizar todos os dados cadastrados, mas as alterações e ações de escrita estão desabilitadas.</p>
      </div>
    </div>
  <?php endif; ?>

  <?php if (!empty($error)): ?>
    <div class="bg-destructive/10 text-destructive text-sm p-4 mx-6 mt-6 rounded-xl border border-destructive/20 flex items-center gap-2">
      <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
      <span><?= htmlspecialchars($error) ?></span>
    </div>
  <?php endif; ?>

  <form action="<?= BASE_URL ?>/admin/pcds/update" method="POST" enctype="multipart/form-data" class="p-6 space-y-8">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= $pcd['id'] ?>">

    <!-- 1. IDENTIFICAÇÃO -->
    <div class="space-y-4">
      <h3 class="text-md font-bold text-primary flex items-center gap-1.5 border-b border-border pb-2">
        <i data-lucide="user" class="w-5 h-5"></i> 1. Identificação do PCD
      </h3>
      <div class="grid sm:grid-cols-3 gap-4">
        <div class="sm:col-span-2">
          <label class="block text-sm font-medium mb-1">Nome completo *</label>
          <input type="text" name="nome_completo" required value="<?= htmlspecialchars($pcd['nome_completo']) ?>" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Nome social</label>
          <input type="text" name="nome_social" value="<?= htmlspecialchars($pcd['nome_social'] ?? '') ?>" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">CPF *</label>
          <input type="text" id="cpf" name="cpf" required value="<?= htmlspecialchars($pcd['cpf']) ?>" placeholder="000.000.000-00" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">RG *</label>
          <input type="text" name="rg" required value="<?= htmlspecialchars($pcd['rg']) ?>" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Data de nascimento *</label>
          <input type="date" name="data_nascimento" required value="<?= htmlspecialchars($pcd['data_nascimento']) ?>" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Sexo Biológico *</label>
          <select name="sexo_biologico" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
            <option value="">Selecione</option>
            <option value="masculino" <?= ($pcd['sexo_biologico'] === 'masculino') ? 'selected' : '' ?>>Masculino</option>
            <option value="feminino" <?= ($pcd['sexo_biologico'] === 'feminino') ? 'selected' : '' ?>>Feminino</option>
            <option value="outro" <?= ($pcd['sexo_biologico'] === 'outro') ? 'selected' : '' ?>>Outro</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Identidade de Gênero</label>
          <input type="text" name="genero" value="<?= htmlspecialchars($pcd['genero'] ?? '') ?>" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Raça / Cor *</label>
          <select name="raca_cor" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
            <option value="">Selecione</option>
            <option value="branca" <?= ($pcd['raca_cor'] === 'branca') ? 'selected' : '' ?>>Branca</option>
            <option value="preta" <?= ($pcd['raca_cor'] === 'preta') ? 'selected' : '' ?>>Preta</option>
            <option value="parda" <?= ($pcd['raca_cor'] === 'parda') ? 'selected' : '' ?>>Parda</option>
            <option value="amarela" <?= ($pcd['raca_cor'] === 'amarela') ? 'selected' : '' ?>>Amarela</option>
            <option value="indigena" <?= ($pcd['raca_cor'] === 'indigena') ? 'selected' : '' ?>>Indígena</option>
            <option value="nao_declarado" <?= ($pcd['raca_cor'] === 'nao_declarado') ? 'selected' : '' ?>>Não declarado</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Nacionalidade *</label>
          <input type="text" name="nacionalidade" value="<?= htmlspecialchars($pcd['nacionalidade']) ?>" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Estado Civil</label>
          <select name="estado_civil" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
            <option value="">Selecione</option>
            <option value="solteiro" <?= ($pcd['estado_civil'] === 'solteiro') ? 'selected' : '' ?>>Solteiro(a)</option>
            <option value="casado" <?= ($pcd['estado_civil'] === 'casado') ? 'selected' : '' ?>>Casado(a)</option>
            <option value="divorciado" <?= ($pcd['estado_civil'] === 'divorciado') ? 'selected' : '' ?>>Divorciado(a)</option>
            <option value="viuvo" <?= ($pcd['estado_civil'] === 'viuvo') ? 'selected' : '' ?>>Viúvo(a)</option>
            <option value="outro" <?= ($pcd['estado_civil'] === 'outro') ? 'selected' : '' ?>>Outro</option>
          </select>
        </div>
      </div>
    </div>

    <!-- 2. ENDEREÇO E CONTATOS -->
    <div class="space-y-4">
      <h3 class="text-md font-bold text-primary flex items-center gap-1.5 border-b border-border pb-2">
        <i data-lucide="map-pin" class="w-5 h-5"></i> 2. Endereço e Contatos
      </h3>
      <div class="grid sm:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium mb-1">CEP *</label>
          <input type="text" id="cep" name="cep" placeholder="00000-000" required value="<?= htmlspecialchars($pcd['cep']) ?>" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div class="sm:col-span-2">
          <label class="block text-sm font-medium mb-1">Logradouro *</label>
          <input type="text" name="logradouro" required value="<?= htmlspecialchars($pcd['logradouro']) ?>" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Número *</label>
          <input type="text" name="numero" required value="<?= htmlspecialchars($pcd['numero']) ?>" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div class="sm:col-span-2">
          <label class="block text-sm font-medium mb-1">Complemento</label>
          <input type="text" name="complemento" value="<?= htmlspecialchars($pcd['complemento'] ?? '') ?>" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Bairro *</label>
          <input type="text" name="bairro" required value="<?= htmlspecialchars($pcd['bairro']) ?>" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Cidade *</label>
          <input type="text" name="cidade" required value="<?= htmlspecialchars($pcd['cidade']) ?>" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">UF *</label>
          <input type="text" name="uf" maxlength="2" required value="<?= htmlspecialchars($pcd['uf']) ?>" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Telefone Principal *</label>
          <input type="text" id="telefonePrincipal" name="telefone_principal" placeholder="(00) 00000-0000" required value="<?= htmlspecialchars($pcd['telefone_principal']) ?>" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Telefone Secundário</label>
          <input type="text" id="telefoneSecundario" name="telefone_secundario" placeholder="(00) 00000-0000" value="<?= htmlspecialchars($pcd['telefone_secundario'] ?? '') ?>" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">E-mail</label>
          <input type="email" name="email" value="<?= htmlspecialchars($pcd['email'] ?? '') ?>" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div class="sm:col-span-2">
          <label class="block text-sm font-medium mb-1">Contato de Emergência (Nome) *</label>
          <input type="text" name="contato_emergencia_nome" required value="<?= htmlspecialchars($pcd['contato_emergencia_nome']) ?>" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Contato Emergência (Fone) *</label>
          <input type="text" id="contatoEmergenciaTelefone" name="contato_emergencia_telefone" placeholder="(00) 00000-0000" required value="<?= htmlspecialchars($pcd['contato_emergencia_telefone']) ?>" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
      </div>
    </div>

    <!-- 3. RESPONSÁVEL LEGAL -->
    <?php $hasResp = !empty($pcd['responsavel']); ?>
    <div class="space-y-4">
      <h3 class="text-md font-bold text-primary flex items-center gap-1.5 border-b border-border pb-2">
        <i data-lucide="shield-alert" class="w-5 h-5"></i> 3. Responsável Legal / Cuidador
      </h3>
      <div class="bg-muted/40 p-4 rounded-xl flex items-center gap-2 mb-4">
        <input type="checkbox" id="possuiResponsavel" name="possui_responsavel" value="1" <?= $hasResp ? 'checked' : '' ?> class="w-4 h-4 accent-primary">
        <label for="possuiResponsavel" class="text-sm font-semibold text-foreground cursor-pointer">Marcar se o PCD possuir responsável legal ou cuidador</label>
      </div>
      <div id="responsavel-fields" class="grid sm:grid-cols-3 gap-4 <?= $hasResp ? '' : 'hidden' ?>">
        <div class="sm:col-span-2">
          <label class="block text-sm font-medium mb-1">Nome completo do responsável *</label>
          <input type="text" id="responsavelNome" name="responsavel_nome" value="<?= $hasResp ? htmlspecialchars($pcd['responsavel']['nome_completo']) : '' ?>" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">CPF do responsável *</label>
          <input type="text" id="responsavelCpf" name="responsavel_cpf" placeholder="000.000.000-00" value="<?= $hasResp ? htmlspecialchars($pcd['responsavel']['cpf']) : '' ?>" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">RG do responsável *</label>
          <input type="text" id="responsavelRg" name="responsavel_rg" value="<?= $hasResp ? htmlspecialchars($pcd['responsavel']['rg']) : '' ?>" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Data de nascimento do responsável *</label>
          <input type="date" id="responsavelDataNascimento" name="responsavel_data_nascimento" value="<?= $hasResp ? htmlspecialchars($pcd['responsavel']['data_nascimento']) : '' ?>" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Parentesco *</label>
          <select id="responsavelParentesco" name="responsavel_parentesco" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
            <option value="">Selecione</option>
            <option value="mae" <?= ($hasResp && $pcd['responsavel']['parentesco'] === 'mae') ? 'selected' : '' ?>>Mãe</option>
            <option value="pai" <?= ($hasResp && $pcd['responsavel']['parentesco'] === 'pai') ? 'selected' : '' ?>>Pai</option>
            <option value="tutor" <?= ($hasResp && $pcd['responsavel']['parentesco'] === 'tutor') ? 'selected' : '' ?>>Tutor(a)</option>
            <option value="curador" <?= ($hasResp && $pcd['responsavel']['parentesco'] === 'curador') ? 'selected' : '' ?>>Curador(a)</option>
            <option value="outro" <?= ($hasResp && $pcd['responsavel']['parentesco'] === 'outro') ? 'selected' : '' ?>>Outro</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Telefone do responsável *</label>
          <input type="text" id="responsavelTelefone" name="responsavel_telefone" placeholder="(00) 00000-0000" value="<?= $hasResp ? htmlspecialchars($pcd['responsavel']['telefone']) : '' ?>" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">E-mail do responsável</label>
          <input type="email" id="responsavelEmail" name="responsavel_email" value="<?= $hasResp ? htmlspecialchars($pcd['responsavel']['email'] ?? '') : '' ?>" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div class="flex items-center gap-2 sm:col-span-2 pt-2">
          <input type="checkbox" id="responsavelFormal" name="responsavel_formal" value="1" <?= ($hasResp && !empty($pcd['responsavel']['responsavel_formal'])) ? 'checked' : '' ?> class="w-4 h-4 accent-primary">
          <label for="responsavelFormal" class="text-sm font-medium">Responsável constituído formalmente (Tutela/Curatela)</label>
        </div>
      </div>
    </div>

    <!-- 4. INFORMAÇÕES SOBRE A DEFICIÊNCIA -->
    <div class="space-y-4">
      <h3 class="text-md font-bold text-primary flex items-center gap-1.5 border-b border-border pb-2">
        <i data-lucide="clipboard-list" class="w-5 h-5"></i> 4. Deficiência e Saúde
      </h3>
      <div>
        <label class="block text-sm font-bold mb-2">Tipos de Deficiência *</label>
        <p class="text-xs text-muted-foreground mb-3">Marque todas as deficiências diagnosticadas (múltiplas permitidas):</p>
        <div class="flex flex-wrap gap-4 bg-muted/20 p-4 rounded-xl border border-border">
          <?php foreach ($tiposDeficiencia as $tipo): ?>
            <?php $checked = in_array($tipo['id'], $pcdDefIds) ? 'checked' : ''; ?>
            <label for="def_edit_<?= $tipo['id'] ?>" class="flex items-center gap-1.5 text-sm font-semibold cursor-pointer">
              <input type="checkbox" id="def_edit_<?= $tipo['id'] ?>" name="deficiencias_ids[]" value="<?= $tipo['id'] ?>" <?= $checked ?> class="accent-primary w-4 h-4">
              <?= htmlspecialchars($tipo['nome']) ?>
            </label>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="grid sm:grid-cols-3 gap-4">
                  <div>
                    <div id="cid-container">
                      <label class="block text-sm font-medium mb-1">CID-10 / CID-11</label>
                      <span class="text-[11px] text-muted-foreground block mb-2 leading-tight">Você pode digitar mais de um CID se possuir mais de uma deficiência.</span>
                      <?php 
                        $cids = [];
                        if (!empty($pcd['cid'])) {
                          $cids = array_map('trim', explode(',', $pcd['cid']));
                        }
                        if (empty($cids)) {
                          $cids = [''];
                        }
                        foreach ($cids as $idx => $cidVal): 
                      ?>
                        <div class="cid-input-group flex items-center gap-2 mb-2">
                          <input type="text" class="cid-input flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="Ex: G80" name="cid[]" value="<?= htmlspecialchars($cidVal) ?>">
                          <?php if ($idx > 0): ?>
                            <button type="button" class="btn-remove-cid p-2 rounded-lg bg-destructive/10 text-destructive hover:bg-destructive/20 transition-colors" title="Remover">
                              <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                          <?php endif; ?>
                        </div>
                      <?php endforeach; ?>
                    </div>
                    <button type="button" id="btn-add-cid" class="mt-1.5 text-xs text-primary font-bold flex items-center gap-1 hover:underline">
                      <i data-lucide="plus" class="w-3.5 h-3.5"></i> Adicionar outro CID
                    </button>
                  </div>
        <div>
          <label class="block text-sm font-medium mb-1">Grau de Deficiência *</label>
          <select name="grau_deficiencia" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
            <option value="">Selecione</option>
            <option value="leve" <?= ($pcd['grau_deficiencia'] === 'leve') ? 'selected' : '' ?>>Leve</option>
            <option value="moderada" <?= ($pcd['grau_deficiencia'] === 'moderada') ? 'selected' : '' ?>>Moderada</option>
            <option value="severa" <?= ($pcd['grau_deficiencia'] === 'severa') ? 'selected' : '' ?>>Severa</option>
            <option value="profunda" <?= ($pcd['grau_deficiencia'] === 'profunda') ? 'selected' : '' ?>>Profunda</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Data do Diagnóstico</label>
          <input type="date" name="data_diagnostico" value="<?= htmlspecialchars($pcd['data_diagnostico'] ?? '') ?>" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div class="sm:col-span-2">
          <label class="block text-sm font-medium mb-1">Tecnologia Assistiva Utilizada</label>
          <input type="text" name="tecnologia_assistiva" value="<?= htmlspecialchars($pcd['tecnologia_assistiva'] ?? '') ?>" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="Cadeira de rodas, órtese, prótese, etc.">
        </div>
        <div class="flex items-center gap-2 pt-2">
          <input type="checkbox" id="necessitaAcompanhante" name="necessita_acompanhante" value="1" <?= !empty($pcd['necessita_acompanhante']) ? 'checked' : '' ?> class="w-4 h-4 accent-primary">
          <label for="necessitaAcompanhante" class="text-sm font-medium">Necessita acompanhante</label>
        </div>
        <div class="sm:col-span-3">
          <label class="block text-sm font-medium mb-1">Medicações de uso contínuo</label>
          <textarea name="medicacao_continua" rows="2" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="Listar remédios utilizados diariamente"><?= htmlspecialchars($pcd['medicacao_continua'] ?? '') ?></textarea>
        </div>
      </div>
    </div>

    <!-- 5. SOCIOECONÔMICO -->
    <div class="space-y-4">
      <h3 class="text-md font-bold text-primary flex items-center gap-1.5 border-b border-border pb-2">
        <i data-lucide="wallet" class="w-5 h-5"></i> 5. Informações Socioeconômicas
      </h3>
      <div class="grid sm:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium mb-1">Renda Familiar Mensal (R$) *</label>
          <input type="number" step="0.01" name="renda_familiar" required value="<?= htmlspecialchars($pcd['renda_familiar']) ?>" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Número de Dependentes *</label>
          <input type="number" name="numero_dependentes" required value="<?= htmlspecialchars($pcd['numero_dependentes']) ?>" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Situação Habitacional *</label>
          <select name="situacao_habitacional" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
            <option value="">Selecione</option>
            <option value="propria_quitada" <?= ($pcd['situacao_habitacional'] === 'propria_quitada') ? 'selected' : '' ?>>Própria Quitada</option>
            <option value="propria_financiada" <?= ($pcd['situacao_habitacional'] === 'propria_financiada') ? 'selected' : '' ?>>Própria Financiada</option>
            <option value="alugada" <?= ($pcd['situacao_habitacional'] === 'alugada') ? 'selected' : '' ?>>Alugada</option>
            <option value="cedida" <?= ($pcd['situacao_habitacional'] === 'cedida') ? 'selected' : '' ?>>Cedida</option>
            <option value="outra" <?= ($pcd['situacao_habitacional'] === 'outra') ? 'selected' : '' ?>>Outra</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Escolaridade *</label>
          <select name="escolaridade" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
            <option value="">Selecione</option>
            <option value="sem_instrucao" <?= ($pcd['escolaridade'] === 'sem_instrucao') ? 'selected' : '' ?>>Sem Instrução</option>
            <option value="fundamental_incompleto" <?= ($pcd['escolaridade'] === 'fundamental_incompleto') ? 'selected' : '' ?>>Fundamental Incompleto</option>
            <option value="fundamental_completo" <?= ($pcd['escolaridade'] === 'fundamental_completo') ? 'selected' : '' ?>>Fundamental Completo</option>
            <option value="medio_incompleto" <?= ($pcd['escolaridade'] === 'medio_incompleto') ? 'selected' : '' ?>>Médio Incompleto</option>
            <option value="medio_completo" <?= ($pcd['escolaridade'] === 'medio_completo') ? 'selected' : '' ?>>Médio Completo</option>
            <option value="superior_incompleto" <?= ($pcd['escolaridade'] === 'superior_incompleto') ? 'selected' : '' ?>>Superior Incompleto</option>
            <option value="superior_completo" <?= ($pcd['escolaridade'] === 'superior_completo') ? 'selected' : '' ?>>Superior Completo</option>
          </select>
        </div>
        <div class="sm:col-span-2">
          <label class="block text-sm font-medium mb-1">Ocupação Atual / Trabalho</label>
          <input type="text" name="ocupacao_atual" value="<?= htmlspecialchars($pcd['ocupacao_atual'] ?? '') ?>" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div class="sm:col-span-3">
          <?php $hasBpc = !empty($pcd['recebe_bpc_loas']); ?>
          <div class="flex items-center gap-2 mb-3">
            <input type="checkbox" id="recebeBpcLoas" name="recebe_bpc_loas" value="1" <?= $hasBpc ? 'checked' : '' ?> class="w-4 h-4 accent-primary">
            <label for="recebeBpcLoas" class="text-sm font-medium">Recebe benefício BPC / LOAS</label>
          </div>
          <div id="bpc-fields" class="<?= $hasBpc ? '' : 'hidden' ?>">
            <label class="block text-sm font-medium mb-1">Número ou detalhes do BPC</label>
            <input type="text" id="beneficioBpcLoas" name="beneficio_bpc_loas" value="<?= htmlspecialchars($pcd['beneficio_bpc_loas'] ?? '') ?>" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
          </div>
        </div>
      </div>
    </div>

    <!-- 6. DOCUMENTOS -->
    <?php
    $docRg = null;
    $docResidencia = null;
    $docLaudo = null;

    if (!empty($pcd['documentos'])) {
        foreach ($pcd['documentos'] as $doc) {
            if ($doc['tipo_documento'] === 'rg') {
                $docRg = $doc;
            } elseif ($doc['tipo_documento'] === 'comprovante_residencia') {
                $docResidencia = $doc;
            } elseif ($doc['tipo_documento'] === 'laudo_medico') {
                $docLaudo = $doc;
            }
        }
    }
    ?>
    <div class="space-y-4">
      <h3 class="text-md font-bold text-primary flex items-center gap-1.5 border-b border-border pb-2">
        <i data-lucide="files" class="w-5 h-5"></i> 6. Documentos Anexados
      </h3>
      <div class="grid sm:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium mb-1">Documento de Identidade (RG/CNH)</label>
          <?php if ($docRg): ?>
            <div class="mb-2 p-2 bg-muted rounded-xl flex items-center justify-between text-xs border border-border">
              <span class="truncate pr-2 font-medium">Anexo Atual</span>
              <a href="<?= BASE_URL ?>/admin/pcds/documento?id=<?= (int)$docRg['id'] ?>" target="_blank" rel="noopener" class="text-primary font-bold hover:underline inline-flex items-center gap-1">
                <i data-lucide="download" class="w-3.5 h-3.5"></i> Baixar
              </a>
            </div>
          <?php endif; ?>
          <input type="file" name="doc_rg" accept=".pdf,.jpg,.jpeg,.png" class="text-sm block w-full text-muted-foreground file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
        </div>
        
        <div>
          <label class="block text-sm font-medium mb-1">Comprovante de Residência</label>
          <?php if ($docResidencia): ?>
            <div class="mb-2 p-2 bg-muted rounded-xl flex items-center justify-between text-xs border border-border">
              <span class="truncate pr-2 font-medium">Anexo Atual</span>
              <a href="<?= BASE_URL ?>/admin/pcds/documento?id=<?= (int)$docResidencia['id'] ?>" target="_blank" rel="noopener" class="text-primary font-bold hover:underline inline-flex items-center gap-1">
                <i data-lucide="download" class="w-3.5 h-3.5"></i> Baixar
              </a>
            </div>
          <?php endif; ?>
          <input type="file" name="doc_residencia" accept=".pdf,.jpg,.jpeg,.png" class="text-sm block w-full text-muted-foreground file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
        </div>
        
        <div>
          <label class="block text-sm font-medium mb-1">Laudo Médico (com CID)</label>
          <?php if ($docLaudo): ?>
            <div class="mb-2 p-2 bg-muted rounded-xl flex items-center justify-between text-xs border border-border">
              <span class="truncate pr-2 font-medium">Anexo Atual</span>
              <a href="<?= BASE_URL ?>/admin/pcds/documento?id=<?= (int)$docLaudo['id'] ?>" target="_blank" rel="noopener" class="text-primary font-bold hover:underline inline-flex items-center gap-1">
                <i data-lucide="download" class="w-3.5 h-3.5"></i> Baixar
              </a>
            </div>
          <?php endif; ?>
          <input type="file" name="doc_laudo" accept=".pdf,.jpg,.jpeg,.png" class="text-sm block w-full text-muted-foreground file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
        </div>
      </div>
    </div>

    <!-- SUBMIT -->
    <div class="flex justify-end gap-3 pt-6 border-t border-border">
      <a href="<?= BASE_URL ?>/admin/pcds" class="px-6 py-2 border border-border text-foreground rounded-full hover:bg-muted transition-colors">
        Cancelar
      </a>
      <button type="submit" class="px-6 py-2 bg-primary text-primary-foreground font-bold rounded-full hover:bg-primary/90 transition-colors">
        Salvar Alterações
      </button>
    </div>

  </form>
</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    // Toggles para Responsável
    document.getElementById('possuiResponsavel').addEventListener('change', (e) => {
      const div = document.getElementById('responsavel-fields');
      const fields = ['responsavelNome', 'responsavelCpf', 'responsavelRg', 'responsavelDataNascimento', 'responsavelParentesco', 'responsavelTelefone'];
      
      if(e.target.checked) {
        div.classList.remove('hidden');
        fields.forEach(f => document.getElementById(f).required = true);
      } else {
        div.classList.add('hidden');
        fields.forEach(f => document.getElementById(f).required = false);
      }
    });

    // Toggles para BPC
    document.getElementById('recebeBpcLoas').addEventListener('change', (e) => {
      const div = document.getElementById('bpc-fields');
      if(e.target.checked) div.classList.remove('hidden');
      else div.classList.add('hidden');
    });

    // Máscaras de entrada
    function maskInput(id, format, length) {
      const el = document.getElementById(id);
      if (el) {
        const applyMask = (val) => {
          let v = val.replace(/\D/g, "");
          for (let fmt of format) {
            v = v.replace(fmt.pattern, fmt.replace);
          }
          return v.substring(0, length);
        };
        // Aplica no carregamento
        el.value = applyMask(el.value);
        
        el.addEventListener('input', function(e) {
          e.target.value = applyMask(e.target.value);
        });
      }
    }

    const cpfFmt = [
      {pattern: /(\d{3})(\d)/, replace: "$1.$2"},
      {pattern: /(\d{3})(\d)/, replace: "$1.$2"},
      {pattern: /(\d{3})(\d{1,2})$/, replace: "$1-$2"}
    ];
    maskInput('cpf', cpfFmt, 14);
    maskInput('responsavelCpf', cpfFmt, 14);

    const foneFmt = [
      {pattern: /(\d{2})(\d)/, replace: "($1) $2"},
      {pattern: /(\d{5})(\d)/, replace: "$1-$2"}
    ];
    maskInput('telefonePrincipal', foneFmt, 15);
    maskInput('telefoneSecundario', foneFmt, 15);
    maskInput('contatoEmergenciaTelefone', foneFmt, 15);
    maskInput('responsavelTelefone', foneFmt, 15);

    const cepFmt = [{pattern: /(\d{5})(\d)/, replace: "$1-$2"}];
    maskInput('cep', cepFmt, 9);

    // Adicionar/Remover CID dinamicamente
    const btnAddCid = document.getElementById('btn-add-cid');
    if (btnAddCid) {
      // Associa evento de clique para os botões de remover já existentes
      document.querySelectorAll('.btn-remove-cid').forEach(btn => {
        btn.addEventListener('click', (e) => {
          e.currentTarget.closest('.cid-input-group').remove();
        });
      });

      btnAddCid.addEventListener('click', () => {
        const container = document.getElementById('cid-container');
        const div = document.createElement('div');
        div.className = 'cid-input-group flex items-center gap-2 mb-2';
        div.innerHTML = `
          <input type="text" class="cid-input flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="Ex: G80" name="cid[]">
          <button type="button" class="btn-remove-cid p-2 rounded-lg bg-destructive/10 text-destructive hover:bg-destructive/20 transition-colors" title="Remover">
            <i data-lucide="x" class="w-4 h-4"></i>
          </button>
        `;
        container.appendChild(div);
        
        div.querySelector('.btn-remove-cid').addEventListener('click', () => {
          div.remove();
        });

        if (typeof lucide !== 'undefined') {
          lucide.createIcons();
        }
      });
    }

    <?php if (isAdminParcial()): ?>
      const form = document.querySelector('form');
      if (form) {
        const inputs = form.querySelectorAll('input, select, textarea, button');
        inputs.forEach(el => {
          if (el.type !== 'hidden' && el.getAttribute('href') === null && !el.classList.contains('bg-card')) {
            el.disabled = true;
          }
        });
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.remove();
        
        // Hide add/remove buttons for CIDs
        const btnAddCid = document.getElementById('btn-add-cid');
        if (btnAddCid) btnAddCid.style.display = 'none';
        document.querySelectorAll('.btn-remove-cid').forEach(btn => btn.style.display = 'none');
      }
    <?php endif; ?>
  });
</script>

<?php require_once __DIR__ . '/../../partials/admin_footer.php'; ?>
