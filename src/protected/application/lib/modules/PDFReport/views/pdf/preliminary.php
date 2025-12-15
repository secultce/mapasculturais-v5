<?php

$this->layout = 'nolayout-pdf';
$sub = $app->view->jsObject['subscribers'];
$nameOpportunity = $sub[0]->opportunity->name;
$opportunity = $app->view->jsObject['opp'];
$appealEnabled = $app->view->jsObject['appealEnabled'];

include_once('header-pdf.php');

?>

<main>
    <div class="container">
        <div class="pre-text">Resultado Preliminar</div>
        <div class="opportunity-info">
            <p class="text-opp">Oportunidade</p>
            <h4 class="opp-name-relatorio"><?php echo $nameOpportunity ?></h4>
        </div>
    </div>
    <?php
    $type = $opportunity->evaluationMethodConfiguration->type->id;
    if ($opportunity->registrationCategories == "" &&  ($type == 'technical' || $type == 'technicalna' || $type == 'homolog')) {
        include_once('technical-no-category.php');
    } elseif ($opportunity->registrationCategories == "" &&  $type == 'simple' || $type == 'documentary' || $type == 'homolog') {
        include_once('simple-documentary-no-category.php');
    } elseif ($opportunity->registrationCategories !== "" &&  ($type == 'technical' || $type == 'technicalna' || $type == 'homolog')) {
        include_once('technical-category.php');
    } elseif ($opportunity->registrationCategories !== "" &&  $type == 'simple' || $type == 'documentary' || $type == 'homolog') {
        include_once('simple-documentary-category.php');
    }
    ?>
</main>
