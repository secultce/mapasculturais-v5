
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
                        TERMO DE ACEITAÇÃO DEFINITIVA DO OBJETO
                    </label>
                </p>
            </div>

            <div class="form-container">
                <p class="title-bold">
                    IDENTIFICAÇÃO
                </p>
                <table>
                    <tbody>
                    <tr style="width: 100%">
                        <td class="title-bold" style="width: 28%">Nº DO TEC :</td>
                        <td style="width: 25%"> <?= $tado->number; ?></td>
                        <td  style="width: 15%; float: right" class="title-bold">DATA :</td>
                        <td style="width: 15%"><?= $carbon::parse($tado->createTimestamp)->format('d/m/Y'); ?></td>
                    </tr>

                    <tr  style="width: 100%">
                        <td class="title-bold">PERÍODO DE VIGÊNCIA : </td>
                        <td colspan="3">
                            <?=
                                $carbon::parse($tado->periodFrom)->format('d/m/Y') . ' a '
                                .$carbon::parse($tado->periodTo)->format('d/m/Y')
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="title-bold">AGENTE CULTURAL : </td>
                        <td colspan="3"> <?= $reg->owner->name; ?></td>
                    </tr>
                    <tr>
                        <td class="title-bold">PROJETO : </td>
                        <td colspan="3"> <?= $reg->opportunity->ownerEntity->name; ?> </td>
                    </tr>
                    <tr>
                        <td class="title-bold">EDITAL : </td>
                        <td colspan="3">
                            <?= $reg->opportunity->name; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="title-bold">OBJETO : </td>
                        <td colspan="3">
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
                            <strong> - CONCLUSÃO - </strong>
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
                    <p><br></p>
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
