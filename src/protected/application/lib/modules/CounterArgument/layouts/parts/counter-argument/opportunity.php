<?php

use MapasCulturais\Entities\CounterArgument;

?>

<div class="aba-content" id="contrarrazao">
    <p class="info-text">Nesta seção são listadas todas as contrarrazões enviadas pelos agentes para esta oportunidade.</p>

    <?php if ($counterArguments) : ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Inscrição</th>
                    <th>Agente</th>
                    <th>Contrarrazão</th>
                    <th>Situação</th>
                    <th>Data do envio</th>
                    <th>Resposta</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($counterArguments as $counterArgument) : ?>
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
                            <?= $counterArgument->createTimestamp->format('d/m/Y H:i') ?>
                        </td>
                        <td>
                            <button
                                type="button"
                                data-id="<?= $counterArgument->id ?>"
                                data-text="<?= htmlspecialchars($counterArgument->response->text ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                data-status="<?= $counterArgument->status ?>"
                                class="counter-argument-btn"
                                btn-view-counter-argument-response
                                <?= $isResponsePeriod ? '' : 'disabled' ?>
                                title="<?= $isResponsePeriod ? 'Responder Contrarrazão' : 'Fora do período de resposta. Aguarde o fim do período de envio das contrarrazões.' ?>">
                                <i class='fas fa-edit'></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <div class="alert info">Ainda não foram enviadas contrarrazões nesta oportunidade.</div>
    <?php endif; ?>
</div>
