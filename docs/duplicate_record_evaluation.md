# Bug: avaliações duplicadas em `registration_evaluation` (race condition)

> Status: causa raiz confirmada. Reprodução local ainda não alcançada
> (containers Docker são rápidos demais mesmo isolando sessões). Próximo
> passo: reproduzir usando banco em servidor externo (latência real), já
> solicitado ao time DevOps.

## Sintoma em produção

Ao avaliar uma inscrição, o avaliador às vezes acaba com **mais de uma linha**
em `registration_evaluation` para a mesma `(registration_id, user_id)`. Isso
corrompe o cálculo da nota final (`Registration::consolidateResult()`), que
considera todas as linhas encontradas para a inscrição.

Rota afetada: `POST /salva-avaliacao/{registration_id}/status:evaluated/`
→ `Controllers/Registration.php:436 POST_saveEvaluation()`.

## Causa raiz confirmada

Fluxo: `Controllers/Registration.php:436 POST_saveEvaluation()` →
`Entities/Registration.php:1372 saveUserEvaluation()`.

```php
// Entities/Registration.php:1345-1360
function getUserEvaluation(\MapasCulturais\UserInterface $user = null){
    ...
    $evaluation = App::i()->repo('RegistrationEvaluation')->findOneBy([
        'registration' => $this,
        'user' => $user
    ]);
    return $evaluation;
}

// Entities/Registration.php:1372-1392
function saveUserEvaluation(array $data, User $user = null, $evaluation_status = null){
    ...
    $evaluation = $this->getUserEvaluation($user);   // <-- SELECT (check)

    if(!$evaluation){
        $evaluation = new RegistrationEvaluation;      // <-- INSERT (act)
        $evaluation->user = $user;
        $evaluation->registration = $this;
    }

    $this->saveEvaluation($evaluation, $data, $evaluation_status); // save(true) = flush imediato
    return $evaluation;
}
```

Isso é um clássico **TOCTOU (time-of-check to time-of-use)**: duas
requisições concorrentes podem executar o `findOneBy` (check) antes que
qualquer uma tenha commitado o `INSERT` (act) subsequente. Ambas recebem
`null` e ambas criam uma nova linha.

Agravantes confirmados:

1. **Sem unique constraint** em `(registration_id, user_id)` — nem no schema
   (`db-updates.php:879-898`, só há índices simples + FKs), nem na entidade
   Doctrine (`Entities/RegistrationEvaluation.php:23-28`, sem
   `@ORM\Table(uniqueConstraints=...)`). O banco não impede a duplicata.
2. **Nenhum lock/transação** envolve o bloco check-then-act em lugar nenhum
   do fluxo. O codebase usa `beginTransaction/commit/rollBack` esparsamente
   em outros pontos, mas nunca locking pessimista
   (`LockMode::PESSIMISTIC_WRITE`, `pg_advisory_lock`) — sem precedente
   desse idioma no projeto.
3. Quem determina o usuário avaliado é `data.uid` do payload
   (`saveUserEvaluation`, linha 1375-1379:
   `$user = $app->repo('User')->find($data['uid'])`), não a sessão de quem
   está autenticado — ou seja, requisições de sessões diferentes ainda podem
   colidir na mesma linha se enviarem o mesmo `uid`.

## Achado importante: lock de sessão do PHP mascara o teste ingênuo

Tentativa inicial de reprodução: 3 requisições em paralelo (`Promise.all`) do
console do navegador, todas na mesma aba/sessão, com um `usleep(500000)`
temporário inserido em `getUserEvaluation()` para alargar a janela de corrida.
**Resultado: nenhuma duplicata** — apenas 1 linha criada e depois atualizada
(create/update sequencial).

Motivo: o app usa o handler nativo `redis` de sessão do PHP
(`bootstrap-common.php:50`, `ini_set('session.save_handler', 'redis')`) e
`session_start()` é chamado em todo request (`App.php:208`), mas
**`session_write_close()` nunca é chamado em lugar nenhum do código**. O
handler `redis` faz *locking* de sessão por padrão: cada request com o mesmo
`PHPSESSID` fica bloqueado em `session_start()` até o request anterior
terminar completamente. Como as 3 requisições do teste vinham da mesma aba
(mesma sessão), elas nunca rodaram de fato em paralelo — o `usleep` dentro do
código não teve efeito porque a 2ª requisição sequer tinha começado a
executar.

Em produção, esse lock não é infinito: o phpredis tenta adquirir o lock por
um tempo limitado e, se não conseguir, segue sem ele. Sob latência real, a
1ª requisição pode ficar retida além desse limite, liberando a 2ª antes da
1ª terminar — reabrindo a janela de corrida descrita acima. É provável que a
combinação **latência real + timeout do lock de sessão** seja o gatilho
verdadeiro em produção, não apenas a latência banco↔app isoladamente.

Correção do plano de teste (ainda não validada): disparar as requisições a
partir de **sessões diferentes** (abas anônimas/perfis diferentes), mantendo
o mesmo `data.uid` no payload, para não sofrer o lock de sessão. **Isso
também não reproduziu localmente** — mesmo com sessões distintas e o
`usleep`, os containers Docker (app e banco na mesma rede local, latência
sub-milissegundo) processam rápido demais para que a corrida se manifeste de
forma confiável. Daí a decisão de testar contra um banco em servidor externo,
que introduz latência de rede real.

## Dados de teste usados até agora

- `registration id`: `222835467`
- `uid` (avaliador forjado): `2147483647` (usuário inexistente, só serve para
  isolar o teste)
- Rota: `POST http://localhost:8088/salva-avaliacao/222835467/status:evaluated/`
- Script de disparo (console do navegador, rodar em cada aba/sessão com o
  mesmo `uid`):

```js
{
    const url = MapasCulturais.createUrl(
        'registration',
        'saveEvaluation',
        { 0: MapasCulturais.request.id, status: 'evaluated' }
    );

    const evaluationData = {
        "c-1535979733308": "3", "c-1535979786557": "3", "c-1535979874188": "3",
        "c-1535980045397": "3", "c-1535980067132": "3", "c-1535980085572": "3",
        "c-1535980125541": "3", "c-1535980156605": "3", "c-1535980184557": "3",
        "c-1535980218180": "3", "obs": "sdfgsdfg"
    };

    const sendRequest = (index) => {
        const data = {};
        $('#registration-evaluation-form form').serializeArray().forEach(field => {
            const match = field.name.match(/^data\[(.+?)\]$/);
            data[match ? match[1] : field.name] = field.value;
        });
        Object.assign(data, evaluationData);
        data.uid = '2147483647'; // mesmo uid em todas as abas/sessões

        console.log(`Disparando requisição #${index}...`);
        return $.post(url, { data });
    };

    Promise.all([sendRequest(1), sendRequest(2), sendRequest(3)])
        .then(r => console.log('Concluídas:', r))
        .catch(e => console.error('Falhou:', e));
}
```

## Instrumentação temporária usada (lembrar de remover)

Em `Entities/Registration.php:1345`, dentro de `getUserEvaluation()`, logo
após o `findOneBy` e antes do `return`:

```php
$evaluation = App::i()->repo('RegistrationEvaluation')->findOneBy([
    'registration' => $this,
    'user' => $user
]);
usleep(500000); // 500ms — SOMENTE PARA TESTE LOCAL, remover depois
return $evaluation;
```

**Não commitar esse `usleep`.** Serve só para alargar a janela de corrida
durante o teste manual.

## Como continuar assim que o banco externo estiver disponível

1. Apontar a aplicação local (`compose/local/config.php` /
   `dev-scripts/docker-compose.local.yml`, serviço `db`) para o host/porta do
   banco externo fornecido pelo DevOps, mantendo o restante do ambiente local
   (app em Docker) como está.
2. Reaplicar o `usleep(500000)` em `getUserEvaluation()` (Passo acima) — com
   latência real de rede para o banco, pode nem ser mais necessário, mas
   ajuda a tornar a corrida determinística nas primeiras tentativas.
3. Repetir o teste com **sessões diferentes** (abas anônimas/perfis
   diferentes), mesmo `uid`, script do console acima disparado quase ao mesmo
   tempo em cada aba.
4. Verificar duplicidade:
   ```sql
   SELECT registration_id, user_id, COUNT(*)
   FROM registration_evaluation
   GROUP BY registration_id, user_id
   HAVING COUNT(*) > 1;
   ```
5. Se reproduzir: confirmado que latência de rede real é o fator que faltava
   localmente. Remover o `usleep` e validar se a duplicata ainda ocorre só
   com a latência do banco externo (sem instrumentação), para saber o quão
   fiel isso é ao cenário de produção.
6. Se **não** reproduzir mesmo com banco externo: repensar a hipótese —
   pode envolver múltiplos workers PHP-FPM, comportamento específico do
   load balancer de produção, ou concorrência real de dois avaliadores
   diferentes (não coberta neste script de teste).

## Diagrama de sequência do diagnóstico

Arquivo PlantUML completo (pode ser colado em https://plantuml.com/):
`docs/diagrams/duplicate_record_evaluation.puml` (ver seção abaixo — cria-lo
se ainda não existir a partir do conteúdo já gerado na conversa).

Resumo visual do fluxo: duas requisições (sessões distintas) chegam quase ao
mesmo tempo → cada uma faz `session_start()` com lock próprio (sem contenção
entre si) → ambas executam `getUserEvaluation()` e recebem `null` porque
nenhuma commitou ainda → ambas criam e salvam uma nova
`RegistrationEvaluation` → resultado: 2 linhas para o mesmo
`(registration_id, user_id)`.

## Correção recomendada (ainda não implementada — fora do escopo desta rodada)

Preferência já definida para quando for corrigir: **unique constraint
`(registration_id, user_id)` no Postgres + tratamento de
`UniqueConstraintViolationException` em `saveUserEvaluation`** (fallback
para re-SELECT + UPDATE), junto com uma migration de deduplicação das linhas
já duplicadas existentes em produção antes de criar o índice único.
