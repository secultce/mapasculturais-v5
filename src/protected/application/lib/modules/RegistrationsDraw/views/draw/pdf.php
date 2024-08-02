<?php

use Carbon\Carbon;
use MapasCulturais\i;

/**
 * @var \MapasCulturais\App $app
 */

$this->layout = 'nolayout-pdf';
$draw = $app->view->regObject['draw'];
$opp = $app->view->regObject['opp'];
$titleNull = empty($draw) ? 'Ainda sem sorteio' : 'Total de Sorteados: ' . count($draw);

?>
<style>
    .wdtd-table {
        text-align: center;
    }

    .title-thead {
        background-color: green;
        color: #fff;
        font-weight: bold;
    }

    table,
    th,
    td {
        border: 1px solid black;
        border-collapse: collapse;
        font-size: 16px;
    }

    .font-td {
        padding: 5px;
        font-size: 12px;
    }

    .back-category {
        background-color: #c7c7c7;
    }
</style>

<div style="text-align: center; border-bottom: 1px solid #c3c3c3;">
    <img src="<?= THEMES_PATH . '/Ceara/assets/img/logo-org-ceara.png'; ?>" width="40%" alt="">
    <h3>Secreatria da Cultura do Estado do Ceará (Secult/CE)</h3>
    <h3>Lista de sorteados da oportunidade</h3>
    <p><?= $opp->name; ?></p>
</div>
<div style="border-bottom: 1px solid #c3c3c3; margin-bottom: 10px">
    <table style="width: 100%;">
        <thead>
            <tr>
                <td class="font-td">
                    <?= $titleNull; ?>
                </td>
                <td style="padding: 5px; font-size: 12px; text-align: right;">
                    Data do sorteio:
                    <?php
                    if (isset($draw[0])) {
                        echo Carbon::parse($draw[0]->createTimestamp)->format('d/m/Y');
                    }
                    ?>
                </td>
            </tr>
        </thead>
    </table>
</div>
<div style="border-bottom: 1px solid #c3c3c3;">
    <table style="width: 100%;">
        <?php
        // Organizar registros por categoria
        $registrationsByCategory = array();

        foreach ($draw as $registration) {
            $registrationsByCategory[$registration->category][] = $registration;
        }
        foreach ($registrationsByCategory as $category => $registrations) : ?>
            <thead>
                <?php if ($category !== "") : ?>
                    <tr>
                        <td class="wdtd-table font-td back-category"><?php i::_e('Categoria'); ?></td>
                        <td class="wdtd-table font-td back-category" colspan="3">
                            <strong><?= $category; ?></strong>
                        </td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td class="wdtd-table title-thead font-td" style=""><?php i::_e('Ranking'); ?></td>
                    <td class="wdtd-table title-thead font-td"><?php i::_e('Inscrição'); ?></td>
                    <td class="wdtd-table title-thead font-td"><?php i::_e('Responsável'); ?></td>
                    <td class="wdtd-table title-thead font-td" style=""><?php i::_e('Sorteado'); ?></td>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($draw as $rank) :
                    if ($rank->category == $category) : ?>
                        <tr>
                            <td class="font-td"><?= '#' . $rank->rank; ?></td>
                            <td class="font-td"><?= $rank->registration->number; ?></td>
                            <td class="font-td"><?= $rank->registration->owner->name; ?></td>
                            <td class="font-td">
                                <?= Carbon::parse($draw[0]->createTimestamp)->format('d/m/Y H:i') ?>
                            </td>
                        </tr>
                    <?php endif;
                endforeach; ?>
            </tbody>
        <?php endforeach; ?>
    </table>
</div>