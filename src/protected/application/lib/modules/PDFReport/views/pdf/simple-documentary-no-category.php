<?php

use MapasCulturais\Entities\Registration;

$this->layout = 'nolayout-pdf';
$sub = $app->view->jsObject['subscribers'];
$nameOpportunity = $sub[0]->opportunity->name;
$opp = $app->view->jsObject['opp'];

?>

<div class="container">
    <table id="table-preliminar" width="100%">
        <thead>
            <tr>
                <th class="text-left" width="30%">Inscrição</th>
                <th class="text-left" width="50%">Candidatos</th>
                <th class="text-center" width="20%">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $isExist = false;
            $arrayCheck = [];
            foreach ($sub as $key => $nameSub) :
                $arrayCheck[] = $nameSub->category; ?>
                <tr>
                    <td class="text-left"><?php echo $nameSub->number; ?></td>
                    <td class="text-left"><?php echo mb_strtoupper($nameSub->owner->name); ?></td>
                    <td class="text-center"><?php echo Registration::getStatusNameById($nameSub->status); ?> </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
