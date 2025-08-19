<?php

use MapasCulturais\Entities\Opportunity;
use PDFReport\Entities\Pdf as EntitiesPdf;

$reg = $app->view->regObject['ins'];
$allPhases = $app->view->regObject['allPhases'];

require THEMES_PATH . 'BaseV1/layouts/headpdf.php';

?>

<table width="100%" style="height: 100px; margin-bottom: 24px; margin-top: 24px; width: 100%;">
    <thead>
        <tr>
            <td>
                <?php if (!empty($reg->opportunity->getFile('img:header'))) : ?>
                    <div>
                        <img src="<?php echo $reg->opportunity->getFile('img:header')->path; ?>" alt="">
                    </div>
                <?php endif; ?>
            </td>
        </tr>
    </thead>
</table>

<main>
    <div class="row">
        <div class="container">
            <div class="col-md-12" class="table-info-ins">
                <div class="col-md-6" style="width: 40%; float: left;">
                    <label class="title-ins-label">Inscrição</label> <br>
                    <label class="title-ins-sublabel"><?php echo $reg->id; ?></label>
                </div>
                <div class="col-md-6  title-ins-sublabel-right" style="width: 50%;float: left;">
                    <label class="title-ins-sublabel">
                        <?php
                        echo !is_null($reg->sentTimestamp) ? "Registrada no dia: " . $reg->sentTimestamp->format('d/m/Y') : '';
                        ?>
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
                        <img src="<?php echo $reg->opportunity->files['avatar']->path; ?>"
                            style="width: 80px; height: 80px; border: 1px solid #c5c5c5; margin-right: 8px">
                    <?php else : ?>
                        <img src="<?php echo THEMES_PATH . 'BaseV1/assets/img/avatar--opportunity.png'; ?>"
                            style="width: 80px; height: 80px;margin:8px;">
                    <?php endif; ?>
                </td>
                <td style="width: 90%;">
                    <div>
                        <div class="title-edital">
                            <label class="">Edital</label><br>
                        </div>
                        <div class="sub-title-edital">
                            <label class=""><?php echo $reg->opportunity->ownerEntity->name; ?></label>
                        </div>
                    </div>
                    <div>
                        <div class="title-edital">
                            <label for="" class="title-edital">Oportunidade</label><br>
                        </div>
                        <div class="sub-title-edital">
                            <label class="sub-title-edital"><?php echo $reg->opportunity->name; ?></label>
                        </div>
                    </div>
                </td>
            </tr>
        </thead>
    </table>

    <table style="margin-top: 24px; margin-bottom: 24px">
        <thead>
            <tr>
                <td>
                    <p class="my-registration-email-confirm"></p>
                </td>
            </tr>
        </thead>
    </table>
    <div class="my-registration-category">
        <div class="my-conten-agent">
            <span class="my-registration-fields"> Categoria: </span>
            <span class="my-registration-fields-span">
                <?php echo ($reg->category !== "") ? $reg->category : 'Não informado'; ?></span><br>
        </div>
    </div>
    <div style="border: 1px solid #E8E8E8; border-radius: 8px; width:  100%; height: 400px; padding: 8px;">
        <h4 class="my-registration-fields">
            Agente responsável pela inscrição
        </h4>
        <br>
        <div id="my-info-registration">
            <?php if (!empty($reg->owner->avatar)): ?>
                <img src="<?php echo $reg->owner->avatar->transform('avatarSmall')->url ?>" alt="">
            <?php else: ?>
                <img src="<?php echo PLUGINS_PATH . 'PDFReport/assets/img/avatar--agent.png'; ?>" alt="" style="width: 35px; height: 35px;margin: 5px 5px;background: #CCCCCC;float:left;">
            <?php endif; ?>
            <div id="numer_registration">
                <label>
                    <?php echo $reg->number; ?>
                </label>
            </div>
        </div>
        <br>
        <div class="my-conten-agent">
            <span class="my-registration-fields"> Site: </span>
            <span
                class="my-registration-fields-span"><?php echo !empty($reg->owner->metadata['site']) ? $reg->owner->metadata['site'] : "Não informado"; ?>
            </span><br>
            <span class="my-registration-fields">Nome completo: </span>
            <span
                class="my-registration-fields-span"><?php echo $reg->owner->name ? $reg->owner->name : "Não informado"; ?>
            </span><br>
            <span class="my-registration-fields">Data de Nascimento/Fundação: </span>
            <span class="my-registration-fields-span">
                <?php echo !empty($reg->owner->metadata['dataDeNascimento']) ? date("d/m/Y", strtotime($reg->owner->metadata['dataDeNascimento'])) : "Não informado"; ?>
            </span><br>
            <span class="my-registration-fields">Gênero: </span>
            <span class="my-registration-fields-span">
                <?php echo !empty($reg->owner->metadata['genero']) ? $reg->owner->metadata['genero'] : "Não informado"; ?>
            </span><br>
            <span class="my-registration-fields">Orientação Sexual: </span>
            <span class="my-registration-fields-span">
                <?php echo !empty($reg->owner->metadata['orientacaoSexual']) ? $reg->owner->metadata['orientacaoSexual'] : "Não informado"; ?>
            </span><br>
            <span class="my-registration-fields">Raça/Cor: </span>
            <span class="my-registration-fields-span">
                <?php echo !empty($reg->owner->metadata['raca']) ? $reg->owner->metadata['raca'] : "Não informado"; ?>
            </span><br>
            <span class="my-registration-fields">Email Privado: </span>
            <span class="my-registration-fields-span">
                <?php echo !empty($reg->owner->metadata['emailPrivado']) ? $reg->owner->metadata['emailPrivado'] : "Não informado"; ?>
            </span><br>
            <span class="my-registration-fields">E-mail: </span>
            <span class="my-registration-fields-span">
                <?php echo !empty($reg->owner->metadata['emailPublico']) ? $reg->owner->metadata['emailPublico'] : "Não informado"; ?>
            </span><br>
            <span class="my-registration-fields">Telefone Público: </span>
            <span class="my-registration-fields-span">
                <?php echo !empty($reg->owner->metadata['telefonePublico']) ? $reg->owner->metadata['telefonePublico'] : "Não informado"; ?>
            </span><br>
            <span class="my-registration-fields">Telefone 1: </span>
            <span
                class="my-registration-fields-span"><?php echo !empty($reg->owner->metadata['telefone1']) ? $reg->owner->metadata['telefone1'] : "Não informado"; ?>
            </span><br>
            <span class="my-registration-fields">Telefone 2: </span>
            <span
                class="my-registration-fields-span"><?php echo !empty($reg->owner->metadata['telefone2']) ? $reg->owner->metadata['telefone2'] : "Não informado"; ?>
            </span><br>
            <span class="my-registration-fields">Currículo Lattes: </span>
            <span class="my-registration-fields-span">
                <?php echo !empty($reg->owner->metadata['curriculoLattes']) ? $reg->owner->metadata['curriculoLattes'] : "Não informado"; ?>
            </span><br>
            <span class="my-registration-fields">Grau acadêmico: </span>
            <span class="my-registration-fields-span">
                <?php echo !empty($reg->owner->metadata['profissionais_graus_academicos']) ? $reg->owner->metadata['profissionais_graus_academicos'] : "Não informado"; ?>
            </span><br>
        </div>
    </div>
</main>

<?php

if ($allPhases) {
    $query = "SELECT id FROM registration WHERE number = :number AND opportunity_id <= :opportunity_id ORDER BY opportunity_id ASC";
    $params = [
        "number" => $reg->number,
        "opportunity_id" => $reg->opportunity->id
    ];
    $conn = $app->em->getConnection();
    $registrationsIds = $conn->fetchAllAssociative($query, $params);

    foreach ($registrationsIds as $registrationsId) {
        $registration = $app->repo('Registration')->find($registrationsId['id']);
        $fields = EntitiesPdf::showAllFieldAndFile($registration);

        if ($registration->opportunity->status !== Opportunity::STATUS_TRASH)
            $this->part('reports/section', ['field' => $fields, 'reg' => $registration]);
    }
} else {
    $fields = EntitiesPdf::showAllFieldAndFile($reg);

    $this->part('reports/section', ['field' => $fields, 'reg' => $reg]);
}

require THEMES_PATH . 'BaseV1/views/pdf/footer-pdf.php';
