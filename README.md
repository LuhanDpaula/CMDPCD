# Documentação do PI - CMPCD Jaú

Esta documentação descreve o andamento do Projeto Integrador (PI) desenvolvido para o **CMPCD Jaú – Conselho Municipal dos Direitos da Pessoa com Deficiência de Jahu**, apresentando os objetivos, requisitos, regras de negócio e evolução do sistema institucional e de cadastro de pessoas com deficiência.

---

# Sumário

<details>
<summary><strong>Expandir Sumário</strong></summary>

- [1. Introdução e Objetivos](#1-introdução-e-objetivos)
  - [Objetivo Geral](#objetivo-geral)
  - [Objetivos Específicos](#objetivos-específicos)
- [2. Especificação de Requisitos](#2-especificação-de-requisitos)
  - [2.1 Requisitos Funcionais](#21-requisitos-funcionais-rf)
  - [2.2 Requisitos Não Funcionais](#22-requisitos-não-funcionais-rnf)
- [3. Estudo de Viabilidade](#3-estudo-de-viabilidade)
- [4. Modelo de Negócios Canvas](#4-modelo-de-negócios-canvas)
- [5. Design do Sistema](#5-design-do-sistema)
- [6. Protótipo](#6-protótipo)
- [7. Aplicação](#7-aplicação)

</details>

---

# 1. Introdução e Objetivos

## Contextualização

O **CMPCD Jaú (Conselho Municipal dos Direitos da Pessoa com Deficiência de Jahu)** é um órgão colegiado de caráter consultivo, deliberativo e fiscalizador, responsável por acompanhar, propor e fiscalizar políticas públicas voltadas às pessoas com deficiência no município.

Com o crescimento da demanda por organização, transparência e centralização de informações, surgiu a necessidade de um sistema digital capaz de armazenar dados, facilitar o acesso às informações institucionais e auxiliar a Secretaria de Assistência e Desenvolvimento Social no planejamento de ações inclusivas.

O projeto propõe o desenvolvimento de um **portal institucional integrado a um sistema de cadastro e gerenciamento de PCDs**, permitindo a geração de dados estatísticos e maior eficiência administrativa.

---

## Objetivo Geral

Desenvolver um site institucional para o **CMPCD Jaú**, integrado à Secretaria de Assistência e Desenvolvimento Social, que possibilite o cadastro e gerenciamento de informações de pessoas com deficiência (PCDs), funcionando como um banco de dados confiável para o planejamento de serviços de inclusão social.

---

## Objetivos Específicos

1. Criar um portal informativo com a história do conselho, missão, valores, organograma e galeria de presidentes.

2. Desenvolver um sistema de cadastro online para PCDs com upload obrigatório de documentos:
   - RG;
   - Comprovante de residência;
   - Laudo médico.

3. Implementar um espaço de transparência para divulgação de:
   - atas;
   - reuniões;
   - resoluções;
   - editais;
   - documentos oficiais.

4. Facilitar a importação de dados externos provenientes de fichas e planilhas de entidades parceiras como:
   - AMAE;
   - APAE;
   - CISC.

5. Garantir acessibilidade digital plena com:
   - alto contraste;
   - redimensionamento de fontes;
   - suporte a leitores de tela.

---

# 2. Especificação de Requisitos

## 2.1 Requisitos Funcionais (RF)

Os requisitos funcionais representam as funcionalidades obrigatórias do sistema.

| ID | Nome do Requisito | Descrição |
|---|---|---|
| RF01 | História Institucional | O site deve exibir a história do conselho, sua origem, evolução e principais marcos institucionais. |
| RF02 | Galeria de Presidentes | O sistema deve exibir fotos, nomes e períodos de mandato dos presidentes do conselho. |
| RF03 | Membros do Conselho | O portal deve apresentar os membros ativos do conselho e suas respectivas funções. |
| RF04 | Organograma Institucional | O site deve exibir a estrutura organizacional oficial do conselho. |
| RF05 | Formulário de Contato | Disponibilizar formulário contendo nome, e-mail, telefone, assunto e mensagem, além dos contatos institucionais. |
| RF06 | Integração com Redes Sociais | Integrar Instagram e Facebook, exibindo publicações recentes automaticamente. |
| RF07 | Espaço de Transparência Pública | Disponibilizar área para visualização e download de atas, resoluções, editais e documentos oficiais. |
| RF08 | Cadastro de PCD | Permitir cadastro completo de cidadãos com informações pessoais, socioeconômicas e de saúde, incluindo upload de documentos obrigatórios. |
| RF09 | Gestão de Cadastros | Permitir localizar, editar e atualizar registros já existentes. |
| RF10 | Painel Estatístico e de Índices | Exibir dashboards administrativos com gráficos, estatísticas e indicadores sobre a população PCD cadastrada. |
| RF11 | Gestão de Níveis de Acesso | Controlar permissões por perfis: Assistente Social, CMPCD e Administrador/Editor. |
| RF12 | Importação de Dados Externos | Permitir importação de dados via arquivos Excel (.xlsx/.csv) e fichas físicas digitalizadas. |

---

## 2.2 Requisitos Não Funcionais (RNF)

Os requisitos não funcionais definem características técnicas e de qualidade do sistema.

---

### 2.2.1 Segurança e Privacidade

| ID | Requisito | Descrição |
|---|---|---|
| RNF01 | LGPD e Privacidade | Dados individuais dos PCDs devem ser protegidos e acessíveis apenas por usuários autenticados. |
| RNF02 | Integridade dos Dados | O sistema deve validar campos obrigatórios para garantir precisão estatística. |

---

### 2.2.2 Usabilidade e Acessibilidade

| ID | Requisito | Descrição |
|---|---|---|
| RNF03 | Acessibilidade | O sistema deve seguir diretrizes WCAG, com suporte a leitores de tela, alto contraste e redimensionamento de fontes. |
| RNF04 | Responsividade | A interface deve adaptar-se automaticamente a desktop, tablets e smartphones. |

---

### 2.2.3 Desempenho e Tecnologia

| ID | Requisito | Descrição |
|---|---|---|
| RNF05 | Desempenho | O carregamento das páginas deve ocorrer em até 3 segundos em conexões estáveis. |
| RNF06 | Stack Tecnológica | O sistema será desenvolvido com HTML5, CSS3 (Bootstrap), JavaScript e MySQL. |
| RNF07 | Interoperabilidade | O sistema deve permitir exportação de relatórios em Excel, CSV e PDF. |

---

# 3. Estudo de Viabilidade

## Viabilidade Técnica

O projeto utiliza tecnologias web amplamente consolidadas, como:
- HTML5;
- CSS3;
- Bootstrap;
- JavaScript;
- MySQL.

Essas ferramentas possuem ampla documentação, baixo custo de implementação e facilidade de manutenção.

---

## Viabilidade Financeira

O sistema poderá ser hospedado em infraestrutura municipal ou em serviços de hospedagem de baixo custo, reduzindo despesas operacionais.

---

## Viabilidade Operacional

A interface será desenvolvida com foco em simplicidade e usabilidade, permitindo que os responsáveis da Secretaria de Assistência Social utilizem o sistema sem necessidade de conhecimento técnico avançado.

---

# 4. Modelo de Negócios Canvas

## Proposta de Valor

Centralizar informações e o censo PCD do município em uma plataforma digital segura, acessível e transparente.

---

## Segmentos de Usuários

- Conselheiros do CMPCD;
- Pessoas com deficiência;
- Secretaria de Assistência Social;
- Gestão Pública Municipal.

---

## Parceiros-Chave

- Prefeitura Municipal de Jahu;
- Secretaria de Assistência e Desenvolvimento Social;
- AMAE;
- APAE;
- CISC.

---

## Benefícios Esperados

- Maior organização dos dados;
- Transparência pública;
- Facilidade de gestão;
- Apoio ao planejamento de políticas públicas;
- Inclusão digital e acessibilidade.

---

# 5. Design do Sistema

## Paleta de Cores

| Cor | Código |
|---|---|
| Azul Primário | #2D9CDB |
| Azul Suave | #56CCF2 |
| Bege Claro | #F9F7F1 |
| Cinza Suave | #E0E0E0 |
| Preto Suave | #333333 |

---

## Tipografia

- Fonte principal: **Nunito**
- Fontes alternativas:
  - Arial;
  - Helvetica;
  - sans-serif.

---

## Diretrizes Visuais

- Interface limpa e acessível;
- Navegação simplificada;
- Alto contraste;
- Layout responsivo;
- Foco em acessibilidade digital.

---

# 6. Protótipo

O protótipo visual do sistema foi desenvolvido no **Figma**, permitindo a validação prévia da interface e experiência do usuário antes da implementação.

- Estruturação das telas institucionais;
- Simulação do cadastro de PCDs;
- Definição da identidade visual;
- Protótipo responsivo.

---

# 7. Aplicação

Atualmente, o sistema encontra-se em desenvolvimento, com foco na estruturação do banco de dados, construção das interfaces e implementação das funcionalidades administrativas.

## Tecnologias Utilizadas

- HTML5;
- CSS3;
- Bootstrap;
- JavaScript;
- MySQL;
- Git/GitHub.

---

## Funcionalidades em Desenvolvimento

- Sistema de autenticação;
- Importação de planilhas;

---


# Equipe do Projeto

- Lucas Antonio Ribeiro
- Luhan de Paula Ribeiro
- Samuel Custódio dos Santos

---

# Instituição

Projeto Integrador desenvolvido na Fatec-Jahu – 2026**.
