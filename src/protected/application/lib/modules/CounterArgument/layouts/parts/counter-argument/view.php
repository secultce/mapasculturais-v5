<?php

use MapasCulturais\Entities\CounterArgument;
use MapasCulturais\Services\CounterArgumentService;

$counterArgumentService = new CounterArgumentService();

?>

<div class="panel-list panel-main-content">
    <h4>Minhas Contrarrazões</h4>
    <p class="info-text">Aqui você pode visualizar todas as contrarrazões que você enviou.</p>

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
                    <?php
                    $isCounterArgumentPeriod = $counterArgumentService->isCounterArgumentPeriod($counterArgument->registration->opportunity)
                    ?>
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
                            <button type="button" class="counter-argument-btn" data-text="<?= htmlspecialchars($counterArgument->text, ENT_QUOTES, 'UTF-8') ?>" btn-view-counter-argument>
                                <i class='fas fa-eye'></i>
                            </button>
                            <?php if ($counterArgument->getFiles('counter-argument-attachment')) : ?>
                                <div class="counter-argument-file-wrapper">
                                    <?php foreach ($counterArgument->getFiles('counter-argument-attachment') as $file) : ?>
                                        <div class="counter-argument-file">
                                            <span><i class="fas fa-paperclip"></i></span>
                                            <a href="<?= $file->url ?>" title="<?= $file->name ?>"><?= $file->name ?></a>
                                            <?php if ($isCounterArgumentPeriod) : ?>
                                                <span class="icon-remove-counter-argument-file" remove-counter-argument-file data-file-id="<?= $file->id ?>" title="Remover arquivo">
                                                    <i class="fas fa-trash-alt"></i>
                                                </span>
                                            <?php endif; ?>
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
                            <button type="button" class="counter-argument-btn" data-text="<?= $counterArgument->response ?>" btn-view-counter-argument-response>
                                <i class='fas fa-eye'></i>
                            </button>
                        </td>
                        <td>
                            <button
                                type="button"
                                class="counter-argument-btn"
                                data-id="<?= $counterArgument->id ?>"
                                data-text="<?= htmlspecialchars($counterArgument->text, ENT_QUOTES, 'UTF-8') ?>"
                                edit-counter-argument-btn
                                <?= $isCounterArgumentPeriod ? '' : 'disabled' ?>
                                title="<?= $isCounterArgumentPeriod ? 'Editar Contrarrazão' : 'O período para edição da contrarrazão está encerrado' ?>">
                                <i class='fas fa-edit'></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <div class="alert info">Você ainda não enviou nenhuma contrarrazão.</div>
    <?php endif; ?>
</div>
