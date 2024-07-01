<?php
// use MapasCulturais\App;
use MapasCulturais\i;

$this->layout = 'nolayout-pdf';
$draw = $app->view->regObject['draw'];
// dump($draw); die;
$opp = $app->view->regObject['opp'];
$titleNull = '';
?>
<style>
    .wdtd-table {
        width: 55%;
    }
</style>
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
<div style="border-bottom: 1px solid #c3c3c3;">
  <table>
    <thead>
        <tr style="width: 100%;">
            <td class="wdtd-table"><?= i::_e('Ranking'); ?></td>
            <td class="wdtd-table"><?= i::_e('Inscrição'); ?></td>
            <td class="wdtd-table"><?= i::_e('Categoria'); ?></td>
            <td class="wdtd-table"><?= i::_e('Responsável'); ?></td>
        </tr>
    </thead>
    <tbody>
        <?php
            foreach ($draw as $key => $value) { ?>
            <tr>
                <td><?= '#'.$value->rank; ?></td>
                <td><?= $value->registration->number; ?></td>
                <td><?= $value->category; ?></td>
                <td><?= $value->registration->owner->nome; ?></td>
            </tr>
            <?php } ?>
    </tbody>
  </table>
</div>