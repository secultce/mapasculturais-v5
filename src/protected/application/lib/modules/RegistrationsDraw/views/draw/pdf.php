<?php
use MapasCulturais\App;

$this->layout = 'nolayout-pdf';
$draw = $app->view->regObject['draw'];
$opp = $app->view->regObject['opp'];
$titleNull = '';
?>
<div style="text-align: center; border-bottom: 1px solid #c3c3c3;">    
    <img src="<?= THEMES_PATH . '/Ceara/assets/img/logo-org-ceara.png'; ?>" width="40%">
    <h3>Secreatria da Cultura do Estado do Ceará (Secult/CE)</h3>
    <h3>Lista de sorteados da oportunidade</h3>
    <p><?= $opp->name; ?></p>
</div>
<div style="border-bottom: 1px solid #c3c3c3;">
   <?php 
    is_null($draw) ? $titleNull = 'Ainda sem sorteio' : $titleNull = 'Total de Sorteados: ' . count($draw);
    echo $titleNull;
    ?>
</div>