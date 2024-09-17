<?php

use MapasCulturais\i;

$this->layout = 'panel';

?>

<div class="panel-list panel-main-content">
    <header class="panel-header clearfix">
        <h2><?php \MapasCulturais\i::_e("Prestações de Contas"); ?></h2>
    </header>

    <ul class="abas clearfix clear">
        <?php if (!$isFiscal) : ?>
            <?php $this->part('tab', ['id' => 'emProcesso', 'label' => i::__("Em processo"), 'active' => true]) ?>
            <?php $this->part('tab', ['id' => 'finalizadas', 'label' => i::__("Finalizadas")]) ?>
        <?php endif; ?>
        <?php if ($isFiscal) : ?>
            <?php $this->part('tab', ['id' => 'emMonitoramento', 'label' => i::__("Em monitoramento"), 'active' => true]) ?>
            <?php $this->part('tab', ['id' => 'monitoramentoFinalizado', 'label' => i::__("Monitoramento finalizado")]) ?>
        <?php endif; ?>
    </ul>
    <?php if (!$isFiscal) : ?>
        <div id="emProcesso">
            <?php
            if ($regAsPropInProcess) {
                foreach ($regAsPropInProcess as $reg) {
                    $this->part('panel-accountability', array('registration' => $reg));
                }
            } else {
            ?>
                <div class="alert info"><?php \MapasCulturais\i::_e("Você não possui nenhuma prestação de contas em processo."); ?></div>
            <?php } ?>
        </div>
        <div id="finalizadas">
            <?php
            if ($regAsPropFinished) {
                foreach ($regAsPropFinished as $reg) {
                    $this->part('panel-accountability', array('registration' => $reg));
                }
            } else {
            ?>
                <div class="alert info"><?php \MapasCulturais\i::_e("Você não possui nenhuma prestação de contas finalizada."); ?></div>
            <?php } ?>
        </div>
    <?php endif; ?>
    <?php if ($isFiscal) : ?>
        <div id="emMonitoramento">
            <?php
            if ($oppInMonitoringProcess) {
                foreach ($oppInMonitoringProcess as $opp) {
                    $this->part('panel-accountability-fiscal', array('opportunity' => $opp));
                }
            } else {
            ?>
                <div class="alert info"><?php \MapasCulturais\i::_e("Você não possui nenhuma prestação de contas para realizar monitoramento."); ?></div>
            <?php } ?>
        </div>
        <div id="monitoramentoFinalizado">
            <?php
            if ($oppMonitoringFinished) {
                foreach ($oppMonitoringFinished as $opp) {
                    $this->part('panel-accountability-fiscal', array('opportunity' => $opp));
                }
            } else {
            ?>
                <div class="alert info"><?php \MapasCulturais\i::_e("Você não possui nenhuma oportunidade com processo de monitoramento finalizado."); ?></div>
            <?php } ?>
        </div>
    <?php endif; ?>
</div>
