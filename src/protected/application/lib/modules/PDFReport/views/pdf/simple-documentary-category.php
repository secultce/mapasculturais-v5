<?php

use MapasCulturais\Entities\Registration;

$this->layout = 'nolayout-pdf';
$sub = $app->view->jsObject['subscribers'];
$nameOpportunity = $sub[0]->opportunity->name;
$opp = $app->view->jsObject['opp'];

?>

<div class="container">
    <?php foreach ($opp->registrationCategories as $keyCat => $nameCat) : ?>
        <div class="table-info-cat">
            <?php echo $nameCat; ?>
        </div>
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
                foreach ($sub as $key => $nameSub) {
                    if ($nameCat == $nameSub->category) {
                        $arrayCheck[] = $nameSub->category;
                ?>
                        <tr>
                            <td class="text-left"><?php echo $nameSub->number; ?></td>
                            <td class="text-left"><?php echo mb_strtoupper($nameSub->owner->name); ?></td>
                            <td class="text-center"><?php echo Registration::getStatusNameById($nameSub->status); ?> </td>
                        </tr>
                    <?php
                    }
                }
                if (!in_array($nameCat, $arrayCheck)) : ?>
                    <tr>
                        <td class="text-left"></td>
                        <td class="text-left">Não há candidatos selecionados</td>
                        <td class="text-center"></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endforeach; ?>
</div>
