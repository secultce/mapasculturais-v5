<?php

use PDFReport\Entities\Pdf;

$this->layout = 'nolayout-pdf';
$sub = $app->view->jsObject['subscribers'];
$nameOpportunity = $sub[0]->opportunity->name;
$opp = $app->view->jsObject['opp'];
$sections = $opp->evaluationMethodConfiguration->sections;
$criterios = $opp->evaluationMethodConfiguration->criteria;

if ($type == "technicalna") {
    $sub = Pdf::sortArrayForNAEvaluations($sub, $opp);
}

$inscritos = [];
foreach ($sub as $reg) {
    $noteSection = [];
    foreach ($sections as $sec) {
        if (!isset($sec->categories) || in_array($reg->category, $sec->categories)) {
            $noteSection[] = Pdf::getSectionNote($opp, $reg, $sec->id);
        }
    }

    $now  = new DateTime("now");
    $birth = new DateTime($reg->owner->dataDeNascimento);
    $idade = $now->diff($birth);

    if (isset($noteSection[0])) {
        $inscritos[] = [
            'number' => $reg->number,
            'name' => $reg->owner->name,
            'preliminaryResult' => $reg->preliminaryResult,
            'consolidatedResult' => $reg->consolidatedResult,
            'category' => $reg->category,
            'birth' => $reg->owner->dataDeNascimento,
            'age' => ($idade->y >= 60) ? true : false,
            'noteSection1' => (float) $noteSection[0],
            'noteAllSections' => $noteSection,
        ];
    } else {
        $inscritos[] = [
            'number' => $reg->number,
            'name' => $reg->owner->name,
            'preliminaryResult' => $reg->preliminaryResult,
            'consolidatedResult' => $reg->consolidatedResult,
            'category' => $reg->category,
            'birth' => $reg->owner->dataDeNascimento,
            'age' => ($idade->y >= 60) ? true : false,
            'noteAllSections' => $noteSection,
        ];
    }
}

if ($type == "technical") {
    usort($inscritos, function ($a, $b) {
        if (isset($a['noteSection1']) && isset($b['noteSection1'])) {
            return [$b['consolidatedResult'], $b['age'], $b['noteSection1'], $a['birth']] <=> [$a['consolidatedResult'], $a['age'], $a['noteSection1'], $b['birth']];
        } else {
            return [$b['consolidatedResult'], $b['age'], $a['birth']] <=> [$a['consolidatedResult'], $a['age'], $b['birth']];
        }
    });
}

?>

<div class="container">
    <?php foreach ($opp->registrationCategories as $key_first => $nameCat) : ?>
        <div class="table-info-cat">
            <span><?php echo $nameCat; ?></span>
        </div>
        <table id="table-preliminar" width="100%">
            <thead>
                <tr style="border: 1px solid #CFDCE5;">
                    <?php if (isset($preliminary)) : ?>
                        <th class="text-left" width="10%">Classificação</th>
                    <?php endif; ?>
                    <th class="text-left" style="margin-top: 5px;" width="22%">Inscrição</th>
                    <th class="text-left" width="68%">Candidatos</th>
                    <?php
                    if (isset($preliminary)) {
                        echo '<th class="text-center" width="10%">NF</th>';
                    } else {
                        $notaIndex = 1;
                        foreach ($sections as $key => $sec) {
                            if (!isset($sec->categories) || in_array($nameCat, $sec->categories)) { ?>
                                <th class="text-center" width="<?php echo count($sections) > 1 ? "5%" : "10%" ?>"><?php echo 'N' . ($notaIndex++) ?></th>
                    <?php
                            }
                        }
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                $countArray = [];
                $arrayCheck = [];

                foreach ($inscritos as $key => $ins) {
                    if ($nameCat == $ins['category']) {
                        $countArray[$nameCat][] = $key;
                        $arrayCheck[] = $ins['category'];
                ?>
                        <tr>
                            <?php if (isset($preliminary)) : ?>
                                <td class="text-center"><?php echo count($countArray[$nameCat]) ?> </td>
                            <?php endif; ?>
                            <td class="text-left"><?php echo $ins['number']; ?></td>
                            <td class="text-left"><?php echo mb_strtoupper($ins['name']); ?></td>
                            <?php if ($type == "technicalna" && !isset($preliminary)) : ?>
                                <td class="text-center"><?php echo $ins['preliminaryResult']; ?></td>
                            <?php elseif (isset($preliminary)) : ?>
                                <td class="text-center"><?php echo $ins['consolidatedResult']; ?></td>
                            <?php else : ?>
                                <?php foreach ($ins['noteAllSections'] as $noteSection) : ?>
                                    <td class="text-center"><?php echo $noteSection; ?></td>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tr>
                    <?php
                    }
                }

                if (!in_array($nameCat, $arrayCheck)) : ?>
                    <tr class="no-subs">
                        <td width="10%"></td>
                        <td class="text-left">Não há candidatos selecionados</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endforeach; ?>
</div>
