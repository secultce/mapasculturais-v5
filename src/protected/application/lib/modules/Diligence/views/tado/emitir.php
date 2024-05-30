<?php

use MapasCulturais\App;

$this->layout = 'default';
$urlOpp = App::i()->createUrl('opportunity' . $reg->opportunity->id);
$app = App::i();
$this->jsObject['idEntity'] = $reg->id;
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
                        <label class="title-info">Número do TEC</label>
                        <input name="numbertec" />
                    </div>
                    <div class="form-group">
                        <label class="title-info">Data</label>
                        <input name="numbertec" />
                    </div>
                </div>

                <div style=" display: flex; justify-content: space-between;">
                    <div class="form-group">
                        <label class="title-info">Período de Vigência</label>
                        <input name="numbertec" placeholder="Data Inicial" />
                    </div>
                    <div class="form-group">
                        <label class="title-info">Data</label>
                        <input name="numbertec" placeholder="Data Final" />
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
                        <input name="object" id="object" />
                    </div>
                </div>
            </div>

            <div>
                <div class="form-container">
                    <p style="margin-bottom: 10px; font-weight: 700; line-height: 24.51px; font-size: 18px">
                        Conclusão sobre o projeto
                    </p>
                    <textarea
                        name="conclusionTado"
                        id="conclusionTado"
                        rows="10"
                        cols="100"
                        placeholder="Escreva aqui a sua conclusão sobre o projeto."></textarea>
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
                            <input name="numbertec" value="<?= $app->auth->getAuthenticatedUser()->profile->getMetadata('cpf'); ?>"/>
                        </div>                   
                    </div>
                </div>
                <div class="form-container">
                <div class="form-group">
                        <button class="btn btn-primary" id="generateTado">Salvar</button>
                    </div>
                </div>
            </div>
        </article>
    </section>
<script>
    
</script>

</div>



<?php
dump($app->auth->getAuthenticatedUser()->profile->getMetadata('cpf'));
dump($reg);

?>