<?php

use MapasCulturais\i;

return;
/**
 * @var \MapasCulturais\App $app
 * @var \MapasCulturais\Entities\Opportunity $opportunity
 * @var \MapasCulturais\Entities\Draw[] $draws
 */

$this->layout = 'nolayout-pdf';
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
<?php if (empty($draws)) { ?>
    <div style="text-align: center; border-bottom: 1px solid #c3c3c3;">
        <img src="<?= THEMES_PATH . '/Ceara/assets/img/logo-org-ceara.png'; ?>" width="40%" alt="">
        <h3>Secreatria da Cultura do Estado do Ceará (Secult/CE)</h3>
        <h3>Lista de sorteados da oportunidade</h3>
        <p><?= $opportunity->name; ?></p>
    </div>
    <table>
        <thead>
            <tr>
                <td class="font-td">
                    <?php i::_e('Ainda não há sorteios') ?>
                </td>
            </tr>
        </thead>
    </table>
    <?php return;
}

foreach ($draws as $draw) : ?>
    <div style="text-align: center; border-bottom: 1px solid #c3c3c3;">
        <img src="<?= THEMES_PATH . '/Ceara/assets/img/logo-org-ceara.png'; ?>" width="40%" alt="">
        <h3>Secreatria da Cultura do Estado do Ceará (Secult/CE)</h3>
        <h3>Lista de sorteados da oportunidade</h3>
        <p><?= $opportunity->name; ?></p>
    </div>
    <div style="border-bottom: 1px solid #c3c3c3; margin-bottom: 10px">
        <table style="width: 100%;">
            <thead>
                <?php if ($draw->category !== '') : ?>
                    <tr>
                        <td class="wdtd-table font-td back-category"><?php i::_e('Categoria'); ?></td>
                        <td class="wdtd-table font-td back-category" colspan="3">
                            <strong><?= $draw->category; ?></strong>
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
                foreach ($draw->drawRegistrations as $drawRegistration) : ?>
                        <tr>
                            <td class="font-td"><?= '#' . $drawRegistration->rank; ?></td>
                            <td class="font-td"><?= $drawRegistration->registration->number; ?></td>
                            <td class="font-td"><?= $drawRegistration->registration->owner->name; ?></td>
                            <td class="font-td">
                                <?= $draw->createTimestamp->format('d/m/Y \à\s H:i:s') ?>
                            </td>
                        </tr>
                <?php endforeach; ?>
            </tbody>
    </table>
</div>
<?php endforeach;
