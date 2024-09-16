<?php

use MapasCulturais\i;

$this->layout = 'panel';

?>

<div class="panel-list panel-main-content">
    <header class="panel-header clearfix">
        <h2><?php \MapasCulturais\i::_e("Prestações de Contas"); ?></h2>
    </header>

    <ul class="abas clearfix clear">
        <?php $this->part('tab', ['id' => 'emProcesso', 'label' => i::__("Em processo"), 'active' => true]) ?>
        <?php $this->part('tab', ['id' => 'finalizadas', 'label' => i::__("Finalizadas")]) ?>
    </ul>
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
</div>
