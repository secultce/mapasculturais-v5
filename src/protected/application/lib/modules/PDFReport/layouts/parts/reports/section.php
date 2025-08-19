<?php

use PDFReport\Entities\Pdf;

/**
 * RETORNO DOS METADATAS DO AGENTE COM OS INDICES SENDO O VALOR QUE ESTÁ 
 * EM KEY NA TABELA E O RESULTADO SENDO O VALOR QUE ESTÁ EM VALUE NA TABELA
 */
$result = $reg->getAgentsData();
unset($result['owner']['nomeCompleto']);

$newAgentData = [];
$newAgentData['shortDescription'] = $reg->owner->shortDescription;
$newAgentData['longDescription'] = $reg->owner->longDescription;
$newAgentData['nomeCompleto'] = $reg->owner->nomeCompleto;

$agentMetaData = array_merge($result['owner'] ?? [], $newAgentData);
$registrationMeta = $reg->getMetadata();

?>

<div class="border-section">
    <h4 style="color: rgba(0, 0, 0, 0.87); font-family: Arial !important;">
        <?php echo $reg->opportunity->name; ?>
    </h4>

    <?php
    $check = 'Não confirmado';
    $fieldValueAll = [];
    $categories = [];
    $isCategory = false;

    foreach ($field as $fie => $fields) :
        $valueMetas = Pdf::getValueField($fields['id'], $reg->id);
        $showSpan = Pdf::getDependenciesField($reg, $fields);

        if (!is_null($fields['categories'])) {
            $categories = $fields['categories'];
        }

        if ($showSpan == true):
            // SE CATEGORIA FOR VAZIO, MOSTRA O NOME DOS CAMPOS
            if (empty($categories)) {
    ?>
                <span class="span-section">
                    <?php
                    if ($fields['fieldType'] === 'section') {
                        echo "<hr><br>";
                        echo '<u>' . $fields['title'] . '</u><br />';
                    } else {
                        echo $fields['title'] . ': ';
                    }
                    ?>
                </span>
            <?php
                // SE TIVER CATEGORIA E FOR IGUAL A CATEGORIA DA INSCRIÇÃO, MOSTRA O CAMPO
            } elseif (in_array($reg->category, $categories)) {
                $isCategory = true;
            ?>
                <span class="span-section">
                    <?php
                    if ($fields['fieldType'] === 'section') {
                        echo "<hr><br>";
                        echo '<u>' . $fields['title'] . '</u><br />';
                    } else {
                        echo $fields['title'] . ': ';
                    }
                    ?>
                </span>
    <?php }
            $iniSpan = '<span class="my-registration-value-span">';
            $endSpan = '</span><br />';
            foreach ($valueMetas as $keyMeta => $valueMeta) {

                if ($fields['fieldType'] !== "agent-owner-field") {
                    echo $iniSpan;
                    if ($fields['fieldType'] == 'checkbox') {
                        if ($valueMeta->value) {
                            echo $fields['description'];
                        } else {
                            echo "Não informado";
                        }
                    } else if ($fields['fieldType'] == 'cnpj') {

                        echo Pdf::mask($valueMeta->value, '##.###.###/####-##');
                    } else if ($fields['fieldType'] == 'cpf') {

                        echo Pdf::mask($valueMeta->value, '###.###.###-##');
                    } else if ($fields['fieldType'] == 'persons') {

                        Pdf::showDecode($valueMeta->value, null, 'name');
                    } else if ($fields['fieldType'] == 'space-field') {

                        Pdf::showSpaceField($fields['config']['entityField'], $valueMeta->value);
                    } else if ($fields['fieldType'] == 'date') {
                        echo date("d/m/Y", strtotime($valueMeta->value));
                    } else if ($fields['fieldType'] == 'links') {

                        Pdf::showDecode($valueMeta->value, 'title', 'value');
                    } else if ($fields['fieldType'] == 'checkboxes') {

                        Pdf::showItensCheckboxes($valueMeta->value);
                    } else if ($fields['fieldType'] == 'agent-collective-field') {

                        Pdf::showAgentCollectiveField($fields['config']['entityField'], $valueMeta->value);
                    } else if ($valueMeta->value !== "") {
                        echo $valueMeta->value;
                    } else {
                        echo '<span class="my-reg-font-10">Não informado</span>';
                    }
                    echo $endSpan;
                }
            }

            if ($fields['fieldType'] == 'agent-owner-field') {   // PARA O TIPO DE CAMPO DE AGENTE 
                echo $iniSpan;

                Pdf::showAgenteOwnerField($fields, $valueMeta->value);

                echo $endSpan;
            }

            if ($fields['fieldType'] == 'file') {

                if (empty($categories)) {

                    require 'content-file.php';
                } elseif (in_array($reg->category, $categories)) {

                    require 'content-file.php';
                }
            }
        endif; // ENDIF showSpan
    endforeach;
    ?>
</div>
