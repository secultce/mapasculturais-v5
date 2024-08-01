
<div>

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
                <table style="margin-top: 10px;">
                    <tbody>
                    <tr style="width: 100%;">
                        <td class="title-bold multi-report-text-left" style="width: 20%;">Nº DO TEC :</td>
                        <td style="width: 30%" class="multi-report-text-left"> <?= $tado->number; ?></td>
                        <td style="width: 25%; float: right" class="title-bold multi-report-text-left">DATA :</td>
                        <td style="width: 25%" class="multi-report-text-left"><?= $carbon::parse($tado->createTimestamp)->format('d/m/Y'); ?></td>
                    </tr>

                    <tr  style="width: 100%">
                        <td class="title-bold multi-report-text-left multi-report-title-left-width" >PERÍODO DE VIGÊNCIA : </td>
                        <td colspan="3" class="multi-report-text-left multi-report-title-right-width">
                            <?=
                                $carbon::parse($tado->periodFrom)->format('d/m/Y') . ' a '
                                .$carbon::parse($tado->periodTo)->format('d/m/Y')
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="title-bold multi-report-text-left multi-report-title-left-width">
                            AGENTE CULTURAL :
                        </td>
                        <td colspan="3" class="multi-report-text-left multi-report-title-right-width">
                            <?= $reg->owner->name; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="title-bold multi-report-text-left multi-report-title-left-width">
                            PROJETO :
                        </td>
                        <td colspan="3" class="multi-report-text-left multi-report-title-right-width">
                            <?= $reg->opportunity->ownerEntity->name; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="title-bold multi-report-text-left multi-report-title-left-width">
                            EDITAL :
                        </td>
                        <td colspan="3" class="multi-report-text-left multi-report-title-right-width">
                            <?= $reg->opportunity->name; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="title-bold multi-report-text-left multi-report-title-left-width">
                            OBJETO :
                        </td>
                        <td colspan="3" class="multi-report-text-left multi-report-title-right-width">
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
                    <td class="title-bold">CPF : </td>
                    <td>
                        <?= $app->view->regObject['tado']->agentSignature->getMetadata('cpf'); ?>
                    </td>
                </tr>
                </tbody>
            </table>

</div>
