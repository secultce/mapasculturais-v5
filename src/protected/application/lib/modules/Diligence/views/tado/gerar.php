<?php

use MapasCulturais\App;
use Dompdf\Dompdf;

$this->layout = 'nolayout';
$urlOpp = App::i()->createUrl('opportunity' . $reg->opportunity->id);
$app = App::i();
$dompdf = new Dompdf();
$dompdf->loadHtml('hello world');
?>


<div>
    <section class="clearfix">
        <article class="main-content">
            <h5 class="entity-parent-title">
                <div class="icon icon-agent"></div>
                <?php echo $reg->owner->name; ?>
            </h5>
            <header class="main-content-header">
                <div class="header-content">
                    <div class="avatar">
                        <img class="js-avatar-img" src="http://0.0.0.0:8088/assets/img/avatar--project-636617000-1709737773.png">
                    </div>
                    <!--.avatar-->
                    <div class="entity-type registration-type">
                        <div class="icon icon-project"></div>
                        <a rel="noopener noreferrer">Edital</a>
                    </div>
                    <!--.entity-type-->
                    <!-- BaseV1/layouts/parts/entity-parent.php # BEGIN -->
                    <h4 class="entity-parent-title">
                        <a href="http://0.0.0.0:8088/oportunidade/4446/">

                        </a>
                    </h4>
                    <!-- BaseV1/layouts/parts/entity-parent.php # END -->
                    <h2>
                        <a href="<?= $urlOpp; ?>">
                            <?php echo $reg->opportunity->name; ?>
                        </a>
                    </h2>
                </div>
            </header>
            <div>
                <p>
                    <hr>
                </p>
            </div>
            <div>
                <p style="display: flex;justify-content: center;">
                    <label style="font-weight: 700; font-size: 20px; line-height: 27.24px;">
                        Emissão do Termo de Aceitação Definitiva do Objeto (TADO)
                    </label>
                </p>
            </div>

            <div class="form-container">
                <p style="margin-bottom: 15px; font-weight: 700; line-height: 24.51px; font-size: 18px">
                    Informações de Identificação
                </p>
                <div style=" display: flex; justify-content: space-between;">
                    <div class="form-group">
                        <label style="font-weight: 500; font-size: 14px; line-height: 19px;">Número do TEC</label>
                        <input name="numbertec" />
                    </div>
                    <div class="form-group">
                        <label style="font-weight: 500; font-size: 14px; line-height: 19px;">Data</label>
                        <input name="numbertec" />
                    </div>
                </div>

                <div style=" display: flex; justify-content: space-between;">
                    <div class="form-group">
                        <label style="font-weight: 500; font-size: 14px; line-height: 19px;">Período de Vigência</label>
                        <input name="numbertec" placeholder="Data Inicial" />
                    </div>
                    <div class="form-group">
                        <label style="font-weight: 500; font-size: 14px; line-height: 19px;">Data</label>
                        <input name="numbertec" placeholder="Data Final" />
                    </div>
                </div>

                <div style=" display: flex; justify-content: space-between;">
                    <div class="form-group">
                        <label style="font-weight: 600; font-size: 14px; line-height: 19px;">Agente Cultural</label>
                        <label class="content-value-name">
                            <?= $reg->owner->name; ?>
                        </label>
                    </div>
                </div>

                <div style=" display: flex; justify-content: space-between; margin-top: 10px">
                    <div class="form-group">
                        <label style="font-weight: 600; font-size: 14px; line-height: 19px;">Projeto</label>
                        <label class="content-value-name">
                            <?= $reg->opportunity->ownerEntity->name; ?>
                        </label>
                    </div>
                </div>

                <div style=" display: flex; justify-content: space-between; margin-top: 10px">
                    <div class="form-group">
                        <label style="font-weight: 600; font-size: 14px; line-height: 19px;">Edital</label>
                        <label class="content-value-name">EDITAL DE CHAMAMENTO PÚBLICO Nº 01/2024 -
                            EDITAL PARA AS DEMAIS ÁREAS CULTURAIS - SELEÇÃO DE PROJETOS PARA FIRMAR TERMO DE EXECUÇÃO
                            CULTURAL COM RECURSOS DA LEI COMPLEMENTAR 195/2022 (LEI PAULO GUSTAVO) CAMOCIM – CE.</label>
                    </div>
                </div>

                <div style=" display: flex; justify-content: space-between; margin-top: 10px">
                    <div class="form-group">
                        <label style="font-weight: 600; font-size: 14px; line-height: 19px;">Objeto</label>
                        <label class="content-value-name">O nome do Objeto</label>
                    </div>
                </div>
            </div>

            <div>
                <div class="form-container">
                    <p style="margin-bottom: 15px; font-weight: 700; line-height: 24.51px; font-size: 18px">
                        Conclusão sobre o projeto
                    </p>
                    <textarea name="" id="" rows="10" cols="100"></textarea>
                </div>
            </div>
            <div>
                <div class="form-container">
                    <p style="margin-bottom: 15px; font-weight: 700; line-height: 24.51px; font-size: 18px">
                        Fiscal responsável pela emissão
                    </p>
                    <div style=" display: flex; justify-content: space-between;">
                        <div class="form-group">
                            <label style="font-weight: 500; font-size: 14px; line-height: 19px;">Nome do Fiscal</label>
                            <input name="nameFiscal" value="<?= $app->auth->getAuthenticatedUser()->profile->name; ?>" />
                        </div>
                        <div class="form-group">
                            <label style="font-weight: 500; font-size: 14px; line-height: 19px;">CPF do Fiscal</label>
                            <input name="numbertec" value="<?= $app->auth->getAuthenticatedUser()->profile->getMetadata('cpf'); ?>" />
                        </div>
                    </div>
                </div>
            </div>
        </article>
    </section>
</div>