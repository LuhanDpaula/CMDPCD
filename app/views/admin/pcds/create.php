<?php require_once __DIR__ . '/../../partials/admin_header.php'; ?>

<div class="bg-card rounded-2xl border border-border overflow-hidden max-w-4xl mx-auto mb-10" style="box-shadow: var(--card-shadow)">
  <div class="p-6 border-b border-border">
    <h2 class="text-xl font-bold">Cadastrar Novo PCD</h2>
    <p class="text-sm text-muted-foreground">Registre uma pessoa com deficiência diretamente no banco de dados.</p>
  </div>

  <?php if (!empty($error)): ?>
    <div class="bg-destructive/10 text-destructive text-sm p-4 mx-6 mt-6 rounded-xl border border-destructive/20 flex items-center gap-2">
      <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
      <span><?= htmlspecialchars($error) ?></span>
    </div>
  <?php endif; ?>

  <form action="<?= BASE_URL ?>/admin/pcds/store" method="POST" enctype="multipart/form-data" class="p-6 space-y-8">
    <?= csrf_field() ?>
    
    <!-- 1. IDENTIFICAÇÃO -->
    <div class="space-y-4">
      <h3 class="text-md font-bold text-primary flex items-center gap-1.5 border-b border-border pb-2">
        <i data-lucide="user" class="w-5 h-5"></i> 1. Identificação do PCD
      </h3>
      <div class="grid sm:grid-cols-3 gap-4">
        <div class="sm:col-span-2">
          <label class="block text-sm font-medium mb-1">Nome completo *</label>
          <input type="text" name="nome_completo" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Nome social</label>
          <input type="text" name="nome_social" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">CPF *</label>
          <input type="text" id="cpf" name="cpf" required placeholder="000.000.000-00" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">RG *</label>
          <input type="text" name="rg" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Data de nascimento *</label>
          <input type="date" name="data_nascimento" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Sexo Biológico *</label>
          <select name="sexo_biologico" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
            <option value="">Selecione</option>
            <option value="masculino">Masculino</option>
            <option value="feminino">Feminino</option>
            <option value="outro">Outro</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Identidade de Gênero</label>
          <input type="text" name="genero" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Raça / Cor *</label>
          <select name="raca_cor" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
            <option value="">Selecione</option>
            <option value="branca">Branca</option>
            <option value="preta">Preta</option>
            <option value="parda">Parda</option>
            <option value="amarela">Amarela</option>
            <option value="indigena">Indígena</option>
            <option value="nao_declarado">Não declarado</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Nacionalidade *</label>
          <input type="text" name="nacionalidade" value="Brasileira" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Estado Civil</label>
          <select name="estado_civil" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
            <option value="">Selecione</option>
            <option value="solteiro">Solteiro(a)</option>
            <option value="casado">Casado(a)</option>
            <option value="divorciado">Divorciado(a)</option>
            <option value="viuvo">Viúvo(a)</option>
            <option value="outro">Outro</option>
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
          <input type="text" id="cep" name="cep" placeholder="00000-000" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div class="sm:col-span-2">
          <label class="block text-sm font-medium mb-1">Logradouro *</label>
          <input type="text" name="logradouro" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Número *</label>
          <input type="text" name="numero" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div class="sm:col-span-2">
          <label class="block text-sm font-medium mb-1">Complemento</label>
          <input type="text" name="complemento" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Bairro *</label>
          <input type="text" name="bairro" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Cidade *</label>
          <input type="text" name="cidade" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">UF *</label>
          <input type="text" name="uf" maxlength="2" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Telefone Principal *</label>
          <input type="text" id="telefonePrincipal" name="telefone_principal" placeholder="(00) 00000-0000" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Telefone Secundário</label>
          <input type="text" id="telefoneSecundario" name="telefone_secundario" placeholder="(00) 00000-0000" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">E-mail</label>
          <input type="email" name="email" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div class="sm:col-span-2">
          <label class="block text-sm font-medium mb-1">Contato de Emergência (Nome) *</label>
          <input type="text" name="contato_emergencia_nome" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Contato Emergência (Fone) *</label>
          <input type="text" id="contatoEmergenciaTelefone" name="contato_emergencia_telefone" placeholder="(00) 00000-0000" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
      </div>
    </div>

    <!-- 3. RESPONSÁVEL LEGAL -->
    <div class="space-y-4">
      <h3 class="text-md font-bold text-primary flex items-center gap-1.5 border-b border-border pb-2">
        <i data-lucide="shield-alert" class="w-5 h-5"></i> 3. Responsável Legal / Cuidador
      </h3>
      <div class="bg-muted/40 p-4 rounded-xl flex items-center gap-2 mb-4">
        <input type="checkbox" id="possuiResponsavel" name="possui_responsavel" value="1" class="w-4 h-4 accent-primary">
        <label for="possuiResponsavel" class="text-sm font-semibold text-foreground cursor-pointer">Marcar se o PCD possuir responsável legal ou cuidador</label>
      </div>
      <div id="responsavel-fields" class="grid sm:grid-cols-3 gap-4 hidden">
        <div class="sm:col-span-2">
          <label class="block text-sm font-medium mb-1">Nome completo do responsável *</label>
          <input type="text" id="responsavelNome" name="responsavel_nome" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">CPF do responsável *</label>
          <input type="text" id="responsavelCpf" name="responsavel_cpf" placeholder="000.000.000-00" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">RG do responsável *</label>
          <input type="text" id="responsavelRg" name="responsavel_rg" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Data de nascimento do responsável *</label>
          <input type="date" id="responsavelDataNascimento" name="responsavel_data_nascimento" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Parentesco *</label>
          <select id="responsavelParentesco" name="responsavel_parentesco" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
            <option value="">Selecione</option>
            <option value="mae">Mãe</option>
            <option value="pai">Pai</option>
            <option value="tutor">Tutor(a)</option>
            <option value="curador">Curador(a)</option>
            <option value="outro">Outro</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Telefone do responsável *</label>
          <input type="text" id="responsavelTelefone" name="responsavel_telefone" placeholder="(00) 00000-0000" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">E-mail do responsável</label>
          <input type="email" id="responsavelEmail" name="responsavel_email" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div class="flex items-center gap-2 sm:col-span-2 pt-2">
          <input type="checkbox" id="responsavelFormal" name="responsavel_formal" value="1" class="w-4 h-4 accent-primary">
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
            <label for="def_create_<?= $tipo['id'] ?>" class="flex items-center gap-1.5 text-sm font-semibold cursor-pointer">
              <input type="checkbox" id="def_create_<?= $tipo['id'] ?>" name="deficiencias_ids[]" value="<?= $tipo['id'] ?>" class="accent-primary w-4 h-4">
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
                      <div class="cid-input-group flex items-center gap-2 mb-2">
                        <input type="text" class="cid-input flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="Ex: G80" name="cid[]">
                      </div>
                    </div>
                    <button type="button" id="btn-add-cid" class="mt-1.5 text-xs text-primary font-bold flex items-center gap-1 hover:underline">
                      <i data-lucide="plus" class="w-3.5 h-3.5"></i> Adicionar outro CID
                    </button>
                  </div>
        <div>
          <label class="block text-sm font-medium mb-1">Grau de Deficiência *</label>
          <select name="grau_deficiencia" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
            <option value="">Selecione</option>
            <option value="leve">Leve</option>
            <option value="moderada">Moderada</option>
            <option value="severa">Severa</option>
            <option value="profunda">Profunda</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Data do Diagnóstico</label>
          <input type="date" name="data_diagnostico" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div class="sm:col-span-2">
          <label class="block text-sm font-medium mb-1">Tecnologia Assistiva Utilizada</label>
          <input type="text" name="tecnologia_assistiva" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="Cadeira de rodas, órtese, prótese, etc.">
        </div>
        <div class="flex items-center gap-2 pt-2">
          <input type="checkbox" id="necessitaAcompanhante" name="necessita_acompanhante" value="1" class="w-4 h-4 accent-primary">
          <label for="necessitaAcompanhante" class="text-sm font-medium">Necessita acompanhante</label>
        </div>
        <div class="sm:col-span-3">
          <label class="block text-sm font-medium mb-1">Medicações de uso contínuo</label>
          <textarea name="medicacao_continua" rows="2" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="Listar remédios utilizados diariamente"></textarea>
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
          <input type="number" step="0.01" name="renda_familiar" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Número de Dependentes *</label>
          <input type="number" name="numero_dependentes" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Situação Habitacional *</label>
          <select name="situacao_habitacional" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
            <option value="">Selecione</option>
            <option value="propria_quitada">Própria Quitada</option>
            <option value="propria_financiada">Própria Financiada</option>
            <option value="alugada">Alugada</option>
            <option value="cedida">Cedida</option>
            <option value="outra">Outra</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Escolaridade *</label>
          <select name="escolaridade" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
            <option value="">Selecione</option>
            <option value="sem_instrucao">Sem Instrução</option>
            <option value="fundamental_incompleto">Fundamental Incompleto</option>
            <option value="fundamental_completo">Fundamental Completo</option>
            <option value="medio_incompleto">Médio Incompleto</option>
            <option value="medio_completo">Médio Completo</option>
            <option value="superior_incompleto">Superior Incompleto</option>
            <option value="superior_completo">Superior Completo</option>
          </select>
        </div>
        <div class="sm:col-span-2">
          <label class="block text-sm font-medium mb-1">Ocupação Atual / Trabalho</label>
          <input type="text" name="ocupacao_atual" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
        </div>
        <div class="sm:col-span-3">
          <div class="flex items-center gap-2 mb-3">
            <input type="checkbox" id="recebeBpcLoas" name="recebe_bpc_loas" value="1" class="w-4 h-4 accent-primary">
            <label for="recebeBpcLoas" class="text-sm font-medium">Recebe benefício BPC / LOAS</label>
          </div>
          <div id="bpc-fields" class="hidden">
            <label class="block text-sm font-medium mb-1">Número ou detalhes do BPC</label>
            <input type="text" id="beneficioBpcLoas" name="beneficio_bpc_loas" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
          </div>
        </div>
      </div>
    </div>

    <!-- 6. DOCUMENTOS -->
    <div class="space-y-4">
      <h3 class="text-md font-bold text-primary flex items-center gap-1.5 border-b border-border pb-2">
        <i data-lucide="files" class="w-5 h-5"></i> 6. Documentos Anexados
      </h3>
      <div class="grid sm:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium mb-1">Documento de Identidade (RG/CNH) *</label>
          <input type="file" name="doc_rg" accept=".pdf,.jpg,.jpeg,.png" required class="text-sm block w-full text-muted-foreground file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Comprovante de Residência *</label>
          <input type="file" name="doc_residencia" accept=".pdf,.jpg,.jpeg,.png" required class="text-sm block w-full text-muted-foreground file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Laudo Médico (com CID) *</label>
          <input type="file" name="doc_laudo" accept=".pdf,.jpg,.jpeg,.png" required class="text-sm block w-full text-muted-foreground file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
        </div>
      </div>
    </div>

    <!-- SUBMIT -->
    <div class="flex justify-end gap-3 pt-6 border-t border-border">
      <a href="<?= BASE_URL ?>/admin/pcds" class="px-6 py-2 border border-border text-foreground rounded-full hover:bg-muted transition-colors">
        Cancelar
      </a>
      <button type="submit" class="px-6 py-2 bg-primary text-primary-foreground font-bold rounded-full hover:bg-primary/90 transition-colors">
        Salvar Cadastro
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
        el.addEventListener('input', function(e) {
          let v = e.target.value.replace(/\D/g, "");
          for (let fmt of format) {
            v = v.replace(fmt.pattern, fmt.replace);
          }
          e.target.value = v.substring(0, length);
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
  });
</script>

<?php require_once __DIR__ . '/../../partials/admin_footer.php'; ?>
