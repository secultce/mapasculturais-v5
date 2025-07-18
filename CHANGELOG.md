# Changelog
Todas as mudanças notáveis no projeto serão documentadas neste arquivo.

O formato é baseado no [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/)
e este projeto adere ao [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [5.10.5] - 2025-06-25
### Adicionado
- Filtro de pretação de contas com TADO gerado
### Corrigido
- Nome do coordenador no TADO
- Ocultando o cpf do agenda na busca via api

## [5.10.4] - 2025-06-16
### Adicionado
- Envio de e-mails ao publicar os pareceres das avaliações
### Corrigido
- Adicionado script faltante para funcionamento da diligencia pela visão do proponente

## [5.10.3] - 2025-06-03
### Modificado
- Modificando view da planilha para não mostrar campos condicionais ocultos
- Remoção do modal de início
- Criado metodo para desabilitar entidades
- Remove valores dos metadados condicionados quando os campos não estão visíveis
- Alterando referência do plugin de recurso
- Alterando referência do plugin multiplelocalauth para layout de e-mail
- Alterando cache do assets
### Adicionado
- Adicionado imagem de destaque das entidades via api
- Adicionado exceções e mensagens de erro para serviço de cotas indisponíveis
### Corrigido
- Mantendo exequibilidade orçamentária como pendente

## [5.10.2] - 2025-05-16
### Modificado
- Reabertura de avaliação para admins e dono da oportunidade
- Alterando nomes no tado para Coordenador e Coordenador Finalístico
### Corrigido
- Proibindo distribuição de inscrições inválidas

## [5.10.1] - 2025-05-06
### Modificado
- Calculo e forma de apresentar valor de critérios de aceitação

## [5.10.0] - 2025-05-02
### Adicionado
- Logs de Exceptions para serviços
- Dispondo de eventos cancelados na api do endpoint de eventos
### Corrigido
- Correção ao cancelar envio na resposta de diligência
- Correção de notificação ao fiscal
- Remoção de notificação de diligência ao usuário
- Correção na obrigatoriedade de critérios em habilitação documental
### Modificado
- Evita duplo clicks em importar inscrições


## [5.9.11] - 2025-03-26
### Modificado
- Atualização a referência ao plugin de Recurso e Login
- Contagem de limite de vagas para oportunidade
- Notificação quando o Coletivo for obrigatório

## [5.9.10] - 2025-03-10
### Modificado
- Atualizado arquivo cache.php para ambiente de produção

## [5.9.9] - 2025-03-10
### Modificado
- Atualizando a referência do plugin de Recurso

## [5.9.8] - 2025-02-17
### Modificado
- Atualizando a referência do plugin de Recurso
- Corrigido textos da Prestação de Contas

## [5.9.7] - 2025-02-10
### Modificado
- Ativação para LGPD

## [5.9.6] - 2024-11-29
### Corrigido
- Corrigindo bug de metadata do relacionamento de entidade com selos

## [5.9.5] - 2024-11-25
### Corrigido
- Corrigido renderização da lista de inscritos na aba de avaliação

## [5.9.4] - 2024-11-18
### Corrigido
- Corrigido renderização da página em relatório de graficos

## [5.9.4-rc3] - 2024-11-14
### Corrigido
- Corrigido renderização da página em relatório de graficos

## [5.9.4-rc2] - 2024-11-14
### Corrigido
- Corrigido renderização da página em relatório de graficos.

## [5.9.4-rc1] - 2024-11-07
### Corrigido
- Corrigido renderização de campos da pré visualização de formulário.

## [5.9.3] - 2024-11-05
### Modificado
- Alterado versão do plugin de Autenticação
- Alterado arquivo de autenticação para uso local
- Removendo habilitação de reativar conta

## [5.9.2] - 2024-09-24
### Adicionado
- Área Prestação de Contas.
### Modificado
- Atualizando layouts de e-mails
### Corrigido
- Removendo mensagem de erro dos logs

## [5.9.2-rc4] - 2024-09-13
### Novas Funcionalidades
- Adicionado módulo para realizar rankeamento aleatório dentro de oportunidades com avaliação documental.
- Adicionada configuração de cotas a oportunidade.
- Adicionado módulo de heteroidentificação.
### Melhorias
- Adicionado logs às oportunidades.
- Modificação de algumas terminologias.

## [5.9.2-rc1] - 2024-07-09
### Melhorias
- Adicionado novo plugin no gitmodule
- Adicionado plugin para realizar Consulta Pública
- Adicionado Publicação do resultado dos sorteios
- Geração do resultado do sorteio em PDF
- Adicionados arquivos de configuração para bots de indexação
### Correções
- Adicionado arquivo cache

## [5.9.1] - 2024-06-25
### Melhorias
- Adicionado novo plugin no gitmodule
- Adicionado arquivo de configuração no ambiente de desenvolvimento
- Adicionado geração do relatório TADO
- Removido código desnecessário
### Correções
- Remoção de códigos desnecessários do plugin PDFReport

## [5.9.0] - 2024-06-14
### Melhorias
- Adiciona módulo de diligência.
- Atualiza versões de plugins de Recurso e Parecer.
- Adiciona feedback em caso de erro ao iniciar inscrição.
### Correções
- Corrige nome do hook 'importFields' na entidade Opportunity.
- Corrige listagem completa de agentes que esgotava o limite de memória.
- Corrige a busca de avaliações pelo filtro "não avaliadas".
- Limitar o usuário para no momento da inscrição só poder selecionar agentes que ele tem permissão.

## [5.8.16] - 2024-03-11
### Melhorias
- Adicionado novos repositórios dos plugins para o submodulos do git
- Adicionar o arquivo CI.yml para deploy automátizado
- Habilitado nas configurações do plugin do Parecer

## [5.8.15] - 2024-02-16
### Coreções
- Corrige a aba de avaliações da oportunidade onde poderiam algumas avaliações não serem exibidas e outras duplicadas.

## [5.8.14] - 2024-01-26
### Melhorias
- Corrige verificação do valor no hook de que pega os campos entre fases

## [5.8.13] - 2024-01-16
### Melhorias
- Cria novos hooks para inserção de dados dentro da tag head do html.

## [5.8.12] - 2023-12-14
### Correções 
- Corrige permissão para que gestores não administradores do sistema modifiquem avaliação após a fase ser publicada

## [5.8.11] - 2023-12-13
### Correções 
- Corrige permissão para que gestores modifiquem avaliação após a fase ser publicada

## [5.8.10] - 2023-12-12
### Correções 
- Corrige exibição de formulário das avaliações dos avaliadores para o gestor

## [5.8.9] - 2023-12-05
### Correções
- Corrige erro no processamento de campos de tadas informados no formulário de inscrição

## ['5.8.8'] - 2023-11-29
- Garante que o $entity seja carregado corretamente pegando a entidade carregada na tela no header single da inscrição

## ['5.8.7'] - 2023-11-04
- Evita que as politicas afirmativas possam ser salvas de forma parcial

## ['5.8.6'] - 2023-11-04
- Ajusta formato da data na população do front end da inscrição

## [5.8.5] - 2023-11-03
- Ajusta o deletePermissionsCache para evitar que o mesmo quebre caso não tenha uma lista de users passada

## [5.8.4] - 2023-10-18
### Melhorias não funcionais
- Novos hooks na tela de configuração de avaliadores

## [5.8.3] - 2023-10-18
### Correções
- Corrige a configuração manual dos avaliadores de uma inscrição pelos checkboxes na ficha de inscrição

## [5.8.1] - 2023-10-18
### Correções
- Corrige a distribuição de inscrições entre os avaliadores

## [5.8.0] - 2023-10-17
### Melhorias
- Impede que o número de processos de geração de cache e de jobs ultrapasse o número de processadores disponíveis
- Refatoração na criação dos pcache, processando somente para os usuários envolvidos
- Melhoria de performance na distribuição de inscrições entre os avaliadores

## [5.7.6] - 2023-09-12
### Melhorias
-  Permite que a busca de agentes no gerenciador de usuários faça busca pelo nomeCompleto, nomeSocial, CPF e agentes ainda em rascunho

## [5.7.5] - 2023-08-28
### Correções
- Cria mc-update para atualizar dados legados para a nova estrutura de condicionalidade de campos
- Corrige importação de formularios para interpretar a nova estrutura de condicionalidade de campos

## [5.7.4] - 2023-08-24
### Melhorias
- atualização das bibliotecas PHP para as últimas versões compatíveis com o PHP 7.2

## [5.7.3] - 2023-08-23
### Correções
- Corrige a altura dos gráficos de linha do módulo de relatorios das oportunidades

## [5.7.1] - 2023-07-21
### Correções
- Ajusta CSS do sidebar direito para evitar que o header sobrescreva os conteúdos dos editbox

## [5.7.0] - 2023-07-13
### Novas Funcionalidades
- Implementa novo método de avaliação EvaluationMethodQualification
- Implementa opção para condicionar campos do tipo a nexos
### Melhorias
- Ajusta definição de cores na listagem de avaliações na tela do avaliador nos métodos simples e documental
- Ajusta estrutura de condicionalidade de campo criando colunas específicas na tabela registration_field_configuration
- Não obriga mais um campos ser obrigatorio para ser condicionado a outro campo

## [5.6.21] - 2023-07-13
### Melhorias
- Faz refatorações para que o módulo de relatório busque campo da fase em questão e tambem fases anteriores
- Aplica novos filtros no módulo de relatórios
- Remove dados mínimos exigidos para geração de grafico no módulo de relatórios
- Ajusta posicionamento do filtro na tela do módulo de relatorios, deixando facil interpretar quais gráficos serão afetados com o filtro

## [5.6.20] - 2023-06-06
### Melhorias
- define o tempo de duração das sessões para 2 horas, por padrão
- possibilidade de configurar o tempo de duração das sessões pela variável de ambiente `SESSION_TIMEOUT`

## [5.6.19] - 2023-05-25
### Correções
- Cria pasta DoctrineProxies durante o build, corrindo redeploy da aplicação que estava quebrado por falta da pasta

## [5.6.18] - 2023-05-25
### Correções
- Melhorias na visualização das telas com tabelas de inscrições em dispositivos mobile

## [5.6.17] - 2023-05-18
### Correções
- Ajusta para que em oportunidades multifases, o preview do formulario das fases posteriores exibam as categorias evitando que os campos condicionados fiquem ocultos

## [5.6.16] - 2023-05-17
### Correções
- Corrige formulário de inscrição das fases, ocultando a seleção das categorias e dos agentes relacionados

### Melhorias
- Atualiza o Node.js para a versão 14
- Reestruturação dos arquivos Dockerfile para o processo de build aproveitar melhor os caches

## [5.6.15] - 2023-05-12
### Correções
- Corrige módulo de deníncia e contato para usar o nome do dono da entidade ao invez do usuário logado

## [5.6.14] - 2023-05-12
### Correções
- remove botões de adicionar subespaço e de adicionar subagente das páginas single e edit de agente e projeto, pois levavam para uma página que não existe mais

## [5.6.13] - 2023-05-12
### Correções
- corrige configuração de vinculação de espaço relacionado na configuração do formulário de inscrição em oportunidades

### Melhorias 
- adiciona MailHog no ambiente de desenvolvimento para teste dos envios

## [5.6.17] - 2023-05-18
### Correções
- Ajusta para que em oportunidades multifases, o preview do formulario das fases posteriores exibam as categorias evitando que os campos condicionados fiquem ocultos

## [5.6.16] - 2023-05-17
### Correções
- Corrige formulário de inscrição das fases, ocultando a seleção das categorias e dos agentes relacionados

### Melhorias
- Atualiza o Node.js para a versão 14
- Reestruturação dos arquivos Dockerfile para o processo de build aproveitar melhor os caches

## [5.6.15] - 2023-05-12
### Correções
- Corrige módulo de deníncia e contato para usar o nome do dono da entidade ao invez do usuário logado

## [5.6.14] - 2023-05-12
### Correções
- remove botões de adicionar subespaço e de adicionar subagente das páginas single e edit de agente e projeto, pois levavam para uma página que não existe mais

## [5.6.13] - 2023-05-12
### Correções
- corrige configuração de vinculação de espaço relacionado na configuração do formulário de inscrição em oportunidades

## [5.6.12] - 2023-04-17
### Correções
- corrige erro de js que estava quebrando a edição de oportuidades em algumas situações

## [5.6.11] - 2023-04-17
### Melhorias
- garante que os CPFs e CNPJs sejam exibidos e salvos formatados (XXX.XXX.XXX-XX e XX.XXX.XXX/YYYY-ZZ)
- unserialize nos metadados CPF e CNPJ que retorna do campo documento caso não esteja preenchido

## [5.6.10] - 2023-04-17
### Melhorias
- refatoração do db-update que popula os metadados dos documentos para rodar mais rapidamente

## [5.6.9] - 2023-04-17
### Melhorias
- melhora performance do endpoint que retorna avaliações
- melhora performance da criação dos caches de permissão das oportunidades e inscrições

## [5.6.8] - 2023-04-17
### Melhorias
- Faz com que sidebar direito, fique com scroll do campo dowloads sem max-higth no modo de edição da oportunidade

## [5.6.7] - 2023-04-06
### Correção
- Ajusta erro de carregamento dos campos liberados para os avaliadores em oportunidades multifases

## [5.6.6] - 2023-03-31
### Correção
- Evita que ao exportar a planila de inscritos se quebre quando a politicas afirmativas esta ativa e nao existe rule configurada
- Corrige erro ao salvar metadado que define se as politicas afirmativas estão ativas ou não
- Corrige front-end para que quando a politica afirmativa esteja ativa, o box de configuração permaneça aberto

## [5.6.5] - 2023-03-28
### Melhorias
- Adiciona botão para fazer a validação de erros da inscrição no formulario de inscrição
### Correção
- Redefine método saveRegistration para apenas salvar a inscrição e não computar erros do formulário
- Ajusta o scroll para que quando existir error no formulário ele pare corretamente sobre o elemento
- Ajusta método sendRegistration para ele só fazer a validação de erros após salvar a inscrição
- Limpa mensagem de erro do anexo após o envio de arquivos

## [5.6.4] - 2023-03-27
### Correção
- Na tela do avaliador, corrige o avanço automático das avaliações ao finalizar o processo em uma determinada inscrição

## [5.6.3] - 2023-03-24
### Melhorias
- Passa processo de passar dados do metadado documento para o metadados CPF ou CNPJ do db-update para o mc-update para melhorar performace

## [5.6.2] - 2023-03-14
### Correções
- Corrige filtro de avaliações na tela do avaliador
## [5.6.1] - 2023-03-10
### Correções
- Evita que ao importar inscrições entre fases, não seja disparado e-mail de notificação de envio da inscrição

## [5.6.0] - 2023-03-09
### Novas Funcionalidade
- No formulário de inscrição, exibe os erros de preenchimento no final da página para facilitar identificação dos campos com erros
- Impede que usuários comuns criem novos agentes individuais

### Melhorias
- Coloca scroll na modal de criação rapida de entidades
- Remove botões de criação de entidades da home
- Define forma de bloquear acesso a áreas que requer autenticação se os dados obrigatórios do agente não estão preenchidos
- Garante que todo agente principal seja criado como agente individual
- Faz com que somente administradores possam alterar o tipo do agente
- Remove opção de troca de perfil principal para evitar que sejam atribuídos perfis do tipo coletivo como agente principal para atender LGPD

### Correções
- Evita que o modal de criação rápida de entidades seja acessado quando o usuários não estiver autenticado
- Faz com que Job SendMailNotification retorne o status do disparo de e-mail para concluir o processo do Job e evitar erros

## [5.5.23] - 2023-04-17
### Melhorias
- melhora performance do endpoint que retorna avaliações
- melhora performance da criação dos caches de permissão das oportunidades e inscrições

## [5.5.15] - 2023-03-06
### Correções
- Ajusta verificação que evita que quebre quando não existe dados do lockedFields
## [5.5.14] - 2023-03-06
### Correções
- Evita que ao importar inscrições entre fases, não seja disparado e-mail de notificação da criação da inscrição
- Evita que quebre quando não existe dados do lockedFields

## [5.5.13] - 2023-02-24
### Correções
- Verifica se a taxonomia função existe antes de utilizar seus atributos

## [5.5.12] - 2023-02-17
### Correções
- Faz correção do bloqueio dos campos atravéz dos selos, caso seja bloqueado campos do tipo location

## [5.5.11] - 2023-02-16
### Melhorias
- Melhora performance da criação de revisões de agentes com muitas propriedades

## [5.5.10] - 2023-02-10
### Correções
- Corrige erro da acentuação no upload de arquivos

## [5.5.9] - 2023-02-09
### Melhorias
- Cria template de e-mail para enviar certificado de solicitação de recurso ao proponente

## [5.5.8] - 2023-02-06
### Melhorias
- Cria hooks que possibilitam manipular sidebar direito na ficha de inscrição
- Cria novo template de e-mail para o recurso usando padrão header+footer+imagem
### Correções
- seta como vazio campo escolaridade do agent caso esteja com valor não informado
- Corrige função append do ajaxUpload para entender quando o registro do grupo do arquivo for para um único arquivo

## [5.5.7] - 2023-01-26
### Correções
- Corrige jsonSerialize da registration para quando a ficha é acessada por um gestor da oportunidade o mesmo poça ter acesso aos campos sem necessidade de liberação na permissão dos avaliadores

## [5.5.6] - 2023-01-18
### Correções
- bugfix registration_field_configuratios ao passar um campo tipo @ para um campo normal

## [5.5.5] - 2023-01-09
### Correções
- Evita que autosave execute na ficha de inscrição após a mesma estar enviada
- Corrige erros de carregamento do thumbnail do avatar nos templates referente ao agente caso nao encontre o avatar
- Corrige problema de duplicidade ao processar a planilha de importação de eventos
## [5.5.4] - 2022-12-30
### Correções
- corrige validação de CNPJ e CPF após criação dos novos metadados

## [5.5.3] - 2022-12-28
### Correções
- corrige redirecionamento após criação de apps

## [5.5.2] - 2022-12-27
### Correções
- Corrige validação e definição de campos nulos
- Formulário de inscrição envia valores falsos não nulos
- Validação de campos numéricos obrigatórios vazios

## [5.5.1] - 2022-12-23
### Correções
- Cria db-update para setar campos de CPF e CNP nos agentes com base no dados do campo documento

## [5.5.0] - 2022-12-22
### Correções
- Remove definição da configuração availableAgentFields no Module registrationFieldTypes
- Corrige carregamento dos campos @ na tela de configuração de campo garantindo que todos ja tenham sido registrados
- Corrige erro na tela do certificado que impedia a exibição ao tentar verificar a expiração do selo

### Melhorias
- atualiza o updateTimestamp da entidade quando modifica um metadado
- opção de bloqueio de campos das entidades seladas
- Cria hooks no modulo sendMailNotification
- Possibilita que seja possivel controlar disparo de e-mails de criação e aprovação no modulo sendMailNotification

### Novas Funcionalidade
- Bloqueio dos campos através dos selos
- Adciona novos campos para o cadastro de agente individual: 
    - Nome social
    - Escolaridade
    - Pessoa com deficiência
    - Comunidades tradicionais
    - CNPJ (MEI)
    - Pessoa idosa

## [5.4.10] - 2023-04-17
### Melhorias
- melhora performance do endpoint que retorna avaliações
- melhora performance da criação dos caches de permissão das oportunidades e inscrições

## [5.4.7] - 2023-02-16
- melhora performance da criação de revisões de agentes com muitas propriedades

## [5.4.6] - 2023-01-26
### Correções
- Corrige jsonSerialize da registration para quando a ficha é acessada por um gestor da oportunidade o mesmo poça ter acesso aos campos sem necessidade de liberação na permissão dos avaliadores

## [5.4.5] - 2023-01-09
### Correções
- Evita que autosave execute na ficha de inscrição após a mesma estar enviada
- Corrige erros de carregamento do thumbnail do avatar nos templates referente ao agente caso nao encontre o avatar
- Corrige problema de duplicidade ao processar a planilha de importação de eventos
## [5.4.4] - 2022-12-28
- Corrige redirecionamento após criação de App

## [5.4.3] - 2022-12-27
### Correções
- Corrige validação e definição de campos nulos
- Formulário de inscrição envia valores falsos não nulos
- Validação de campos numéricos obrigatórios vazios

## [5.4.2] - 2022-12-20
### Correções
- Evita que ao processar a planilha do importador de eventos, caia em timeout

## [5.4.1] - 2022-12-19
### Correções
- Corrige importador de enventos interpretanto Avatar, Banner e Galeria como campos obrigatórios

## [5.4.0] - 2022-12-14
### Correções
- Atualiza updateTimestamp das entidades quando modificado um metadado
### Novas Funcionalidade
- Importação de eventos por planilha
## [5.3.43] - 2023-04-17
### Melhorias
- melhora performance do endpoint que retorna avaliações
- melhora performance da criação dos caches de permissão das oportunidades e inscrições

## [5.3.42] - 2023-02-16
- melhora performance da criação de revisões de agentes com muitas propriedades

## [5.3.41] - 2023-01-26
### Correções
- Corrige jsonSerialize da registration para quando a ficha é acessada por um gestor da oportunidade o mesmo poça ter acesso aos campos sem necessidade de liberação na permissão dos avaliadores

## [5.3.39] - 2022-12-27
### Correções
- Corrige validação e definição de campos nulos
- Formulário de inscrição envia valores falsos não nulos
- Validação de campos numéricos obrigatórios vazios

## [5.3.38] - 2022-11-30
### Correções
- Corrige verificação de criação das taxnomias

## [5.3.37] - 2022-11-18
### Correções
- corrige definição de valor default para os metadados

## [5.3.36] - 2022-11-16
### Correções
- typo no módulo Notifications 
### Melhorias
- Cria novos campos no cadastro do agente

## [5.3.35] - 2022-11-16
### Correções
- Corrige erro ao gerar thumbnails do avatar

## [5.3.34] - 2022-11-10
### Correções
- Corrige erro ao enviar inscrições

## [5.3.33] - 2022-11-10
### Correções
- Corrige problema de não salvar a inscrição quando existe campos obrigatórios não preenchidos na ficha

## [5.3.32] - 2022-10-21
### Melhorias
- Cria módulo para importação de eventos atrvéz de planilha CSV

### Correções
- Aplica correções nos layouts de e-mails no novo módulo de disparo de notificações

## [5.3.31] - 2022-10-18
### Melhorias
- Cria módulo para para disparos de e-mails gerais no mapas culturais

## [5.3.30] - 2022-10-6
### Correções
- Instala LIB league/csv

## [5.3.29] - 2022-10-5
### Correções
- Corrige erro que permite o agente excluir um selo associado a ele

## [5.3.28] - 2022-09-13
### Correções
- Corrige db-update que faz a correção de eros nas inscrições entre fases
- Faz com que os fields sejam registrados recursivamente ao reconsolidar as avaliações

## [5.3.27] - 2022-09-09
### Correções
- Corrige busca recursiva dos fields nos casos de oportunidades multifases

## [5.3.26] - 2022-08-19
### Melhorias
- Cria dp-update que define permissão em todos os campos nas permissões dos avaliadores para oportunidades legadas 

## [5.3.25] - 2022-08-16
### Correções
- Corrige objeto Module para a chamada do método getChartsColors
- Corrige exportação do .csv quando existe campos de endereço do agente na inscrição
- Corrige busca de endereço agente por cep na inscrição

## [5.3.24] - 2022-08-12
### Correções
- Ajusta permissão dos avaliadores para nao quebrar quando o formulário estiver sem campos

## [5.3.23] - 2022-08-01
### Correções
- Faz com que o método dict, retorne o valor ao invez de imprimir
- Garante que a pasta SaaS seja sempre criada com a permissão correta
- Faz com que o método dict, retorne o valor ao invez de imprimir no arquivo space.php

## [5.3.22] - 2022-07-26
### Correções
- Corrige redirecionamento do subsite após edição e criação
- Garante que ao acessar a single do subsite esteja em modo de edição

## [5.3.21] - 2022-07-25
- Ajusta local de tipagem para array da variavel $_field_val
- Ajusta entripoint para setar permissão correta na pasta DoctrineProxies

## [5.3.20] - 2022-07-21
### Correções
- Remove chamadas de função iniexistente

## [5.3.19] - 2022-07-12
### Correções
- Corrige erro na contagem de avaliações

## [5.3.18] - 2022-06-23
- Revisa condicionais para exibir campos das politicas afirmatrivas para o avaliador e na planilha de inscritos

## [5.3.17] - 2022-06-10
- Remove atualização dos metadados dos campos @ do dp-update e passa para o mc-updates

## [5.3.16] - 2022-06-10
- Cria termo de autorização de uso de imagem padrão

## [5.3.15] - 2022-06-10
### Correções
- Garante que fique visivel campo do projeto para o proponente e o gestor, independente se existe liberação para o avaliador ou não no sistema de permissão dos avaliadores
- cria db-update que pega os dados relacionados a ficha e salva nos metadados
- Corrige erro que nao deixa exibir campo permitido ao avaliador, caso a categoria nao esteja liberada
- Ajusta telas de edição e visualização do cartão de visitas do agente para exibição da label dados pessoais somente se existir dados ou se estiver em modo de edição

## [5.3.14] - 2022-06-07
### Correções
- Evita criação duplicada de metadados
### Melhorias
- Novos hooks
  
## [5.3.13] - 2022-06-07
### Correções
- Melhora configuração do recapcha no módulo CompliantSuggestion
- Corrige texto das politicas afirmativas da planilha de inscritos que estava escrito errado
- Correção do cartão das entidades no campo descrição curta.
- Aplica o ajuste na de estilo tabela de critérios de avaliações de forma que nao afete outros inputs
- Impede que o proponente envie inscrições que estão em fases excluídas
- Melhora texto do toltip do botão enviar avaliações
- Impede que as avaliações sejam enviadas antes do prazo final da fase
- Evita que o nome do agente e categoria da inscrição seja exibida na listagem de inscrições a serem avaliadas na tela do avaliador caso não tenha permissão para visualizar
- Faz com que nas politicas afirmativas que a inscrição se enquandar, caso seja um campo multiseleção salve somente o campo correto e não todos
- Corrige erro de nao deixar associar espaço na tela de edição da inscrição 
- Corrige erro na política afirmativa que fazia o botão de adicionar administradores nao aparecesse na tela de edição da oportunidade
### Melhorias
- Trunca em 80 caractéres o tamanho do título do campo ao exibir no select de configuração das políticas afirmativa
- Ajusta classe do ID do campo na listagem de campos para liberação para o avaliador padronizando com a configuração do formulário
- Evita que necessite do número da residência para que se resolva uma geolocalização
- Ajusta tabela dos crtérios de avaliação para que nao quebre a estilização com mudanças de resolução
- Adiciona método para poder filtrar campo na sessão de permissão dos avaliadores
- Verifica se o arquivo Module.php esta presente antes de setar na lista de módulos ativos

## [5.3.12] - 2022-06-02
- Corrige erro ao salvar campo @ quando os mesmos já retornam preenchidos com dados do agente

## [5.3.11] - 2022-05-25
### Correções
- Define chave ENV para configurar chaves do recaptcha google no módulo CompliantSuggestion

## [5.3.10] - 2022-05-24
### Correções
- Aplica reverse na máscara do campo caso  o mesmo tenha aclasse  js-mask-currency

## [5.3.9] - 2022-05-23 
### Correções
- Corrige erro na abertura da tag php

## [5.3.8] - 2022-05-23 
### Melhorias
- Cria campo no agente chamado Agente agente itinerante

## [5.3.7] - 2022-05-23 
### Correções
- Ajuste nas fontes dos cartões das entidades
- Ajuste dos itens faltantes do cartão das entidades
- Ajuste da posição da tag
- Corrige css do select que define o tipo de um espaço
- Utiliza função mb_strlen para contar os caractéres da descrição curta, levando em conta que podem existir caracteres multibyte
### Melhorias
- Limita acesso ao botão de download da planila de agentes a administradores
- Informa numero de caracteres preenchidos na descrição curta do agente
- Ajusta limite de caractéres da descrição curta em todas as entidades
- Na listagem de eventos, faz com que filtro seja efetuado por padrão de 1 anos apartir da data atual
- No endpoint apiQueryByLocation, seta que o período de eventos pesquisados sejam de 1 anos apartir da data atual
- Quando o agente é redirecionado para a tela de edição por nao ter os dados mínimos preenchidos, eexibe opção de sair "Deslogar"
- Faz com que o filtro de eventos na gestão de usuários busque todos os eventos independente de existir espaço ou ocorrencias

## [5.3.6] - 2022-05-23 
- Corrige erro que nao deixava exibir campos condicionados a outros nas configurações das politicas afirmativas

## [5.3.5] - 2022-05-19 
## Correções
- Reconsolida a avaliação da inscrição caso em fases posteriores exista avaliação técnica com políticas afirmativas aplicadas
- Na planilha de inscritos, corrige exibição dos valores de politicas afirmativas atribídas a inscrição

## [5.3.4] - 2022-05-18 
## Correções
- Altera o texto das politicas afirmativas no botão de utilização e correção do bug do ckeckbox

## [5.3.3] - 2022-05-17
### Correções
- corrige validação de metadaos únicos na criação de entidades

## [5.2.13] - 2022-06-07
### Correções
- evita criação duplicada de metadados
- 
### Melhorias
- Novos hooks

## [5.2.12] - 2022-05-17
### Correções
- corrige validação de metadaos únicos na criação de entidades

## [5.3.2] - 2022-05-13
### Correções
- Corrige listagem de campos das fases anteriores que ficavam vazios em algumas situações
### Melhorias
- Libera avaliações antes do término das inscrições

## [5.2.11] - 2022-05-13
### Correções
- Corrige listagem de campos das fases anteriores que ficavam vazios em algumas situações
### Melhorias
- Libera avaliações antes do término das inscrições

## [5.3.1] - 2022-05-09
### Melhorias
- Altera mensagem das inscrições enviadas

## [5.3.0] - 2022-05-09
### Novas funcionalidades
- Implementa sistemas de permissão para os avaliadores
- Implementa calculo sobre políticas afirmativas nas avaliações técnicas
- Novo tipo de saída da api em tabela de texto: @type=texttable

## [5.2.10] - 2022-05-06
### Melhorias
- Após a inscrição ser enviada, pega os dados dos @campo diretamente do metadado e não mais do agente
  
## [5.2.9] - 2022-05-03
### Correções
- Corrige atualização de geo localização ao se atualizar endereço do agente
### Melhorias
- Revisão dos scripts e arquivos de configuração para ambiente de desenvolvimento
- Deixa o cadastro do agente em conformidade com a LGPD
- Implementa checagem do tamanho da string da descrição curta do agente no back-end para garantir que se tenha 400 caracteres

## [5.2.9]
### Correções
- Corrige a inserção de links nos selos e tela de edição de selos

## [5.2.8]
### Correções
- evita warnings em escripts que rodam na cli

## [5.2.7] - 2022-04-25
- Adiciona possibilidade para a não inclusão do hash nos assets publicados

### Correções
- Corrige a página de visualização do selo aplicado nas entidades

## [5.2.5] - 2022-04-25
- Corrige nomes dos arquivos dos assets publicados

## [5.2.4] - 2022-04-25
- Adição de hooks na tela de gestão de usuários
  
## [5.2.3] - 2022-04-18
- Remove chamada do método requireAuthentication desnecessário no template part info-admin

## [5.2.2] - 2022-04-14
### Correções
- Remove chamada do parent::__construct()  do controlador

## [5.2.1] - 2022-04-14
- Deixa as configuações do modulo LGPD por default  vazia para evitar redirecionamento sem configurações de termos

## [5.2.0] - 2022-04-14
### Novas funcionalidades
- Novo módulo LGPD com redirecionamento para aceitação dos termos de uso e politica de privacidade, se o usuário ainda não tiver aceito ou sempre que houver modificação nos textos dos termos
### Melhorias
- Novo formato de changelog
- Refatoração no trait MagicCaller para disparar exceção quando não existe o método, além de hook para criação de novos métodos (hook `Class::newMethod`)

### Correções
- altera identidade do usuário que executa os scripts de jobs e recriação de pcache pendente
- corrige várias chamadas para métodos inexistentes, que não davam erro por conta do trait MagicCaller

# [5.1.55] - 2022-03-25
### Correções
- Corrige o filtro por avaliador no endpoint findEvaluations
- Adiciona na API a possibilidade de filtrar pela permissão de outros usuários (@permissionsUser)

# [5.1.54] - 2022-03-24
### Correções
- Corrige filtro por categoria na aba de avaliações na tela do avaliador

# [5.1.53] - 2022-03-23
### Correções
- Corrige filtro de categorias no endpoint findEvaluation
- Seta valor padrão como desabilitado no plugin ProfileCompletion

## [5.1.52] - 2022-03-22
### Correções
- Desabilita chamada automárica para api/notification/find para evitar sobrecarga no servidor

## [5.1.51] - 2022-03-07
### Correção
- Corrige mascara campo de moeda
- Remove chamada de método desnecessário no módulo de relatŕorios
### Melhorias
- Insere hook para controlar exibição do botão de download de planilhas nas telas das entidades

## [5.1.50] - 2022-02-25
### Correção
- Corrige verificação de obrigatório nos campos que estão condicionados a outros campos do formulário #1928
- Aplica máscara de moeda no campo, independentemente do mesmo iniciar oculto ou não no carregamento do formulário #1931

## [5.1.49] - 2022-02-21
### Correção
- Ajusta exportação da planilha de inscritos, para que leve em consideração oportunidades multi-fases

## [5.1.48] - 2022-02-17
### Correção
- Aplica redução no nome do campo para evitar problemas na rename do arquivo de anexo no processo de upload Ref.: #1929

## [5.1.47] - 2022-02-03
### Correção
- Remove recriação de cache da oportunidade quando se envia um inscrição
- Remove lixos hashKey gerados pelo angularjs na tabela registration_meta

## [5.1.46] - 2022-02-02
### Correção
- Corrige bug na troca de agente responsável na fiche de inscrição

## [5.1.45] - 2022-01-31
### Correção
- Impede que campos obrigatórios sejam enviados com essas sujeiras e interpretados como valores verdadeiros

## [5.1.44] - 2022-01-24
### Correção
- Corrige erro no endpoint findEvaluation quando não existe avaliadores cadastrados na oportunidade #1874
- Evita que quem tenha permissão de edição na inscrição, veja o formulário de enviar prestação de contas #1871
- Remove o status todas da lista de status da listagem de inscrições #1868
- Possibilita inserir opções de seleção nos campos de seleção única quando o campo é do tipo @campo_agente_responsável ou @campo_agente_coletivo #1865
- Correção no texto do filtro de status no módulo de relatórios #1862
- Adiciona o status pendente na listagem de status permitido no botão de aplicar avaliações #1782

## [5.1.43] - 2022-01-19
### Correção
- Corrige erro de divisão por zero no módulo de relatórios

## [5.1.42] - 2022-01-14
### Melhorias

- Insere opções para cadastros das redes sociais Linkedin, Spotify, YouTube e pinterest nas entidades
## [5.1.41] - 2022-01-13
### Melhorias
- Remove opção de inserir link da rede social google+ do perfil do agente

## [5.1.40] - 2021-12-14
### Melhorias
- Faz com que perfis admins tenham acesso a aba de suporte
- Insere o campo função dentro do campo de listagem de pessoas (refs #1881)
- Após o envio das avaliações, não exibe mais o formulário de avaliação para os avaliadores (refs: #1876)

### Correções
- Corrige a lista de inscrições do formulário de avaliação das fases (refs: #1875)
- Evita possibilidade de conflito de nome de arquivo nos assets publicados em modo de desenvolvimento adicionando um hash do caminho completo do arquivo no nome do arquivo publicado

## [5.1.39] - 2021-11-22
### Melhorias
- Cria configuração default para as chaves mailer.bbc e mailer.replyto e possibilita setar por variáveis de ambiente

## [5.1.38] - 2021-11-22
### Correções
- Checa se metadado accountabilityPhase existe antes de exibir inscrições de prestação de contas no painel de controle
- Altera forma de atuação do autosave das avaliações para evitar que registre avaliações dublicadas
### Novas LIB's php
- Instala lib spreadsheet para geração de planilhas
### Melhorias
- Melhora função createMailMessage para ser capaz de interpetrar mailer.bcc e mailer.replyTo


## [5.1.37] - 2021-11-16
### Correções
- Ajusta autosave das avaliações para evitar requisições duplicadas ao finalizar a avaliação


## [5.1.36] - 2021-11-10
### Correções
- Ajusta momento de chamada do metodo includeGeocodingAssets


## [5.1.35] - 2021-11-10
### Correções
- remove resquício do LocationPatch


## [5.1.34] - 2021-11-10
### Correções
- remove resquício do LocationPatch


## [5.1.33] - 2021-11-09
### Correções
- Corrige erro ao enviar e salvar a prestação de contas

### Melhorias
- remove location patch do RegistrationFieldTypes e move código para um plugin independetnte

## [5.1.32] - 2021-11-08
### Melhorias
- Melhora filtro para pesquisar agente pelo nome na tela de avaliação

## [5.1.31] - 2021-11-05
### Melhorias
- Ajusta estilo para adaptar área de atuação e linguanes na nova posição

## [5.1.30] - 2021-11-04
### Melhorias
- Altera posição do campo área de atuações nas telas de agentes e espaços e linguagem nas telas de ventos para melhorar visualização no mobile

## [5.1.29] - 2021-10-26
### Melhorias
- Verifica se a caixa de atribuição de condicional esta marcada para que evite o campo obrigatório seja enviado sem dados

## [5.1.28] - 2021-10-22
### Melhorias
- Limpa dados dos inputs e selects quando desmarca checkbox que define as condicionais dos campos evitando que os campos obrigatórios sejam enviados vazios

### Correções
- corrige erro fatal no módulo de prestação de contas
- Ajusta exibição de inscrições no painel de gerênciamento de usuários Ref.: #1852

## [5.1.27] - 2021-10-19
### Melhorias
- log dos template hooks em comentários html quando a aplicação está em modo de desenvolvimento

## [5.1.26] - 2021-10-18
### Melhorias
- coloca o template part da seleção de espaços para dentro do controller de oportunidade
- refatora seleção de agentes e espaço na ficha de inscrição para ficarem dentro do controller de inscrição
- melhoria no feedback de erro no formulário de inscrição
### Correções
- Escapa valores do enum na procedure de exclusão de órfãos
- Corrige mensagem de erro do botão enviar inscrição
- Corrige exibição de erros dos campos de categoria e de agentes relacionados na ficha de inscrição
- corrige botão de salvar ficha de inscrição

## [5.1.25] - 2021-10-15
### Melhorias
- refatora autosave das entiades para salvar após 60 segundos da última modificação
- evita o enfileiramento desnecessário de objetos na fila de reprocessamento de cache
- configuração do timeout para salvamento atomático de inscrições

## [5.1.24] - 2021-10-08
### Correções
- Corrige status das inscrições nas fichas das fases

## [5.1.23] - 2021-10-07
### Melhorias
- Faz com que os perfis admin, tenham sempre o link de suporte na ficha de inscrição

## [5.1.22] - 2021-09-23
### Melhorias
- Insere redirecionamento no final do endpoint de reconsolidação de avaliação

## [5.1.19] - 2021-09-13
### Correções
- corrige validação de metadados com serialize definido

### Melhorias
- Cria hook para tratamentos após a troca de status de uma inscrição

## [5.1.18] - 2021-09-09
### Melhorias
- Faz com que um agente que é avaliador tambem possa ser um agente de suporte

## [5.1.17] - 2021-09-03
### Correções
- Corrige erro ao rodar pcache_pending

## [5.1.13] - 2021-08-25
### Correções
- Remove duplicidade da informação required nos campos de telefone e data
- Faz correção no sufixo dos hooks "reports-footer e home-searsh-form" nos arquivos opportunity-reports.php e home-search.php 
- Corrige merge dos campos em oportunidades multi-fases

### Melhorias
- Formata cambos do tipo checkbox, para que sejam exibidos de forma mais organizada no formulário
- Padroniza texto de "Campos obrigatório" nos campos formulários

## [5.0.0] - 2021-03-31
### Funcionalidades
#### Novas funcionalidades
- Modais para criação de entidades com os campos obrigatórios (refs: culturagovbr/mapasculturais#148)
- Remoção de conta do usuário com possibilidade de transferência das entidades para outro usuário (refs: culturagovbr/mapasculturais#213)
- Privacidade dos arquivos de acordo com o status da entidade dona do arquivo
- Avaliador pode acompanhar, em seu painel admin, editais que possuem mais de uma fase
- Concessão de permissões por meio de procurações
- API para confirmar presença e interesse em eventos
- No painel dos administradores, adiciona nova aba para exibir todos os selos que ele tem permissão de editar
- Painel de gestão de usuários
    - Admins podem alterar email e password de agentes pelo painel de controle (refs #1490)
- Painel de gestão de administradores
- Habilitação de login e cadastros por dispositivos móveis
- Novos endpoints para validação de campos e de entidades, sem a necessidade de mandar salvar a entidade (`/{entity}/validateProperties` e `/{entity}/validateEntity`)
- Adiciona termos Artes Circenses, Ópera e Patrimônio Cultural à lista de áreas de atuação
- Adiciona configuração para imagem de compartilhamento dos subsites
- Novas opções de gênero

#### Melhorias
- Atualização das bibliotecas PHP para as versões mais novas compatíveis com o Mapas Culturais (por conta disto deixa de suportar PHP versão < 7.2)
- Impede que os selos certificadores sejam excluídos
- Melhoria de performance na listagem de avaliações
- Adiciona recaptcha no form de denúncia e de contato
- Melhorias na interface para dispositívos móveis
- Busca por cpf na busca geral de agentes e na página de gestão de usuários
- Aumenta o limite de caracteres das descrições curtas para 2000 (refs: #1529)
- Mensagem de alerta ao excluir entidades
- Adiciona link no número da incrição para o API output type HTML
- Adiciona o texto de observação do avaliador na visualização de avaliações simples

#### Melhorias nas oportunidades
- Implementado campos de oportunidade com obrigatoriedade condicionada ao valor de outro campo (refs #1501)
- Na configuração dos campos das oportunidades, adiciona filtro por categoria para exibir somente os campos da categoria selecionada
- Botão para aplicar as avaliações às inscrições das oportunidades com método de avaliação simplificada e documental
- Possibilidade de vinculação de espaço à ficha de inscrição de oportunidades
- Substitui os campos x-editable por inputs normais nos formulários de oportunidades (refs #1471)
- Melhora mensagens de erro dos campos das oportunidades (refs: #1478)
- No painel de controle > Minhas Oportunidades > Aba Concedidas, permite o usuario ver as oportundades que estao em rascunho que ele administra
- Proprietário da oportunidade pode devolver uma avaliação já enviada para que o avaliador realize a revisão/alteração da mesma
- Refatoração do importador de campos das oportunidades para funcionar com os novos tipos de campos
- Exibe o id do campo na configuração do formulário
- Possibilidade de adicionar campos do espaço e dos agentes responsável e coletivo para preenchimento na ficha de inscrição de oportunidade (refs #1467 e #1468) 
- Novo tipo de campo Caixa de verificação
- Novo tipo de campo `brPhone` para os formulários de inscrição em oportunidades
- Novo tipo de campo de listagem de pessoas nos formulários de oportunidades
- Novo tipo de campo para lista de links
- Adiciona possibilidade de informar valor diferente do label para campos de checkboxes e select.
- Avança para o próximo campo da oportunidade com a tecla enter ou setinha do celular (refs #1475)
- Endpoint para reconsolidar as avaliações das inscrições (`/opportunity/reconsolidateResults/{opportunity_id}`)
- Busca por palavra-chave na lista de inscrições buscando na api
- Botão para exportar as inscrições ainda não enviadas
- Campos de URL e email das fichas de inscrição agora abrem em novas abas do navegador
- Melhoria de performance na listagem de inscrições
- A definiçao de campos obrigatórios dos agentes deve ser feita adicionando os campos obrigatórios à ficha de inscrição (refs: #1467)
- Adiciona data e hora ao nome do arquivo das planilhas de inscritos
- Importação contínua de inscrições da fase anterior, possibilitando novas inscrições selecionadas serem importadas

#### Correções
- Corrige sobrecarga/timeout da página quando ocorre acesso direto à URL `/busca/#`, sem nenhum parâmetro.
- Força o tipo inteiro int no header de retorno dos metadados das consultas à api (header `API-Metadata`)
- Adiciona label para o campo `_type`
- Corrige validação do valor '0' em campos obrigatórios das oportunidades
- Corrige deleção de entidades e adiciona diálogo de confirmação antes da exclusão
- Corrige busca por ownerEntity na api de oportunidades
- Corrige seleção manual de avaliadores, que não exibia corretamente os avaliadores da inscrição
- Corrige link para inscrição no aba de avaliações para usuários administradores
- Corrige salvamento de critérios de avaliação técnica que, em algumas situações, podia deixar critérios órfãos de seções, quebrando o formulário de avaliação
#### Outros
- remove versão 1 da api de leitura

### Modificações nao funcionais no código

#### Utilidades
- Função para criar arquivo de documentação das configurações
- Script para executar scripts dentro do container de desenvolvimento
- Adiciona diagrama ER na pasta de documentação
- adiciona o `less` e o `vim` à imagem Docker

#### Melhorias e refatorações
- Possibilita a configuração de HTTPS pela variável de ambiente `MAPAS_HTTPS = true`
- Adiciona automaticamente funções `serialize` e `unserialize` para metadados do tipo `json`
- Refatoração do arquivo base de configurações e do arquivo de configuração para desenvolvimento;
- Melhorias nos scripts de desenvolvimento;
- Possibilita configuração de inicialização do php (ini_set) por rota utilizando a configuraçao ini.set
- nos PUT e PATCH das entidades, utiliza as funções `delete`, `undelete`, `archive`, `publish`, `unpublish` quando há mudança no status da entidade
- Transforma método `Entity::getEntityTypeLabel` estático
- Validação de upload de arquivos retornando mensagens de erro no lugar de status code 403
- Refatoraçao da criação dos caches de permissão para execurarem paralelamente impedindo criação de fila
- Modifica método `unregisterEntityMetadata` para permitir desresgistrar todos os metadados registrados
- Possibilidade de configurar o caminho da pasta de arquivos privados
- Adiciona ao Dockerfile suporte ao Redis para utilizaçào como cache 
- Possibilidade de configurar o local de salvamento das sessões (caminho dos arquivos ou Redis)
- Refatoração do registro de tipos de campos das oportunidades, agrupando-os todos no módulo `RegistrationFieldTypes`
- Substitui o `uglify-js` pelo `terser` para possibilitar utilização de ECMAScript >= 6 (refs: #1488)
- Possibilidade de definição de valor padrão para os metadados (refs: #1477)
- Melhora a performance do formulário de inscrição renderizando utilizando one way data binding onde possível (Refs: #1483)
- Adiciona possibilidade de definir um valor padrão para o `RegistrationFieldType`
- Expõe a configuração do `RegistrationFieldConfiguration` no json da entidade
- Faz o segundo parâmetro da função env ser opcional
- Adiciona pacote para validação de contas bancárias
- Refatora métodos `disableAccessControl` e `enableAccessControl` utilizando um contador para possibilitar chamadas aninhadas
- Para uma inicialização mais rápida, no entrypoint da imagem docker só executa o `deploy.sh` quando a versão do mapas mudar, se não executa somente o `db-update.sh`
- Possibilidade de configurar o prefixo do número das inscrições
- Atualiza bibliotecas PHP
- Refatora as colunas `object_type` para serem do tipo ENUM e não do tipo VARCHAR
- Remove o `final` do construct do AuthProvider para possibilitar utilização de valores padrão nos provedores de autenticação
- Considera que o post no index (`EntityControlle::POST_index`) vem por ajax
- Remove o cache do apt para reduzir o tamanho da imagem
- Adiciona `Trait\RepositoryKeyword` ao repositório de inscriçòes (`Repositories\Registration`)
- Possibilidade de configuração do tile server dos mapas pela chave `maps.tileServer` ou pela variável de ambiente `MAPS_TILESERVER`
- Arquivos de proxy do Doctrine agora ficam na pasta `protected/DoctrineProxies` e são gerados automaticamente se não existirem
- Cria a pasta `private-files` se ela não existir
- Registra metadado cnpj e razaoSocial para os espaços
- Adiciona metodo `findByProjectAndOpportunityMeta` no repositorio de oportunidade
- Possibilidade de bloquear edição de campos de oportunidades pela variável js `jsObject['blockedOpportunityFields']`
- Modifica o valor padrão da configuração `mailer.protocol` para `null`
- Adiciona método `findIds` à classe `ApiQuery`, que retorna somente os ids das entidades encontradas
- Move a função `apiQuery` do `Traits/ControllerAPI` para a classe `EntityController` para que possa ser utilizada mesmo em controllers que não usem o trait
- Criação paralela de caches de permissão `pcache`
- Só executa o flush do `persisPCachePendingQueue` se algum objeto foi criado
- Refatora a coluna `action` da tabela `pcache` para ser do tipo ENUM e não do tipo VARCHAR
- Refatora o log do `pcache` adicionando na mensagem o tempo de execução
- Evita que o pcache de um mesmo usuário seja processado mais de uma vez para o mesmo objeto
- Possibilita que um objeto não seja colocado na fila de recriação de cache, utilizando a propriedade `__skipQueuingPCacheRecreation = true`,
- adiciona funcao `findRegistrationDateByIds` no repositorio de `Opportunity` (refs #1568)
- Refatora maneira como os avaliadores são desativados no banco (refs #1658)
- Refatoração do registro de roles para permitir a criação de novos roles por plugins (refs: #1569)
- No entrypoint do container, modifica o status das entradas da fila de criação de cache de permissão para que volte a ser processado
- Nova classe de exception `BadRequest`
- Refatoração na classe `Controller` para possibilitar o instanciamento múltiplo de controladores
- Move o js do `MapasCulturais.AjaxUpload` e do `MapasCulturais.Remove` do `editable.js` para o `mapasculturais.js,` para que estejam disponíveis nas páginas que não são de edição
- Utiliza o método `Entity::setStatus()` nos traits que manipulam os status das entidades
- Melhora o autocomplete do método `App::repo()->find`
- Adiciona o método `registerRegistrationMetadata` no trait `Traits/RegisterFunctions`
- Refatora o `Opportunity::getSentRegistration` para só pegar do banco as inscrições enviadas
- Trata retorno da funcao `valueToString` dos métodos de avaliacao simplificada e documental para retornar o $value quando definido
#### Correções
- Adiciona função fixPosition na diretiva editBox
- Adiciona parâmetro ao Entity::getPropertiesMetadata para retornar também os nomes das colunas das propriedades
- Corrige criação de entidades de subsites após um `$entity->em->clear()`
- Corrige função `Traits\EntityMetadata::canUserViewPrivateData` que estava quebrando quando o usuário estava deslogado
- Correção dos teste para funcionar com as demais modificações e melhorias
- Correçào de vulnerabilidades (XSS)
- Atualiza api do cep aberto para versão 3 e adiciona arquivo de configuração para configurar o token no docker
- Dispara exceção caso tente definir o valor de um metadado não registrado
- Previne chamadas desnecessárias às apis de avaliações e inscrições
- Remove `catch(\Exception $e)` no `RoutManager` para que os erros 500 apareçam no log de erros
- Fixa a versão do composer para v1.10.16 para evitar a quebra do build por conta do lançamento do composer 2.0.0
- Corrige funçao `__env_not_false`
- Corrige captura de exception no `recreatePermissionsCache`
- Corrige erro na funcao `createZipOfEntityFiles` ao receber um array multidimensional (grupos de arquivos não únicos)
- Corrige nome da sequencia da tabela pcache no mapeamento do doctrine
- Corrige permissão de visualização de campos privados das inscrições para os avaliadores incluidos fora da distribuição das avaliações (precisa regerar o pcache para aplicar a correção) (refs #1630)
- Corrige verificação de permissão dos metadados de Registration
- Interrompe execução do script no caso de entrar no modo offline (configuração `app.offline`)
- Corrige `ErrorException` no `Trait\EntityAgentRelation` quando não havia usuários com controle da entidade, que podia quebrar a geração de `pcache`
- Corrige mensagem da revisão de entidade criada quando esta não é criada pelo endpoint
- Correções na verificação de permissão para avaliar incrição
#### Novos hooks
- template hooks no footer
- Hook `mapasculturais.isEditable` para manipular o resultado da função `Theme::isEditable`
- Template hook `main-header` com sufixos `begin` e `end` no `header.php`
- Yemplate hooks nas seções da home
- Hooks para manipular os assets: `assets(filename)` e `assets(filename):url`
- Template hooks nos headers das páginas do painel (`panel-header`)
- Template hook `categories-messages` na seção de configuração de opções (categorias) das oportunidades
- Hook para manipular permissões no formato `can(<EntityName>.action)`
- Executa o hook `auth.login` após definir o usuário logado
- Template hook na tabela de inscritos: `registration-list-header` e `registration-list-item`
- Hook `controller(opportunity).reconsolidateResult` para possibilidar manipulação do array de avaliações antes da reconsolidação
- Hook `entity(Registration).consolidateResult` para possibilitar a filtragem do resultado consolidado das avaliações 
- Template hooks no início e no fim das sidebars das singles: `sidebar-left` e `sidebar-right`
- Template hook `header-inscrito:actions` nos botões da tabela de inscritos nas oportunidades
- Hook `opportunity.registrations.reportCSV` para manipular o CSV de inscritos em oportunidades
- Template hook `entity-opportunities` na aba de oportunidades das entidades
- Template hooks nos formulários dos métodos de avaliação: `evaluationForm.<evaluationMethod>`
- Hook para manipular a mudança de status: `entity(<EntityName>).setStatus(<status>)`

#### documentação
- PHPDoc nas classes `App`, `Entities\AgenztRelation`, `Definitions\Taxonomy`, `Definitions\RegistrationFieldType`, `Definitions\FileGroup`, `Entities/RegistrationEvaluation.php`, `Entities/EvaluationMethodConfigurationAgentRelation.php`
- Correções no PHPDoc para o phpDocumentor
- Corrige PHPDoc do método `findByOpportunityAndUser` do repositório de avaliações
- Documentação das chaves dos arquivos de configuração

## [4.6.0] - 2019-07-22
### Novas Funcionalidades
- endpoint para criação de procurações
- endpoints para confirmar presença ou interesse em eventos
- endpoint para retornar informações da instalação
- seletor de status no header da ficha de inscrição para usuários com permissão para mudar o status da inscrição
- retorna a reccurrence_string no endpoint api/event/findOccurrences (string utilizada para confirmação / interesse de eventos)
- proprietários das oportunidades podem modificar o status das inscrições enviadas após o resultado da oportunidade ser publicado
- botão para proponente reenviar inscrição no caso de ter sua inscrição reaberta após a divulgação dos resultados

### Melhorias
- max-height nos menus dropdown da busca
- faz campos de url e email das fichas de inscrição abrirem em nova aba
- melhorias nos testes (Atualizado phpunit, novo factory para criação de entidades)
- possibilidade de rodar um único teste utilizando o script dev-scripts/run-tests.sh
- melhora o log de teste de conexão com o banco do entrypoint.sh
- novas traduções para o espanhol
- previne erro fatal quando ocorre erro ao tentar criar arquivo zip

### Correção
- correções nas documentações das APIs
- corrige permissãos de oportunidades herdadas da entidade dona da oportunidade
- corrige associação de eventos com projetos
- corrige erro da api de ocorrencia de eventos quando não encontrava nenhum espaço
- correções nos templates de email
- corrige erro que ocorria com selos vencidos
- remove redirecionamento específico do tema do MinC
- corrige problema que quebrava o edit-box do upload de arquivos no caso de haver um registrationFieldConfiguration com id igual ao do registrationFileConfiguration


## [4.5.1] - 2019-02-26
### Melhorias
- Marca a obrigatoriedade do campo "Área de atuação" dos agentes (ref. #121)
- Melhora ícones do painel admin, e highlight da página em questão gestão de usuários/admins (ref. #204)

### Correções
- Remove hooks usados apenas pelo tema baseminc para repositório apropriado
- Remove hook específico do tema baseminc para repositório apropriado

## [4.5.0] - 2018-12-20
### Melhorias
- Proprietário da oportunidade pode devolver uma avaliação já enviada para que o avaliador realize a revisão/alteração da mesma;
- Avaliador pode acompanhar, em seu painel admin, editais que possuem mais de uma fase;
- Agentes culturais agora não possuem mais acesso à inscrição da oportunidade RCV pelas URLs do Mapas;

#### Correção
- Corrige sobrecarga/timeout da página quando ocorre acesso direto à URL '/busca/#', sem nenhum parâmetro.
- 
## [4.4.9] - 2018-12-14
### Correções
- Corrige minificação dos scripts


## [4.4.8] - 2018-11-30
### Melhoria
- Exibe o número de inscrições conforme o filtro selecionado;

### Correções
- Corrige alguns casos onde o filtro da aba inscritos não funciona parâmetro "Inválida";
- Corrige typos na documentação


## [4.4.7] - 2018-11-13
### Correções
- corrige loop infinito no recreatePermissionCache quando há referência circular nas entidades


## [4.4.7.1] - 2018-11-22
### Melhorias
- Melhorias de usabilidade para admin ao adicionar/remover avaliadores para cada inscrição. (culturagovbr/mapasculturais#216)
  
### Correções
- Corrige link da avaliação na listagem geral para dono do edital (culturagovbr/mapasculturais#229)

## [4.4.6] - 2018-11-07
### Correções
- corrige link para download do arquivo de modelo em campos de anexo das inscrições

## [4.4.5.3] - 2018-10-29
### Correção
- Corrige erro na geração do report de avaliações técnicas sem campo de exequibilidade

## [4.4.5.2] - 2018-10-26
### Correção
- Corrige exibição de eventos juntamente com agentes no filtro dos mesmos

## [4.4.5] - 2018-10-19
### Melhorias
- Exibe número total de inscrições e avaliações nas respectivas abas, orientando melhor o dono do edital e avaliadores

## [4.4.4] - 2018-10-17
### Melhorias
- Inclui inscrições não avaliadas na exportação da planilha para o proprietário do edital;
- Melhorias nas labels relacionadas à exequibilidade da inscrição nas exportações; 

## [4.4.3] - 2018-10-11
### Melhorias
- Proprietário da oportunidade não pode alterar notas das avaliações 
### Ajustes
- Aprimora verificação de preenchimento do campo de exequibilidade 

## [4.4.2] - 2018-10-10
### Melhorias
- Novas traduções
- Possibilita que o tema defina um arquivo image-transformations.php
- Correção do botão remover agente da ficha de inscricao
- Refatora scripts de desenvolvimento com docker
- Adiciona script para rodar os testes com o docker

## [4.4.1] - 2018-10-05
- melhorias relacionadas ao permission cache
- correção no deploy da refatoração do número da inscrição


## [4.4.0] - 2018-10-10
### Melhorias
- o número da inscrição agora permanece o mesmo entre as fases das oportunidades
- refatoração da forma como o pcache é mantido atualizado: agora acontece por um processo em background no servidor


## [4.3.5] - 2018-10-10
### Melhorias
- Adiciona campo metadado `event_attendance`para registrar público presente nos eventos
- Adiciona critério de inabilitação por exequibilidade em avaliações técnicas


## [4.3.4] - 2018-10-10
### Melhorias
- Melhoria em botão de solicitação de recurso (módulo de oportunidades)
- Corrige Dockerfile, adicionando suporte JPEG ao GD


## [4.3.3] - 2018-09-14
### Correções
- Corrige exibição de caracteres HTML maliciosos 

## [4.3.2] - 2018-09-11
### Correções
- Corrige envio da notificação ao avaliador ao ser convidado pelo admin da oportunidade
- Corrige cores configuradas pelo painel não aplicadas no Tema Personalizável
- Corrige ícone "+" para adicionar um novo selo

## [4.3.1] - 2018-09-14
### Correções
- Corrige bug das cores personalizadas via painel. Elas não eram aplicadas ao tema extendido do Subsite (#211);
- Modificado range de datas dos datepickers para 10 anos pra frente;


## [4.3.0] - 2018-09-03
### Correções
- Corrige bug no css que agrupava todos os selos, no painel de subsite, ao adicionar um selo verificador (#209)
- Fixa a versão do browser-detector, para que funcione em versões mais antigas do php
- Substitui tags php de abertura e fechamento _'<?=' por '<?php echo...'_
- Corrige versão do BrowserDetector que quebrava instalações com php menor que 7.0
- Fix correções para não inserir registros na tabela pcache com usuários nulos
- Melhorias na tela de gerenciamento de usuários
- Adiciona constraint na tabela de oportuniades para não aceitar `agent_id` nulo

### Documentação
- Adiciona documentação da API para agentes, eventos, projetos, selos e espaços 

## [4.2.0] - 2018-08-17
### Diversos
- corrige verificação de permissão para visualização no caso de inscrições em rascunho
- previne erro ao criar nova fase quando a primeira fase ainda não tem as datas definidas
- corrige identificação de HTTPS quando rodando atrás de um proxy
- correção na tela de admin de usuários
- mudança de valor padrão para visibilidade de localização dos agentes

### Docker
- Dockerfile para utilização em produção com docker-compose.yml de modelo
- novo Dockerfile de desenvolvimento que roda com o built-in webserver do php e script para iniciar o desenvolvimento em apenar um comando

### Localizaçao
- possibilita que seja usado mais de um idioma para o mapas, que será traduzido de acordo com o navegador do usuário
- novas strings localizadas

### Oportunidades
- adiciona as colunas data e hora de envio das inscrições à planilha exportada com a lista de inscritos;
- melhora acompanhamento das inscrições em rascunho, possibilita que o gerente da oportunidade acesse inscrições com status rascunho;
- target blank no botão de download do regulamento das oportunidades

### melhorias na interface de filtragem das inscrições de oportunidades:
- alterada listagem de filtros de checkbox para dropdown;
- exibe as colunas selecionadas como tags;
- possibilita que o gerente filtre pelos campos de seleção única das fases anteriores

### melhorias na interface de filtragem das avaliações de oportunidades:
- adiciona filtro por status da avaliação
- adiciona filtros para avaliadores
- exibe total de avaliações selecionadas

## [4.1.2] - 2018-08-08
- Corrige link de login para redirecionar usuário após o login na página da oportunidade (ref. #143)
- Envia e-mails de contato para os admins do subsite via BCC, e mantém apenas um e-mail da entidade/responsável para receber pelo campo TO (ref #174)
- Corrige função env do config.php utilizado pelas imagens docker que não estava aceitando false como valor.
- Dockerfile de desenvolvimento e script para iniciar desenvolvimento.


## [4.1.1] - 2018-07-31
Corrige erro de tabela apontando para sequencia errada


## [4.1.0] - 2018-07-31

- Novo painel de administração de usuários

- Realizar logout do ID Cutura ao utilizar o menu sair;
- Alterar o serviço de geolocalização para utilizar o Google;
- Incluir diferenciação para o "Meu Perfil"; 
- Incluir o separador dos "Informações Geográficas";
- Correção ao utilizar geocoder do google na busca por endereço no Mapa;
- Correção de envio de mensages de contato para o email privado do agente responsável;

* No perfil do agente, exibe os grupos que ele faz parte em relacionamentos com outras entidades ([#157](https://github.com/culturagovbr/mapasculturais/issues/157))
* Faz com que os temas presente no diretório `themes` sejam ativados automaticamente. ([#170](https://github.com/culturagovbr/mapasculturais/issues/170))
* Adiciona o campo referente ao shortcuts (singleUrl por exemplo) ao chamar a API describe.
* Adiciona a opção `Meu Perfil` aos menus, direcionando para o agente padrão. ([#151](https://github.com/culturagovbr/mapasculturais/issues/151))
* Separa informações geográficas, geradas automaticamente, do endereço informado pelo agente ([#189](https://github.com/culturagovbr/mapasculturais/issues/189))
* Corrige bug ao utilizar Geocoder do Google e a busca por endereço no mapa ([#202](https://github.com/culturagovbr/mapasculturais/issues/202))
* Remove botão excluir definitivamente, problemas de permissão ([#160](https://github.com/culturagovbr/mapasculturais/issues/160))
* Fix envio de e-mails para email privado do agente responsável ([#174](https://github.com/culturagovbr/mapasculturais/issues/174))
* Cria hook para permitir mudar os destinatários dos forms de contato e denúncia ([#200](https://github.com/culturagovbr/mapasculturais/issues/200))
* Corrige retorno de URLs de arquivos privados via API ([#192](https://github.com/culturagovbr/mapasculturais/issues/192))
* Corrige update de dados geográfico ao posicionar o PIN no mapa ([#188](https://github.com/culturagovbr/mapasculturais/issues/188))

## [4.0.0] - 2018-07-30
* Corrige campo de bairro quando se utiliza um CEP geral de uma cidade e a informação de bairro vem vazia
* Adicona ao "describe" da API a lista dos grupos de arquivos disponíveis para o seletor @files.

* Faz com que a pesquisa por palavra-chave para eventos seja executada  usando o nome do espaço e o nome do evento.
* Corrige a posição dos marcadores de possição no mapa.
* Corrige os erros de interface para validação de campos de data e retorno JS de edição de ocorrências pendentes por usuários não-admin (Issue #1111)
* Adicona ao "describe" da API a lista dos grupos de arquivos disponíveis para o seletor @files.
* Altera a propriedade `app.geoDivisionsHierarchy` no config, adicionando o atributo `[showLayer]` para isso alterando a representação da estrutura de dados.
* Acrescenra taxonomias personallizadas na extração de planilhas (#145)
* Na página de inscrição em uma oportunidade, o campo "Agente responsável" já vem com o Agente Padrão do usuário logado preenchido
* Adiciona opção para ordenação na busca quando a opção de visualização por lista é selecionada.
* Após fazer login, redireciona usuário para página em que estava navegando
* Envia e-mails com as mensagens de denúncia apenas para os admins do subsite, e e-mails das mensagens de contato para os admins do subsite, a entidade e responsável

## [3.3] - 2017-08-17
### Novas Seleções na API (@SELECT)
- **isVerified** - retorna um booleano que diz se a entidade tem algum dos selos certificadores aplicado;
- **seals** - retornar todos os selos aplicados a entidade
- **verifiedSeals** - retorna os selos certificadores aplicados

### Correções
- internacionalização da diretiva angular que estava em espanhol
- chamada da API relations.{*,relation.{*}}


## [3.3.7] - 2017-12-13
### Alterações

- Adiciona tipo de espaço Núcleos de Produção Digital


## [3.3.6] - 2017-12-12
### Alterações

- redireciona o usuário para a edição do perfil enquanto ele não tiver o perfil publicado
- omite o botão arquivar para o agente padrão
- verifica a permissão de arquivar ou deletar ao mudar o status para ARCHIVED e TRASH
- habilita a action POST eventOccurrence.index para compatibilidade da api de escrita
- não permite que entidades sejam criadas com o owner ou parent não publicados (refs: culturagovbr/mapasculturais#108)
- melhor tratamento de erros 500 e 403 em requisições ajax
- corrige a resolução da prioridade entre hooks que utilizam regex
- melhorias e correções no módulo de denúncia/sugestões
- Correção de erro na visualização do botão de impressão de selos por usuários anônimos
- Correção da ordem de execução dos hooks com mesma prioridade


## [3.3.5] - 2017-09-19
### Correções

- Chamada por wildcard no @select da API para as relações das entidade (subquery)


## [3.3.4] - 2017-09-14
### Alterações

- retira a codificação das propriedades das entidades para evitar problemas de compatibilidade
- corrige #1302
- corrige serialização do objeto subsite no php 5.5


## [3.3.2] - 2017-09-02

### Correções

* Fix tipologias adiciona pontos de memoria a subsite-types
* Corrige o bug que estava impedindo o update_timestamp de ser atualizado
* Atualiza o update_timestamp baseado na data da última revisão


## [3.3.1] - 2017-08-17
### Correções
- Valida os campos impedindo que sejam utilizadas algumas tags html no conteúdo salvo.


## [3.2] - 2017-07-06
### Link para Instagram
Adiciona link para Instagram na lista de links das redes sociais das entidades

O usuário pode adicionar seu username do instagram no formato @username, e  o link o leva para seu perfil no site do Instagram

### Recorte manual de imagens
Permite que o usuário recorte manualmente as fotos enviadas para o avatar e para a imagem de cabeçalho

### Campo de descrição para imagens da galeria
Agora é possível adicionar uma descrição para as fotos da galeria, que são exibidas embaixo da imagem quando a galeria é aberta

### Substituição de textos
* Agora a interface não fala mais em "ocorrência" de eventos, mas em "local e data"
* A aba Permissões, que mostra os agentes que tem controle sobre a entidade, passa a se chamar Reponsáveis


### Correções
* Cor dos pins dos espaços nos subsites (https://github.com/culturagovbr/mapasculturais/issues/69)

### Exportação de planilha 
* Adicionado a possibilidade de gerar uma planilha a partir dos perfis de espaço, agente e projeto
* Mudando a estrutura da planilha de eventos, com os dados separados (ver https://github.com/hacklabr/mapasculturais/issues/1165)


## [3.2.2] - 2017-08-17
### Correçoes
- corrige array salvo com os ids dos selos verificadores do subsite para salvar os ids como inteiro
- o método register dos módulos não estava sendo chamado
- correção das permissões de superAdmin e admin entre subsites

### Ajustes
- hook para alteracao dos administradores das entidades tornando possível bypassar o subsiteId da entidade


## [3.2.1] - 2017-08-02
### Configuração de Filtros
Corrige a configuração da chave `options` de um filtro na busca, sem que o filtro precise estar relacionado a Metadata, EntityType ou Term.
Ex:
```
'label' => i::__('Selos'),
'placeholder' => i::__('Selecione os Selos'),
'fieldType' => 'checklist',
'type' => 'custom',
'isArray' => true,
'isInline' => false,
'filter' => [
    'param' => '@seals',
    'value' => '{val}'
],
'options' => [
    ['value' => '1', 'label' => 'Selo 1'],
    ['value' => '2', 'label' => 'Selo 2'],
    ['value' => '3', 'label' => 'Selo 3'],
]
```

### Correçoes
* Correção do plugin que insere botão para impressão do certificado
* Adicionado novo hook no seal relation, permitindo que metadados não listados no core sejam possíveis de serem impressos no texto do certificado
* Correção ao tentar ceder propriedade de um selo

### Ajustes
* Removendo comportamento de abrir o primeiro campo de edição obrigatorio quando tentar salvar e ele não tiver preenchido. (apenas avisa)
