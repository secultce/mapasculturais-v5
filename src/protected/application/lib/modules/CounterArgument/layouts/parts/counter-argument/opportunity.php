<?php

use MapasCulturais\Entities\CounterArgument;

?>

<div class="aba-content" id="contrarrazao">
    <p style="margin-bottom: 30px;">Nesta seção são listadas todas as contrarrazões enviadas pelos agentes para esta oportunidade.</p>

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
                            <button type="button" data-text="<?= $counterArgument->text ?>" btn-view-counter-argument>
                                <i class='fas fa-eye'></i>
                            </button>
                            <?php if ($counterArgument->getFiles('counter-argument-attachment')) : ?>
                                <div>
                                    <?php foreach ($counterArgument->getFiles('counter-argument-attachment') as $file) : ?>
                                        <div>
                                            <a href="<?= $file->url ?>"><?= $file->name ?></a>
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
                                data-text="<?= $counterArgument->response->text ?? '' ?>"
                                data-status="<?= $counterArgument->status ?>"
                                btn-view-counter-argument-response>
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
