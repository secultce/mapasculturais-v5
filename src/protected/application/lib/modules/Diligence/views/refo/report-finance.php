<?php

use Diligence\Repositories\Diligence as RepoDiligence;
use Carbon\Carbon;
//Dados da diligencia e da inscrição
$dili = $app->view->regObject['diligence'];
$reg = $app->view->regObject['registration'];

$repoDiligence = new RepoDiligence();
$repoDiligence->verifyAcessReport($reg);

$this->layout = 'nolayout-pdf';
require THEMES_PATH. 'BaseV1/layouts/headpdf.php';
?>

<section class="clearfix">
    <div style="margin-top: 25px">
        <p style="text-align: center">
            <img src="<?= MODULES_PATH . 'Diligence/assets/img/logo_secult.jpg' ?>" width="128" alt="">
        </p>
    </div>
    <div>
        <p class="title-bold" style="text-align: center">
            <label class="title-bold">
                RELATÓRIO FISCAL PARA O FINANCEIRO
            </label>
        </p>
    </div>
    <div class="row">
        <div class="container">
            <div class="col-md-12" class="table-info-ins">
                <div class="col-md-6" style="width: 100%; float: left;">
                    <label class="title-ins-label">
                        <strong>Número da Inscrição:</strong>
                    </label>
                    <label class="title-ins-sublabel">
                        <?= $reg->number; ?>
                    </label>
                    <br>
                    <label class="title-ins-label">
                        <strong>Nome do agente:</strong>
                    </label>
                    <label class="title-ins-sublabel">
                        <?= $reg->owner->name; ?>
                    </label>
                    <hr>
                </div>
                <div class="col-md-6  title-ins-sublabel-right" style="width: 50%;float: left;">
                    <label class="title-ins-sublabel">

                    </label> <br>
                </div>
            </div>
        </div>
    </div>
    <table width="100%" style="height: 100px; margin-top: 16px">
        <thead>
            <tr class="">
                <td style="width: 10%;">
                    <?php if (!empty($reg->opportunity->files['avatar'])) : ?>
                        <img src="<?php echo $reg->opportunity->files['avatar']->path; ?>" style="width: 80px; height: 80px; border: 1px solid #c5c5c5; margin-right: 8px">
                    <?php else : ?>
                        <img src="<?php echo THEMES_PATH . 'BaseV1/assets/img/avatar--opportunity.png'; ?>" style="width: 80px; height: 80px;margin:8px;">
                    <?php endif; ?>
                </td>
                <td style="width: 90%;">
                    <div>
                        <div class="multi-title-edital">
                            <label class="">Edital</label><br>
                        </div>
                        <div class="multi-sub-title-edital">
                            <label class=""><?php echo $reg->opportunity->ownerEntity->name; ?></label>
                        </div>
                    </div>
                    <div>
                        <div class="multi-title-edital">
                            <label for="" class="title-edital">Oportunidade</label><br>
                        </div>
                        <div class="multi-sub-title-edital">
                            <label class="sub-title-edital"><?php echo $reg->opportunity->name; ?></label>
                        </div>
                    </div>
                </td>
            </tr>
        </thead>
    </table>
</section>
<section>
    <div style="width: 100%">
        <h3>Histórico da prestação de contas</h3>
        <?php
        $br = "<br/>";
        foreach ($dili as $diligence) {
            if (!is_null($diligence)) {
                if (!is_null($diligence->description)) {
                    $subjects = "Não informado";
                    if (isset($diligence->subject)) {
                        $subjects = $diligence->getSubject();
                    }

                    $dateDiligence = Carbon::parse($diligence->createTimestamp)->format('d/m/Y H:i');

                    echo '<div class="multi-report-diligence">'
                        . "<b>Diligência:</b>" . $br
                        . "Assunto : " . $subjects . $br
                        . $diligence->description . "<br/>"
                        . '<small style="margin-top: 8px">' . $dateDiligence . '</small>'
                        . '</div>';
                } else {
                    echo '<div class="multi-report-answer">'
                        . "<b>Resposta</b>" . $br
                        . $diligence->answer . "<br/>"
                        . '</div>';
                }
                echo $br;
            } else {
                echo '<div class="multi-report-answer">'
                    . "<b>Resposta</b>" . $br
                    . "Não enviada.<br/>"
                    . '</div>';
                echo $br;
            }
        }
        ?>
    </div>
</section>
