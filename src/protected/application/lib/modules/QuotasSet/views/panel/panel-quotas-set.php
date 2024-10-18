<?php

use MapasCulturais\App;

$this->layout = 'panel';
?>

<?php /** @var App $app */
$app->hook('template(panel.cotas-e-politicas.settings-nav):before', function () use ($app) { ?>
    <div class="panel-list panel-main-content">
        <header>
            <h2>Políticas de ações afirmativas - cotas</h2>
        </header>
        <ul class="abas clearfix clear">
            <?php $this->applyTemplateHook('tab-nav', 'begin'); ?>
            <li class="active"><a href="#atribuir" rel="noopener noreferrer" id="tab-atribuir">Atribuição de cotas</a></li>
<!--            <li class=""><a href="#configurar" rel="noopener noreferrer" id="tab-configurar">Configuração de cotas</a></li>-->
            <?php $this->applyTemplateHook('tab-nav', 'end'); ?>
        </ul>

        <div id="tab-content">
            <?php $this->applyTemplateHook('tab-content', 'begin'); ?>
            <?php $this->part('quotas-set-assign.tab.content'); ?>
            <?php $this->applyTemplateHook('tab-content', 'end'); ?>
        </div>
    </div>
<?php }); ?>
