<?php
use MapasCulturais\App;
$reg = $app->view->regObject['reg'];
$urlOpp = App::i()->createUrl('opportunity' . $reg->opportunity->id);

$this->layout = 'nolayout-pdf';
?>

<div>
    <section class="clearfix">
        <article class="main-content">
            <!-- <h5 class="entity-parent-title">
                <div class="icon icon-agent"></div>
                <?php //echo $reg->owner->name; ?>
            </h5>
            <header class="main-content-header">
                <div class="header-content">
                    <div class="avatar">
                        <img class="js-avatar-img" src="http://0.0.0.0:8088/assets/img/avatar--project-636617000-1709737773.png">
                    </div>
                  
                    <div class="entity-type registration-type">
                        <div class="icon icon-project"></div>
                        <a rel="noopener noreferrer">Edital</a>
                    </div>
                    <h4 class="entity-parent-title">
                        <a href="http://0.0.0.0:8088/oportunidade/4446/">

                        </a>
                    </h4>
                    <h2>
                        <a href="<?= $urlOpp; ?>">
                            <?php //echo $reg->opportunity->name; ?>
                        </a>
                    </h2>
                </div>
            </header>
            <div>
                <p>
                    <hr>
                </p>
            </div> -->
            <div>
                <p class="title-bold" style="text-align: center">
                    <label class="title-bold">
                        Emissão do Termo de Aceitação Definitiva do Objeto (TADO)
                    </label>
                </p>
            </div>

            <div class="form-container">
                <p class="title-bold">
                   Identificação
                </p>
                <table>
                    <tbody>
                        <tr>
                            <td class="title-bold">Nº DO TEC : </td>
                            <td class="title-bold">DATA :</td>
                        </tr>
                        <tr>
                            <td class="title-bold">PERÍODO DE VIGÊNCIA : </td>
                        </tr>
                        <tr>
                            <td class="title-bold">AGENTE CULTURAL : </td>
                            <td> <?= $reg->owner->name; ?></td>
                        </tr>
                        <tr>
                            <td class="title-bold">PROJETO : </td>
                            <td> <?= $reg->opportunity->ownerEntity->name; ?> </td>
                        </tr>
                        <tr>
                            <td class="title-bold">EDITAL : </td>
                            <td>
                                <?= $reg->opportunity->name; ?> 
                            </td>
                        </tr>
                        <tr>
                            <td class="title-bold">OBJETO : </td>
                            <td> 
                                <?= $app->view->regObject['tado']->object; ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div>
                    <p>
                        Segundo a Lei no 18.012, de 01 de abril de 2022, o Art. 73:
                    </p>
                    <p>
                        § 3o. O agente público responsável pela análise do Relatório de Execução do Objeto
                        deverá elaborar parecer técnico em que se manifestará:
                    </p>
                    <p>
                        I - pela conclusão de que houve o cumprimento integral do objeto ou pela suficiência
                        do cumprimento parcial, devidamente justificada, e providenciará imediato
                        encaminhamento do processo à autoridade julgadora;
                    </p>
                    <p>
                        <strong> - Conclusão - </strong>
                    </p>
                    <p style="border: 1px solid #c5c5c5; border-radius: 5px">
                        <?= $app->view->regObject['tado']->conclusion; ?>
                    </p>
                    
                </div>
            </div>
                <table>
                    <tbody>
                        <tr>
                            <td class="title-bold"  style="text-align: center">RESPONSÁVEL PELA EMISSÃO - FISCAL</td>
                        </tr>
                    </tbody>
                </table>
                <table>
                    <tbody>
                        <tr>
                            <td class="title-bold" style="width: 40%">NOME : </td>
                            <td> <?= $app->view->regObject['tado']->agentSignature->name; ?> </td>
                        </tr>
                        <tr>
                            <td class="title-bold">CPF : </td>
                            <td>
                                <?= $app->view->regObject['tado']->agentSignature->getMetadata('cpf'); ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
        </article>
    </section>
</div>
