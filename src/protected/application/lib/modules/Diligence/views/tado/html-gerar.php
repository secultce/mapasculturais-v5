<?php
use MapasCulturais\App;
$reg = $app->view->regObject['reg'];
$urlOpp = App::i()->createUrl('opportunity' . $reg->opportunity->id);

$this->layout = 'nolayout-pdf';
?>
<head>
    <style type="text/css">
        section, table, div { font-family: Open Sans, sans-serif !important;}
    </style>
</head>
<div>
    <section class="clearfix">
        <article class="main-content">
            <div>
                <p style="text-align: center">
                    <img src="<?= MODULES_PATH . 'Diligence/assets/img/logo_secult.jpg' ?>" width="128" alt="">
                </p>
            </div>
            <div>
                <p class="title-bold" style="text-align: center">
                    <label class="title-bold">
                        Termo de Aceitação Definitiva do Objeto (TADO)
                    </label>
                </p>
            </div>

            <div class="form-container">
                <p class="title-bold">
                   IDENTIFICAÇÃO
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
                        <br>
                    </p>
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


                    <div style="padding: 5px;">

                        <p>
                            <strong> - Conclusão - </strong>
                            <?= $app->view->regObject['tado']->conclusion; ?>
                        </p>
                    </div>
                    
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
