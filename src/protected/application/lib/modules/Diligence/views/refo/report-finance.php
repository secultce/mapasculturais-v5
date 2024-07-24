<?php
use MapasCulturais\App;

$this->layout = 'nolayout-pdf';
?>
<head>
    <style type="text/css">
        section, table, div { font-family: Open Sans, sans-serif !important;}
    </style>
</head>
<div>
    <section class="clearfix">
        <div>
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
                <div class="col-md-6" style="width: 40%; float: left;">
                    <label class="title-ins-label">Inscrição</label> <br>
                    <label class="title-ins-sublabel">on-125477</label>
                </div>
                <div class="col-md-6  title-ins-sublabel-right" style="width: 50%;float: left;">
                    <label class="title-ins-sublabel">
                        24/07/2024
                    </label> <br>
                </div>
            </div>
        </div>
    </div>
    <table width="100%" style="height: 100px; margin-top: 16px">
        <thead>
            <tr class="">
                <td style="width: 10%;">
                    <?php if (true) : ?>
                    <img src="https://mapacultural.secult.ce.gov.br/files/opportunity/5317/file/5295876/blob.-6b12f99fc1a163b1dbf533e5516cb474.png"
                        style="width: 80px; height: 80px; border: 1px solid #c5c5c5; margin-right: 8px">
                    <?php else : ?>
                    <img src="<?php echo THEMES_PATH . 'BaseV1/assets/img/avatar--opportunity.png'; ?>"
                        style="width: 80px; height: 80px;margin:8px;">
                    <?php endif; ?>
                </td>
                <td style="width: 90%;">
                    <div>
                        <div class="multi-title-edital">
                            <label class="">Edital</label><br>
                        </div>
                        <div class="multi-sub-title-edital">
                            <label class="">Edital teste</label>
                        </div>
                    </div>
                    <div>
                        <div class="multi-title-edital">
                            <label for="" class="title-edital">Oportunidade</label><br>
                        </div>
                        <div class="multi-sub-title-edital">
                            <label class="sub-title-edital">Oportunidade Teste</label>
                        </div>
                    </div>
                </td>
            </tr>
        </thead>
    </table>
    </section>
</div>