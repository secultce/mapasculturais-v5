<?php

use MapasCulturais\Entities\CounterArgument;

$opportunity = $this->controller->requestedEntity;
$unpublishedResponses = [];

?>

<div class="aba-content" id="contrarrazao">
    <p class="info-text">Nesta seção são listadas todas as contrarrazões enviadas pelos agentes para esta oportunidade.</p>

    <?php if ($counterArguments) : ?>
        <table class="table table-striped table-hover" id="counter-reason-admin-table">
            <thead >
                <tr class="counter-reason-table-header">
                    <th>Inscrição</th>
                    <th>Agente</th>
                    <th>Contrarrazão</th>
                    <th>Situação</th>
                    <th>Data do envio</th>
                    <th>Resposta</th>
                    <th>Dados da Resposta</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($counterArguments as $counterArgument) : ?>
                    <?php
                    $response = $counterArgument->response;
                    if (!$response || !$response->published) {
                        $unpublishedResponses[] = $response;
                    }
                    ?>
                    <tr>
                        <td>
                            <a href="<?= $app->createUrl('inscricao', $counterArgument->registration->id) ?>">
                                <?= $counterArgument->registration->number ?>
                            </a>
                        </td>
                        <td>
                            <a href="<?= $app->createUrl('agente', $counterArgument->registration->owner->id) ?>">
                                <?= $counterArgument->registration->owner->name ?>
                            </a>
                        </td>
                        <td>
                            <button type="button" class="counter-argument-btn" data-text="<?= htmlspecialchars($counterArgument->text, ENT_QUOTES, 'UTF-8') ?>" btn-view-counter-argument>
                                <i class='fas fa-eye'></i>
                            </button>
                            <?php if ($counterArgument->getFiles('counter-argument-attachment')) : ?>
                                <div class="counter-argument-file-wrapper">
                                    <?php foreach ($counterArgument->getFiles('counter-argument-attachment') as $file) : ?>
                                        <div class="counter-argument-file">
                                            <span><i class="fas fa-paperclip"></i></span>
                                            <a href="<?= $file->url ?>" title="<?= $file->name ?>"><?= $file->name ?></a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= CounterArgument::STATUSES[$counterArgument->status] ?>
                        </td>
                        <td>
                            <?= ($counterArgument->updateTimestamp ?? $counterArgument->createTimestamp)->format('d/m/Y H:i') ?>
                        </td>
                        <td>
                            <div>
                                <?php if ($response && (!$response->owner->canUser('@control') || $response->published)) : ?>
                                    <button
                                        type="button"
                                        data-text="<?= htmlspecialchars($response->text ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                        class="counter-argument-btn"
                                        btn-view-counter-argument-response
                                        title="Visualizar resposta">
                                        <i class='fas fa-eye'></i>
                                    </button>
                                <?php elseif (($response && $response->owner->canUser('@control')) || !$response) : ?>
                                    <button
                                        type="button"
                                        data-id="<?= $counterArgument->id ?>"
                                        data-text="<?= htmlspecialchars($response->text ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                        data-status="<?= $counterArgument->status ?>"
                                        class="counter-argument-btn"
                                        btn-counter-argument-response
                                        <?= $isResponsePeriod ? '' : 'disabled' ?>
                                        title="<?= $isResponsePeriod ? 'Responder Contrarrazão' : 'Fora do período de resposta. Aguarde o fim do período de envio das contrarrazões.' ?>">
                                        <i class='fas fa-edit'></i>
                                    </button>
                                <?php endif; ?>
                                <?php if ($response) : ?>
                                    <div class="counter-argument-response-info">
                                        <div>
                                            <small><?= $response->owner->name ?></small>
                                        </div>
                                        <div>
                                            <small><?= ($response->updateTimestamp ?? $response->createTimestamp)->format('d/m/Y H:i') ?></small>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <?php if ($response) : ?>
                                <?= $response->owner->name ?> - <?= ($response->updateTimestamp ?? $response->createTimestamp)->format('d/m/Y H:i') ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($opportunity->canUser('@control')) : ?>
            <button
                type="button"
                data-opportunity-id="<?= $opportunity->id ?>"
                id="btn-publish-responses-counter-arguments"
                <?= $unpublishedResponses ? '' : 'disabled' ?>
                title="<?= $unpublishedResponses ? 'Publicar respostas' : 'Todas as respostas estão publicadas' ?>"
                class="btn btn-publish-responses-counter-arguments">
                <i class="fas fa-paper-plane"></i> Publicar respostas
            </button>
        <?php endif; ?>
    <?php else : ?>
        <div class="alert info">Ainda não foram enviadas contrarrazões nesta oportunidade.</div>
    <?php endif; ?>
</div>
