<?php

use MapasCulturais\Entities\CounterArgument;

?>

<div class="panel-list panel-main-content">
    <h4>Minhas Contrarrazões</h4>

    <?php if ($counterArguments) : ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Oportunidade</th>
                    <th>Inscrição/Agente</th>
                    <th>Contrarrazão</th>
                    <th>Situação</th>
                    <th>Data do envio</th>
                    <th>Resposta</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($counterArguments as $counterArgument) : ?>
                    <tr>
                        <td>
                            <a href="<?= $app->createUrl('oportunidade', $counterArgument->registration->opportunity->id) ?>">
                                <?= $counterArgument->registration->opportunity->name ?>
                            </a>
                        </td>
                        <td>
                            <a href="<?= $app->createUrl('inscricao', $counterArgument->registration->id) ?>">
                                <?= $counterArgument->registration->number ?>
                            </a>
                            <br>
                            <a href="<?= $app->createUrl('agente', $counterArgument->registration->owner->id) ?>">
                                <?= $counterArgument->registration->owner->name ?>
                            </a>
                        </td>
                        <td>
                            <button type="button" data-text="<?= $counterArgument->text ?>" btn-view-counter-argument><i class='fas fa-eye'></i></button>
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
                        <td></td>
                        <td></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <div class="alert info">Você ainda não enviou nenhuma contrarrazão.</div>
    <?php endif; ?>
</div>
