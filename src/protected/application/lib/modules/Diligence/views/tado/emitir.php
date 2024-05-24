<?php

use MapasCulturais\App;

$this->layout = 'default';
$urlOpp = App::i()->createUrl('opportunity'. $reg->opportunity->id);
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
        </article>
    </section>


</div>

<?php

dump($reg);

?>