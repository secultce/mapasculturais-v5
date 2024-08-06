<?php

use Carbon\Carbon;
use MapasCulturais\App;
use Diligence\Repositories\Diligence as RepoDiligence;

$this->layout = 'default';
$urlOpp = App::i()->createUrl('oportunidade/' . $reg->opportunity->id);
$app = App::i();
$this->jsObject['idEntity'] = $reg->id;

//Buscando o tado gerado
$td = new RepoDiligence();
$tado = $td->getTado($reg);

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
                        <img class="js-avatar-img" src="<?php $this->asset('img/avatar--project.png'); ?>">
                    </div>
                    <!--.avatar-->
                    <div class="entity-type registration-type">
                        <div class="icon icon-project"></div>
                        <a rel="noopener noreferrer">Edital</a>
                    </div>
                    <h4 class="entity-parent-title">
                        <a href="#"></a>
                    </h4>
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
                <div class="content-space-bet">
                    <p class="info-regis-tado">
                        Informações de Identificação
                    </p>
                    <?php if (!is_null($tado) && $tado->status == 1) : ?>
                        <p>
                            <a onclick="regenerateTado()" class="btn btn-primary">Baixar Novamente</a>
                        </p>
                    <?php endif; ?>

                </div>

                <div style=" display: flex; justify-content: space-between;">
                    <div class="form-group">
                        <label class="title-info">Número do TEC</label>
                        <input name="numbertec"  id="numbertec" value="<?= !is_null($tado) ? $tado->number : null ?>"/>
                    </div>
                    <div class="form-group">
                        <label class="title-info">Data</label>
                        <input name="dateTado" id="dateTado" class="dateTado" value="<?= Carbon::now()->format('d/m/Y'); ?>" />
                    </div>
                </div>

                <div class="content-space-bet">
                    <div class="form-group">
                        <label class="title-info">Período de Vigência</label>
                        <input name="periodInitial" id="periodInitial" class="dateTado" placeholder="Data Inicial" value="<?= !is_null($tado) ? Carbon::parse($tado->periodFrom)->format('d/m/Y') : null; ?>" />
                    </div>
                    <div class="form-group">
                        <label class="title-info">Data</label>
                        <input name="periodEnd" id="periodEnd" class="dateTado" placeholder="Data Final" value="<?= !is_null($tado) ? Carbon::parse($tado->periodTo)->format('d/m/Y') : null; ?>" />
                    </div>
                </div>

                <div style=" display: flex; justify-content: space-between;">
                    <div class="form-group">
                        <label class="title-label">Agente Cultural</label>
                        <label class="content-value-name">
                            <?= $reg->owner->name; ?>
                        </label>
                    </div>
                </div>

                <div style=" display: flex; justify-content: space-between; margin-top: 10px">
                    <div class="form-group">
                        <label class="title-label">Projeto</label>
                        <label class="content-value-name">
                            <?= $reg->opportunity->ownerEntity->name; ?>
                        </label>
                    </div>
                </div>

                <div style=" display: flex; justify-content: space-between; margin-top: 10px">
                    <div class="form-group">
                        <label class="title-label">Edital</label>
                        <label class="content-value-name">
                            <?= $reg->opportunity->name; ?>
                        </label>
                    </div>
                </div>

                <div style=" display: flex; justify-content: space-between; margin-top: 10px">
                    <div class="form-group">
                        <label class="title-label">Objeto</label>
                        <?php if (is_null($tado)) : ?>
                            <input name="object" id="object" />
                        <?php endif; ?>
                        <?php if (isset($tado) && $tado->status == 0) : ?>
                            <input name="object" id="object" value="<?= $tado->object; ?>" />
                        <?php endif; ?>
                        <?php if (isset($tado) && $tado->status == 1) : ?>
                            <label class="content-value-name">
                                <?= $tado->object; ?>
                            </label>
                        <?php endif; ?>
                        <p class="registration-help ng-scope">
                            Esse campo é obrigatório
                        </p>
                    </div>
                </div>
            </div>

            <div>
                <div class="form-container">
                    <p style="margin-bottom: 10px; font-weight: 700; line-height: 24.51px; font-size: 18px">
                        Conclusão sobre o projeto
                    </p>
                    <?php if (isset($tado) && $tado->status == 0) : ?>
                        <textarea name="conclusionTado" id="conclusionTado" rows="10" cols="100" placeholder="Escreva aqui a sua conclusão sobre o projeto."><?= $tado->conclusion; ?></textarea>
                    <?php endif; ?>
                    <?php
                    if (is_null($tado)) : ?>
                        <textarea name="conclusionTado" id="conclusionTado" rows="10" cols="100" placeholder="Escreva aqui a sua conclusão sobre o projeto."></textarea>
                    <?php endif; ?>
                    <?php if (isset($tado) && $tado->status == 1) : ?>
                        <label class="content-value-name">
                            <?= $tado->conclusion; ?>
                        </label>
                    <?php endif; ?>
                    <p class="registration-help ng-scope">
                        Esse campo é obrigatório
                    </p>
                </div>
            </div>
            <div>
                <div class="form-container">
                    <p class="title-label" style="font-size: 18px;">
                        Fiscal responsável pela emissão
                    </p>
                    <div style=" display: flex; justify-content: space-between;">
                        <div class="form-group">
                            <label class="title-info">Nome do Fiscal</label>
                            <input name="nameFiscal" value="<?= $app->auth->getAuthenticatedUser()->profile->name; ?>" />
                        </div>
                        <div class="form-group">
                            <label class="title-info">CPF do Fiscal</label>
                            <input name="cpf" id="cpfTado" value="<?= $app->auth->getAuthenticatedUser()->profile->getMetadata('cpf'); ?>" />
                        </div>
                    </div>
                    <p class="title-label" style="font-size: 18px;">
                        Gestor responsável pela emissão
                    </p>
                    <div style=" display: flex; justify-content: space-between;">
                        <div class="form-group">
                            <label class="title-info">Nome do gestor</label>
                            <input name="nameManager" id="nameManager" value="<?= isset($tado) ? $tado->nameManager : null ?>" />
                        </div>
                        <div class="form-group">
                            <label class="title-info">CPF do Gestor</label>
                            <input name="cpfManager" id="cpfManager" value="<?= isset($tado) ? $tado->cpfManager : null ?>" />
                        </div>
                    </div>
                </div>
                <div class="form-container footer-action-tado">
                    <?php if ((isset($tado) && $tado->status == 0) || is_null($tado)) : ?>
                        <input type="hidden" id="idTado" value="<?= isset($tado) ? $tado->id : 0 ?>">
                        <div class="form-group">
                            <button class="btn" id="draftTado" title="Salva os valores atuais do seu relatório" style="background: #CED4DA; color: #000000">Salvar Rascunho</button>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary" id="generateTado" title="Finaliza o seu relatório">Finalizar TADO</button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </article>
    </section>
</div>