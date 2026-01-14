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
                            <button type="button" class="btn-counter-arguments" data-text="<?= htmlspecialchars($counterArgument->text, ENT_QUOTES, 'UTF-8') ?>" btn-view-counter-argument>
                                <i class='fas fa-eye'></i>
                            </button>
                            <?php if ($counterArgument->getFiles('counter-argument-attachment')) : ?>
                                <div>
                                     <p class="file-row">
                                        <?php foreach ($counterArgument->getFiles('counter-argument-attachment') as $file) : ?>
                                            <div>
                                                <a href="<?= $file->url ?>"  class="truncate-file" title="<?= $file->name ?>"><?= $file->name ?></a>
                                            </div>
                                        <?php endforeach; ?>
                                    </p>
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
                                class="btn-counter-arguments"
                                btn-view-counter-argument-response
                            >               
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
