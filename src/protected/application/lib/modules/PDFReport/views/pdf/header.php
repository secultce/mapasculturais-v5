<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link type="text/css" href="<?php $this->asset('css/stylePdfReport.css') ?>" rel="stylesheet" />
</head>

<body>
    <?php if (isset($_GET['idopportunityReport']) && $_GET['idopportunityReport'] > 0) : ?>
        <div class="container">
            <br>
            <a href="#" class="btn btn-primary" id="btn-print-report">
                <i class="fa fa-print"></i>
                Imprimir Relatório
            </a>
        </div>
    <?php endif; ?>
    <table width="100%" style="height: 100px;">
        <thead>
            <tr class="">
                <td style="text-align: center">
                    <img src="<?php echo MODULES_PATH . 'PDFReport/assets/img/logoNova.png'; ?>" height="96px" />
                </td>
            </tr>
        </thead>
    </table>
    <div class="content-info-edital">
        <table width="100%" style="height: 100px; margin-bottom:40px; margin-top:40px;">
            <thead>
                <tr class="">
                    <td style="width: 10%;">
                        <?php if (!empty($op->files['avatar'])): ?>
                            <img src="<?php echo $op->avatar->transform('avatarSmall')->url; ?>" style="width: 80px; height: 80px;">
                        <?php else: ?>
                            <img src="<?php $this->asset('img/pdfreport/avatar--opportunity.png') ?>" style="width: 80px; height: 80px;">
                        <?php endif; ?>
                    </td>
                    <td style="width: 90%;">


                        <label class="title-edital">Edital</label><br>
                        <label class="sub-title-edital"><?php echo $op->ownerEntity->name; ?></label>
                        <br>
                        <label for="" class="title-edital">Oportunidade</label><br>
                        <label class="sub-title-edital"><?php echo $op->name; ?></label>

                    </td>
                </tr>
            </thead>
        </table>
    </div>
