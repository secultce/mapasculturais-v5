# Plugin OpinionManagement
Plugin para o Mapas Culturais.

### Features:
- Publicação automática dos pareceres de avaliações documentais ao publicar resultado;
- Capacidade do administrador visualizar os pareceres na inscrição.

&nbsp;
> [!NOTE]
> - Compatíbilidade testada com a versão ^5.x.x. do Mapas Culturais.
> - Para o melhor funcionamento no Mozzila Firefox, utilizar versão 121 ou superior.

### Publicação por inscrição

Na aba **Inscrições**, gestores de oportunidades documentais e técnicas com publicação
manual podem selecionar uma ou mais inscrições e usar **Publicar pareceres das
selecionadas**. A confirmação identifica as inscrições e informa a liberação ao
proponente. Inscrições sem avaliações ou com avaliações ainda não enviadas não
ficam disponíveis para seleção. A verificação usa a view `evaluations`, incluindo
avaliadores atribuídos que ainda não iniciaram a avaliação.

A seleção abrange somente inscrições carregadas na lista e é limpa ao mudar os
filtros. Cada requisição aceita até 500 inscrições; a consulta de situações divide
listas maiores em blocos. O servidor valida a seleção inteira antes de publicar.
O modo automático também é validado no servidor e impede a publicação selecionada,
mantendo disponível a consulta de situações. Na paginação, apenas inscrições novas
têm suas situações consultadas; filtros, publicação e atualização explícita
invalidam os estados anteriores. As marcas de publicação são lidas em lote, sem
carregar a coleção de metadados de cada inscrição.

O metadado privado `Registration.opinionPublication` registra data e usuário da
publicação. Apenas gestores da oportunidade podem gravá-lo. A marca global
`Opportunity.publishedOpinions` continua liberando publicações anteriores e os
fluxos de publicação geral e automática. A publicação individual não ativa essa
marca global. Notificações do fluxo geral excluem inscrições já publicadas
individualmente. Não há alteração de esquema do banco de dados.

Os fluxos individual e geral compartilham o limite de execução de 500 segundos
para o processamento das notificações. Esse limite é uma mitigação de timeout;
não há recuperação persistente de notificações interrompidas após a publicação.

Verificação isolada, sem inicializar o banco da aplicação:

```sh
php src/protected/vendor/bin/phpunit --do-not-cache-result --no-configuration --bootstrap src/protected/vendor/autoload.php tests/OpinionPublicationTest.php
php src/protected/vendor/bin/phpunit --do-not-cache-result --no-configuration --bootstrap src/protected/vendor/autoload.php tests/OpinionPublicationControllerTest.php
node tests/OpinionSelectionTest.js
```
