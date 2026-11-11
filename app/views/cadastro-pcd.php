<?php require_once __DIR__ . '/partials/header.php'; ?>

  <main class="pt-16">
    <section class="py-16 md:py-24 bg-card border-b border-border">
      <div class="container mx-auto px-4 text-center">
        <h1 class="text-3xl md:text-5xl font-black text-foreground mb-4 fade-in-up">Cadastro de PCD</h1>
        <p class="text-lg md:text-xl text-muted-foreground max-w-2xl mx-auto fade-in-up">Registre-se e contribua para políticas públicas mais efetivas</p>
      </div>
    </section>

    <!-- Tela de Sucesso (Oculta inicialmente) -->
    <section id="success-screen" class="py-20 bg-background hidden">
      <div class="container px-4 max-w-2xl mx-auto text-center zoom-in">
        <i data-lucide="check-circle" class="w-20 h-20 text-primary mx-auto mb-6"></i>
        <h2 class="text-2xl font-extrabold text-foreground mb-4">Cadastro Enviado!</h2>
        <p class="text-muted-foreground mb-8">Obrigado. Seus dados foram registrados com sucesso no sistema do conselho.</p>
        <button id="btn-novo-cadastro" class="bg-primary text-primary-foreground font-bold px-6 py-3 rounded-full hover:bg-primary/90 transition-colors">Novo Cadastro</button>
      </div>
    </section>

    <section id="form-screen" class="py-12 bg-background">
      <div class="container px-4 max-w-3xl mx-auto">
        <!-- Indicador de Passos -->
        <div class="flex items-center justify-center gap-1 mb-8 flex-wrap" id="step-indicators">
          <button class="step-btn text-xs px-3 py-1.5 rounded-full font-medium transition-colors bg-primary text-primary-foreground">1. Identificação</button>
          <button class="step-btn text-xs px-3 py-1.5 rounded-full font-medium transition-colors bg-muted text-muted-foreground">2. Endereço e Contatos</button>
          <button class="step-btn text-xs px-3 py-1.5 rounded-full font-medium transition-colors bg-muted text-muted-foreground">3. Responsável Legal</button>
          <button class="step-btn text-xs px-3 py-1.5 rounded-full font-medium transition-colors bg-muted text-muted-foreground">4. Deficiência</button>
          <button class="step-btn text-xs px-3 py-1.5 rounded-full font-medium transition-colors bg-muted text-muted-foreground">5. Socioeconômico</button>
          <button class="step-btn text-xs px-3 py-1.5 rounded-full font-medium transition-colors bg-muted text-muted-foreground">6. Documentos</button>
          <button class="step-btn text-xs px-3 py-1.5 rounded-full font-medium transition-colors bg-muted text-muted-foreground">7. Declaração</button>
        </div>

        <div class="bg-card rounded-2xl p-6 md:p-8 border border-border space-y-5" style="box-shadow: var(--card-shadow)">
          <h3 class="text-lg font-bold text-foreground flex items-center gap-2 mb-4">
            <i data-lucide="clipboard-list" class="w-5 h-5 text-primary"></i>
            <span id="step-title">Identificação do Requerente</span>
          </h3>

          <div id="form-error" role="alert" aria-live="assertive" class="hidden bg-destructive/10 text-destructive text-sm p-4 rounded-xl border border-destructive/20 mb-4">
            Preencha todos os campos obrigatórios corretamente.
          </div>

          <form id="cadastro-form" onsubmit="event.preventDefault();">
            
            <!-- Step 0: Identificação -->
            <div class="step-content block" data-step="0">
              <div class="grid sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                  <label class="block text-sm font-medium mb-1">Nome completo *</label>
                  <input type="text" id="nomeCompleto" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1">Nome Social</label>
                  <input type="text" id="nomeSocial" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1">Data de nascimento *</label>
                  <input type="date" id="dataNascimento" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1">CPF *</label>
                  <input type="text" id="cpf" placeholder="000.000.000-00" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1">RG *</label>
                  <input type="text" id="rg" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1">Sexo Biológico *</label>
                  <select id="sexoBiologico" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                    <option value="">Selecione</option>
                    <option value="masculino">Masculino</option>
                    <option value="feminino">Feminino</option>
                    <option value="outro">Outro</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1">Identidade de Gênero</label>
                  <input type="text" id="genero" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1">Raça / Cor *</label>
                  <select id="racaCor" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
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
                  <input type="text" id="nacionalidade" value="Brasileira" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1">Estado Civil</label>
                  <select id="estadoCivil" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
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

            <!-- Step 1: Endereço e Contatos -->
            <div class="step-content hidden" data-step="1">
              <div class="grid sm:grid-cols-3 gap-4">
                <div>
                  <label class="block text-sm font-medium mb-1">CEP *</label>
                  <input type="text" id="cep" placeholder="00000-000" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                </div>
                <div class="sm:col-span-2">
                  <label class="block text-sm font-medium mb-1">Logradouro *</label>
                  <input type="text" id="logradouro" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1">Número *</label>
                  <input type="text" id="numero" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                </div>
                <div class="sm:col-span-2">
                  <label class="block text-sm font-medium mb-1">Complemento</label>
                  <input type="text" id="complemento" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1">Bairro *</label>
                  <input type="text" id="bairro" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1">Cidade *</label>
                  <input type="text" id="cidade" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1">UF *</label>
                  <input type="text" id="uf" maxlength="2" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1">Telefone Principal *</label>
                  <input type="text" id="telefonePrincipal" placeholder="(00) 00000-0000" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1">Telefone Secundário</label>
                  <input type="text" id="telefoneSecundario" placeholder="(00) 00000-0000" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1">E-mail</label>
                  <input type="email" id="email" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                </div>
                <div class="sm:col-span-2">
                  <label class="block text-sm font-medium mb-1">Contato de Emergência (Nome) *</label>
                  <input type="text" id="contatoEmergenciaNome" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1">Contato Emergência (Fone) *</label>
                  <input type="text" id="contatoEmergenciaTelefone" placeholder="(00) 00000-0000" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                </div>
              </div>
            </div>

            <!-- Step 2: Responsável Legal -->
            <div class="step-content hidden" data-step="2">
              <div class="space-y-4">
                <div class="flex items-center gap-2 mb-4 bg-muted/40 p-3 rounded-xl">
                  <input type="checkbox" id="possuiResponsavel" class="w-4 h-4 accent-primary">
                  <label for="possuiResponsavel" class="text-sm font-semibold text-foreground cursor-pointer">Possui responsável legal ou cuidador</label>
                </div>
                <div id="responsavel-fields" class="grid sm:grid-cols-2 gap-4 hidden">
                  <div class="sm:col-span-2">
                    <label class="block text-sm font-medium mb-1">Nome completo do responsável *</label>
                    <input type="text" id="responsavelNome" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                  </div>
                  <div>
                    <label class="block text-sm font-medium mb-1">CPF do responsável *</label>
                    <input type="text" id="responsavelCpf" placeholder="000.000.000-00" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                  </div>
                  <div>
                    <label class="block text-sm font-medium mb-1">RG do responsável *</label>
                    <input type="text" id="responsavelRg" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                  </div>
                  <div>
                    <label class="block text-sm font-medium mb-1">Data de nascimento do responsável *</label>
                    <input type="date" id="responsavelDataNascimento" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                  </div>
                  <div>
                    <label class="block text-sm font-medium mb-1">Parentesco *</label>
                    <select id="responsavelParentesco" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
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
                    <input type="text" id="responsavelTelefone" placeholder="(00) 00000-0000" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                  </div>
                  <div>
                    <label class="block text-sm font-medium mb-1">E-mail do responsável</label>
                    <input type="email" id="responsavelEmail" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                  </div>
                  <div class="sm:col-span-2 flex items-center gap-2">
                    <input type="checkbox" id="responsavelFormal" class="w-4 h-4 accent-primary">
                    <label for="responsavelFormal" class="text-sm font-medium">É responsável legal constituído formalmente (tutela, curatela)?</label>
                  </div>
                </div>
              </div>
            </div>

            <!-- Step 3: Deficiência -->
            <div class="step-content hidden" data-step="3">
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-bold mb-2 text-primary">Tipos de Deficiência *</label>
                  <p class="text-xs text-muted-foreground mb-3">Marque todas as deficiências diagnosticadas (múltiplas permitidas):</p>
                  <div class="flex flex-wrap gap-4 bg-muted/20 p-4 rounded-xl border border-border">
                    <?php foreach ($tiposDeficiencia as $tipo): ?>
                      <label for="def_pub_<?= $tipo['id'] ?>" class="flex items-center gap-1.5 text-sm font-semibold cursor-pointer">
                        <input type="checkbox" id="def_pub_<?= $tipo['id'] ?>" name="deficiencias_ids[]" value="<?= $tipo['id'] ?>" class="deficiencia-checkbox accent-primary w-4 h-4">
                        <?= htmlspecialchars($tipo['nome']) ?>
                      </label>
                    <?php endforeach; ?>
                  </div>
                </div>
                <div class="grid sm:grid-cols-2 gap-4">
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
                    <select id="grauDeficiencia" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                      <option value="">Selecione</option>
                      <option value="leve">Leve</option>
                      <option value="moderada">Moderada</option>
                      <option value="severa">Severa</option>
                      <option value="profunda">Profunda</option>
                    </select>
                  </div>
                  <div>
                    <label class="block text-sm font-medium mb-1">Data do Diagnóstico</label>
                    <input type="date" id="dataDiagnostico" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                  </div>
                  <div>
                    <label class="block text-sm font-medium mb-1">Tecnologia Assistiva Utilizada</label>
                    <input type="text" id="tecnologiaAssistiva" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="Cadeira de rodas, aparelho auditivo, etc.">
                  </div>
                </div>
                <div class="flex items-center gap-2">
                  <input type="checkbox" id="necessitaAcompanhante" class="w-4 h-4 accent-primary">
                  <label for="necessitaAcompanhante" class="text-sm font-medium">Necessita de acompanhante para atividades básicas?</label>
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1">Medicações de uso contínuo</label>
                  <textarea id="medicacaoContinua" rows="2" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="Listar remédios utilizados diariamente"></textarea>
                </div>
              </div>
            </div>

            <!-- Step 4: Socioeconômico -->
            <div class="step-content hidden" data-step="4">
              <div class="grid sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium mb-1">Renda Familiar Mensal (R$) *</label>
                  <input type="number" step="0.01" id="rendaFamiliar" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1">Número de Dependentes *</label>
                  <input type="number" id="numeroDependentes" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1">Situação Habitacional *</label>
                  <select id="situacaoHabitacional" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
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
                  <select id="escolaridade" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
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
                  <input type="text" id="ocupacaoAtual" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="Estudante, aposentado, operador de produção, desempregado...">
                </div>
                <div class="sm:col-span-2 flex items-center gap-2">
                  <input type="checkbox" id="recebeBpcLoas" class="w-4 h-4 accent-primary">
                  <label for="recebeBpcLoas" class="text-sm font-medium">Recebe benefício BPC / LOAS?</label>
                </div>
                <div id="bpc-fields" class="sm:col-span-2 hidden">
                  <label class="block text-sm font-medium mb-1">Detalhes do benefício (Número ou Tipo)</label>
                  <input type="text" id="beneficioBpcLoas" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                </div>
              </div>
            </div>

            <!-- Step 5: Documentos -->
            <div class="step-content hidden" data-step="5">
              <div class="space-y-4">
                <div class="bg-primary/5 p-4 rounded-xl border border-primary/10 mb-4">
                  <p class="text-xs text-muted-foreground">Envie cópias dos documentos solicitados (PDF, JPG ou PNG, máx. 5MB cada). <em>Nota: Esta funcionalidade de armazenamento de arquivos será integrada de forma definitiva no próximo semestre. O sistema está salvando os metadados do cadastro.</em></p>
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1">Documento de Identidade (RG/CNH) *</label>
                  <input type="file" id="docRg" name="doc_rg" accept=".pdf,.jpg,.jpeg,.png" required class="text-sm block w-full text-muted-foreground file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1">Comprovante de Residência *</label>
                  <input type="file" id="docResidencia" name="doc_residencia" accept=".pdf,.jpg,.jpeg,.png" required class="text-sm block w-full text-muted-foreground file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1">Laudo Médico (com CID) *</label>
                  <input type="file" id="docLaudo" name="doc_laudo" accept=".pdf,.jpg,.jpeg,.png" required class="text-sm block w-full text-muted-foreground file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                </div>
              </div>
            </div>

            <!-- Step 6: Declaração -->
            <div class="step-content hidden" data-step="6">
              <div class="space-y-4">
                <div class="bg-muted/50 rounded-xl p-5 text-sm text-muted-foreground border border-border">
                  <p class="font-bold text-foreground mb-3 flex items-center gap-1.5"><i data-lucide="shield-alert" class="w-4 h-4 text-primary"></i>Declaração e Consentimento (LGPD)</p>
                  <p class="leading-relaxed">Declaro, para os devidos fins, que as informações prestadas são verdadeiras e assumo total responsabilidade por elas. Autorizo este Conselho Municipal dos Direitos da Pessoa com Deficiência a tratar e armazenar as informações fornecidas em conformidade com a Lei Geral de Proteção de Dados (LGPD), exclusivamente para fins estatísticos e de desenvolvimento de políticas públicas de acessibilidade.</p>
                </div>
                <div class="flex items-center gap-2 mt-4 bg-muted/20 p-3 rounded-lg">
                  <input type="checkbox" id="consent" class="w-4 h-4 accent-primary" required>
                  <label for="consent" class="text-sm font-semibold cursor-pointer">Li e concordo com os termos acima *</label>
                </div>
              </div>
            </div>

            <!-- Botões de Navegação -->
            <div class="flex justify-between pt-6 mt-6 border-t border-border">
              <button type="button" id="btn-prev" class="px-6 py-2 border border-border text-foreground rounded-full hover:bg-muted transition-colors disabled:opacity-50" disabled>Anterior</button>
              <button type="button" id="btn-next" class="px-6 py-2 bg-primary text-primary-foreground font-bold rounded-full hover:bg-primary/90 transition-colors">Próximo</button>
            </div>
          </form>

        </div>
      </div>
    </section>
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      let currentStep = 0;
      const totalSteps = 7;
      const stepTitles = [
        "Identificação do Requerente", 
        "Endereço e Contatos", 
        "Responsável Legal", 
        "Deficiência", 
        "Socioeconômico", 
        "Documentos", 
        "Declaração e Consentimento"
      ];
      
      const contents = document.querySelectorAll('.step-content');
      const indicators = document.querySelectorAll('.step-btn');
      const btnPrev = document.getElementById('btn-prev');
      const btnNext = document.getElementById('btn-next');
      const stepTitle = document.getElementById('step-title');
      const formScreen = document.getElementById('form-screen');
      const successScreen = document.getElementById('success-screen');
      const formError = document.getElementById('form-error');

      function updateUI() {
        // Update Content
        contents.forEach((el, i) => {
          if(i === currentStep) el.classList.remove('hidden');
          else el.classList.add('hidden');
        });
        
        // Update Indicators
        indicators.forEach((el, i) => {
          el.className = 'step-btn text-xs px-3 py-1.5 rounded-full font-medium transition-colors ';
          if(i === currentStep) el.className += 'bg-primary text-primary-foreground';
          else if(i < currentStep) el.className += 'bg-primary/20 text-primary';
          else el.className += 'bg-muted text-muted-foreground';
        });

        // Update Title
        stepTitle.textContent = stepTitles[currentStep];

        // Update Buttons
        btnPrev.disabled = currentStep === 0;
        
        if (currentStep === totalSteps - 1) {
          btnNext.innerHTML = '<i data-lucide="check-circle" class="w-4 h-4 inline mr-2"></i>Enviar Cadastro';
        } else {
          btnNext.innerHTML = 'Próximo';
        }
        if (typeof lucide !== 'undefined') {
          lucide.createIcons();
        }
      }

      function validateCurrentStep() {
        formError.classList.add('hidden');
        
        if (currentStep === 0) {
          // Identificação
          const fields = ['nomeCompleto', 'dataNascimento', 'cpf', 'rg', 'sexoBiologico', 'racaCor', 'nacionalidade'];
          for (let f of fields) {
            const el = document.getElementById(f);
            if (el && !el.value) {
              return false;
            }
          }
        }
        
        if (currentStep === 1) {
          // Endereço e Contatos
          const fields = ['cep', 'logradouro', 'numero', 'bairro', 'cidade', 'uf', 'telefonePrincipal', 'contatoEmergenciaNome', 'contatoEmergenciaTelefone'];
          for (let f of fields) {
            const el = document.getElementById(f);
            if (el && !el.value) {
              return false;
            }
          }
        }
        
        if (currentStep === 2) {
          // Responsável
          const possui = document.getElementById('possuiResponsavel').checked;
          if (possui) {
            const fields = ['responsavelNome', 'responsavelCpf', 'responsavelRg', 'responsavelDataNascimento', 'responsavelParentesco', 'responsavelTelefone'];
            for (let f of fields) {
              const el = document.getElementById(f);
              if (el && !el.value) {
                return false;
              }
            }
          }
        }
        
        if (currentStep === 3) {
          // Deficiência
          const checks = document.querySelectorAll('.deficiencia-checkbox:checked');
          if (checks.length === 0) {
            alert('Por favor, marque pelo menos uma deficiência.');
            return false;
          }
          const grau = document.getElementById('grauDeficiencia').value;
          if (!grau) return false;
        }

        if (currentStep === 4) {
          // Socioeconômico
          const fields = ['rendaFamiliar', 'numeroDependentes', 'situacaoHabitacional', 'escolaridade'];
          for (let f of fields) {
            const el = document.getElementById(f);
            if (el && !el.value) {
              return false;
            }
          }
        }

        if (currentStep === 5) {
          // Documentos
          const fields = ['docRg', 'docResidencia', 'docLaudo'];
          for (let f of fields) {
            const el = document.getElementById(f);
            if (el && el.files.length === 0) {
              return false;
            }
          }
        }
        
        return true;
      }

      btnNext.addEventListener('click', () => {
        if (!validateCurrentStep()) {
          formError.textContent = "Por favor, preencha todos os campos obrigatórios (*) deste passo antes de prosseguir.";
          formError.classList.remove('hidden');
          return;
        }

        if (currentStep < totalSteps - 1) {
          currentStep++;
          updateUI();
        } else {
          // Submit final
          const consent = document.getElementById('consent').checked;
          if (!consent) {
            alert('O consentimento com os termos da LGPD é obrigatório.');
            return;
          }
          
          enviarCadastro();
        }
      });

      btnPrev.addEventListener('click', () => {
        if (currentStep > 0) {
          currentStep--;
          updateUI();
        }
      });

      // Permite clicar nos steps anteriores
      indicators.forEach((btn, i) => {
        btn.addEventListener('click', () => {
          if (i <= currentStep) {
            currentStep = i;
            updateUI();
          }
        });
      });

      // Toggles para Responsável
      document.getElementById('possuiResponsavel').addEventListener('change', (e) => {
        const div = document.getElementById('responsavel-fields');
        if(e.target.checked) {
          div.classList.remove('hidden');
          document.getElementById('responsavelNome').required = true;
          document.getElementById('responsavelCpf').required = true;
          document.getElementById('responsavelRg').required = true;
          document.getElementById('responsavelDataNascimento').required = true;
          document.getElementById('responsavelParentesco').required = true;
          document.getElementById('responsavelTelefone').required = true;
        } else {
          div.classList.add('hidden');
          document.getElementById('responsavelNome').required = false;
          document.getElementById('responsavelCpf').required = false;
          document.getElementById('responsavelRg').required = false;
          document.getElementById('responsavelDataNascimento').required = false;
          document.getElementById('responsavelParentesco').required = false;
          document.getElementById('responsavelTelefone').required = false;
        }
      });

      // Toggles para BPC
      document.getElementById('recebeBpcLoas').addEventListener('change', (e) => {
        const div = document.getElementById('bpc-fields');
        if(e.target.checked) div.classList.remove('hidden');
        else div.classList.add('hidden');
      });

      // Máscaras de entrada
      function maskInput(id, regex, format, length) {
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

      // CPF
      const cpfFmt = [
        {pattern: /(\d{3})(\d)/, replace: "$1.$2"},
        {pattern: /(\d{3})(\d)/, replace: "$1.$2"},
        {pattern: /(\d{3})(\d{1,2})$/, replace: "$1-$2"}
      ];
      maskInput('cpf', /\D/g, cpfFmt, 14);
      maskInput('responsavelCpf', /\D/g, cpfFmt, 14);

      // Fones
      const foneFmt = [
        {pattern: /(\d{2})(\d)/, replace: "($1) $2"},
        {pattern: /(\d{5})(\d)/, replace: "$1-$2"}
      ];
      maskInput('telefonePrincipal', /\D/g, foneFmt, 15);
      maskInput('telefoneSecundario', /\D/g, foneFmt, 15);
      maskInput('contatoEmergenciaTelefone', /\D/g, foneFmt, 15);
      maskInput('responsavelTelefone', /\D/g, foneFmt, 15);

      // CEP
      const cepFmt = [
        {pattern: /(\d{5})(\d)/, replace: "$1-$2"}
      ];
      maskInput('cep', /\D/g, cepFmt, 9);

      // Função AJAX para submeter dados
      function enviarCadastro() {
        // Coleta deficiências
        const deficiencias = [];
        document.querySelectorAll('.deficiencia-checkbox:checked').forEach(chk => {
          deficiencias.push(parseInt(chk.value));
        });

        const formData = new FormData();
        formData.append('_token', <?= js_json(csrf_token()) ?>);
        formData.append('nome_completo', document.getElementById('nomeCompleto').value);
        formData.append('nome_social', document.getElementById('nomeSocial').value);
        formData.append('data_nascimento', document.getElementById('dataNascimento').value);
        formData.append('cpf', document.getElementById('cpf').value);
        formData.append('rg', document.getElementById('rg').value);
        formData.append('sexo_biologico', document.getElementById('sexoBiologico').value);
        formData.append('genero', document.getElementById('genero').value);
        formData.append('raca_cor', document.getElementById('racaCor').value);
        formData.append('nacionalidade', document.getElementById('nacionalidade').value);
        formData.append('estado_civil', document.getElementById('estadoCivil').value);
        formData.append('cep', document.getElementById('cep').value);
        formData.append('logradouro', document.getElementById('logradouro').value);
        formData.append('numero', document.getElementById('numero').value);
        formData.append('complemento', document.getElementById('complemento').value);
        formData.append('bairro', document.getElementById('bairro').value);
        formData.append('cidade', document.getElementById('cidade').value);
        formData.append('uf', document.getElementById('uf').value);
        formData.append('telefone_principal', document.getElementById('telefonePrincipal').value);
        formData.append('telefone_secundario', document.getElementById('telefoneSecundario').value);
        formData.append('email', document.getElementById('email').value);
        formData.append('contato_emergencia_nome', document.getElementById('contatoEmergenciaNome').value);
        formData.append('contato_emergencia_telefone', document.getElementById('contatoEmergenciaTelefone').value);
        formData.append('possui_responsavel', document.getElementById('possuiResponsavel').checked ? 1 : 0);
        formData.append('responsavel_nome', document.getElementById('responsavelNome').value);
        formData.append('responsavel_cpf', document.getElementById('responsavelCpf').value);
        formData.append('responsavel_rg', document.getElementById('responsavelRg').value);
        formData.append('responsavel_data_nascimento', document.getElementById('responsavelDataNascimento').value);
        formData.append('responsavel_parentesco', document.getElementById('responsavelParentesco').value);
        formData.append('responsavel_telefone', document.getElementById('responsavelTelefone').value);
        formData.append('responsavel_email', document.getElementById('responsavelEmail').value);
        formData.append('responsavel_formal', document.getElementById('responsavelFormal').checked ? 1 : 0);
        const cidInputs = document.querySelectorAll('.cid-input');
        const cids = Array.from(cidInputs).map(input => input.value.trim()).filter(v => v !== '');
        formData.append('cid', cids.join(', '));
        formData.append('grau_deficiencia', document.getElementById('grauDeficiencia').value);
        formData.append('data_diagnostico', document.getElementById('dataDiagnostico').value);
        formData.append('tecnologia_assistiva', document.getElementById('tecnologiaAssistiva').value);
        formData.append('necessita_acompanhante', document.getElementById('necessitaAcompanhante').checked ? 1 : 0);
        formData.append('medicacao_continua', document.getElementById('medicacaoContinua').value);
        formData.append('renda_familiar', document.getElementById('rendaFamiliar').value);
        formData.append('numero_dependentes', document.getElementById('numeroDependentes').value);
        formData.append('situacao_habitacional', document.getElementById('situacaoHabitacional').value);
        formData.append('escolaridade', document.getElementById('escolaridade').value);
        formData.append('ocupacao_atual', document.getElementById('ocupacaoAtual').value);
        formData.append('recebe_bpc_loas', document.getElementById('recebeBpcLoas').checked ? 1 : 0);
        formData.append('beneficio_bpc_loas', document.getElementById('beneficioBpcLoas').value);

        // Anexa deficiencias
        deficiencias.forEach(id => {
          formData.append('deficiencias_ids[]', id);
        });

        // Anexa arquivos
        const fileRg = document.getElementById('docRg').files[0];
        if (fileRg) formData.append('doc_rg', fileRg);

        const fileRes = document.getElementById('docResidencia').files[0];
        if (fileRes) formData.append('doc_residencia', fileRes);

        const fileLaudo = document.getElementById('docLaudo').files[0];
        if (fileLaudo) formData.append('doc_laudo', fileLaudo);

        btnNext.disabled = true;
        btnNext.textContent = 'Enviando...';

        fetch('<?= BASE_URL ?>/cadastro-pcd', {
          method: 'POST',
          headers: {
            'Accept': 'application/json'
          },
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          btnNext.disabled = false;
          if (data.success) {
            formScreen.classList.add('hidden');
            successScreen.classList.remove('hidden');
            successScreen.querySelector('.zoom-in').classList.add('visible');
          } else {
            formError.textContent = data.message || "Erro ao salvar os dados.";
            formError.classList.remove('hidden');
            formError.scrollIntoView({ behavior: 'smooth' });
            btnNext.innerHTML = '<i data-lucide="check-circle" class="w-4 h-4 inline mr-2"></i>Enviar Cadastro';
          }
        })
        .catch(error => {
          btnNext.disabled = false;
          btnNext.innerHTML = '<i data-lucide="check-circle" class="w-4 h-4 inline mr-2"></i>Enviar Cadastro';
          formError.textContent = "Houve um erro de rede ou comunicação com o servidor.";
          formError.classList.remove('hidden');
        });
      }

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

      document.getElementById('btn-novo-cadastro').addEventListener('click', () => {
        document.getElementById('cadastro-form').reset();
        document.getElementById('responsavel-fields').classList.add('hidden');
        document.getElementById('bpc-fields').classList.add('hidden');
        currentStep = 0;
        updateUI();
        successScreen.classList.add('hidden');
        formScreen.classList.remove('hidden');
      });
    });
  </script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
