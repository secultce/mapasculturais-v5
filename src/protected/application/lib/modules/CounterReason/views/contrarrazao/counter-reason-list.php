<?php

use Recourse\Utils\Util;

/**
 * @var \MapasCulturais\App $app
 * @var bool $isOwner
 * @var \Recourse\Entities\Recourse[] $allRecoursesUser
 */

$this->layout = 'panel';

?>
<div class="panel-list panel-main-content">
    <?php $this->applyTemplateHook('panel-header','before'); ?>
    <header class="panel-header clearfix">
        <h2>Minhas Contrarrazão</h2>
    </header>
    <div class="table-responsive">
        <table class="table table-bordered" id="tableAllRecourse" style="width:100%;">
            <thead>
                <tr>
                    <th>Oportunidade</th>
                    <th>Inscrição/Agente</th>
                    <th style="width:25%;">Recurso Solicitado</th>
                    <th>Enviado em</th>
                    <th>Situação</th>
                    <th style="width:25%;">Resposta</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="bodyAllCounterReasons">
            <?php foreach ($cr as $crs): ?>
                <tr>
                    <td>
                        <a
                            href="<?= $app->createUrl('oportunidade', $crs->opportunity->id ) ?>"
                            title="<?= $crs->opportunity->name ?>"
                        >
                            <?= \MapasCulturais\Utils::getPlainTextPreview($crs->opportunity->name, 50); ?>
                        </a>
                    </td>
                    <td>
                        <a href="<?= $app->createUrl('inscricao', $crs->registration->id ) ?>">
                            <?= $crs->registration->number; ?>
                        </a>
                    </td>
                    <td><?= \MapasCulturais\Utils::getPlainTextPreview($crs->text, 100); ?></td>
                    <td><?= $crs->send->format('d/m/Y H:i');; ?></td>
                    <td><?= $crs->status; ?></td>
                    <td>Aguardando</td>
                    <td>
                        <a
                            class="btn btn-recourse "
                            style="color: #0a766a"
                            title="Editar contrarrazão"
                            edit-recourse-btn
                            data-recourse-id="<?= $crs->id ?>"
                            data-recourse-text="<?= htmlspecialchars($crs->text, ENT_QUOTES, 'UTF-8') ?>"
                        >
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

    </div>
</div>
