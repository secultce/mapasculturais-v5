<?php

use MapasCulturais\App;
use Recourse\Utils\Util;
use CounterReason\Repositories\CounterReasonRepository;

$this->layout = 'panel';
$app = App::i();

?>
<div class="panel-list panel-main-content">
    <?php $this->applyTemplateHook('panel-header', 'before'); ?>
    <header class="panel-header clearfix">
        <h2>Minhas Contrarrazão</h2>
    </header>
    <div id="status-info" class="alert info">
        <div class="close" style="cursor: pointer;"></div>
        <?php
        if (!empty($app->getCookie("cr-empty"))) {
            echo $app->getCookie("cr-empty");
            $app->deleteCookie("cr-empty");
        } else {
            echo '<p>Durante o período da contrarrazão da oportunidade, você poderá editar a contrarrazão.<p>';
        }
        ?>

    </div>
    <div class="table-responsive">
        <table class="table table-bordered" id="tableAllRecourse" style="width:100%;">
            <thead>
                <tr>
                    <th>Oportunidade</th>
                    <th>Inscrição/Agente</th>
                    <th style="width:25%;">Recurso Solicitado</th>
                    <th>Enviado em</th>
                    <th style="width:25%;">Resposta</th>
                    <th>Ações</th>
                </tr>
            </thead>

            <tbody id="bodyAllCounterReasons">
                <?php
                if (isset($cr[0]) && $scope->getVerifyUser($cr[0]->agent)):
                    foreach ($cr as $crs): ?>
                        <tr>
                            <td>
                                <a href="<?= $app->createUrl('oportunidade', $crs->opportunity->id) ?>"
                                    title="<?= $crs->opportunity->name ?>">
                                    <?= \MapasCulturais\Utils::getPlainTextPreview($crs->opportunity->name, 50); ?>
                                </a>
                            </td>
                            <td>
                                <a href="<?= $app->createUrl('inscricao', $crs->registration->id) ?>">
                                    <?= $crs->registration->number; ?>
                                </a>
                            </td>
                            <td>
                                <?php
                                echo \MapasCulturais\Utils::getPlainTextPreview($crs->text, 100);
                                $rcText = htmlspecialchars(addslashes($crs->text), ENT_QUOTES);
                                if (strlen($rcText) > 100):
                                    echo "...<br/>
                                                <a href='#' onclick='getInfoCounterReason(\"{$rcText}\")'>Ler Recurso</a>";
                                endif;
                                ?>
                            </td>
                            <td><?= $crs->send->format('d/m/Y H:i');; ?></td>
                            <td><?= is_null($crs->reply) ? "Aguardando." : htmlspecialchars($crs->text ?? '', ENT_QUOTES); ?>
                            </td>
                            <td>
                                <?php if (CounterReasonRepository::validatePeriodCounterReason($crs->registration)): ?>
                                    <a class="btn btn-recourse btnOpen-counterReason" disabled style="color: #0a766a"
                                        title="Editar contrarrazão" data-entity-id-cr="<?= $crs->registration->id; ?>"
                                        href="javascript:void(0)"
                                        data-entity-context-cr="<?= htmlspecialchars($crs->text ?? '', ENT_QUOTES); ?>"
                                        data-reason-id="<?= $crs->id ?>">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                <?php else: ?>
                                    <a class="btn btn-recourse btnOpen-counterReason disabled a-houver-cr" disabled
                                        style="color: #0a766a" title="Contrarrazão expirada" href="javascript:void(0)">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

    </div>
</div>