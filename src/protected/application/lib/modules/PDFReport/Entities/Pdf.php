<?php

namespace PDFReport\Entities;

use DateTime;
use MapasCulturais\App;

class Pdf extends \MapasCulturais\Entity
{
    public static function getValueField($id, $registration)
    {
        $body = 'field_' . $id;

        return App::i()->repo('RegistrationMeta')->findBy([
            'key' => $body,
            'owner' => $registration
        ]);
    }

    public static function mask($val, $mask)
    {
        $maskared = '';
        $k = 0;

        for ($i = 0; $i <= strlen($mask) - 1; $i++) {
            if ($mask[$i] == '#') {
                if (isset($val[$k])) $maskared .= $val[$k++];
            } else {
                if (isset($mask[$i])) $maskared .= $mask[$i];
            }
        }

        return $maskared;
    }

    public static function oportunityRegistrationByStatus($idopportunity, $status = 0)
    {
        $app = App::i();
        $opp = $app->repo('Opportunity')->find($idopportunity);

        $where = "";
        if ($status) {
            $where .= "AND r.status = :status ";
        } else {
            $where .= "AND r.status > :status ";
        }

        $dql = "SELECT r FROM MapasCulturais\Entities\Registration r
                WHERE r.opportunity = :opportunity {$where}
                ORDER BY r.consolidatedResult DESC";
        $query = $app->em->createQuery($dql);

        $query->setParameter('opportunity', $idopportunity);
        $query->setParameter('status', $status);

        $regs = $query->getResult();

        return ['opp' => $opp, 'regs' => $regs];
    }

    public static function verifyResource($idOportunidade)
    {
        $opp = App::i()->repo('OpportunityMeta')->findBy(['owner' => $idOportunidade, 'key' => 'claimDisabled']);

        return $opp;
    }

    public static function handleRedirect($error_message, $status_code, $opp_id)
    {
        $app = App::i();
        $_SESSION['error'] = $error_message;
        $url = $app->createUrl('oportunidade/' . $opp_id . '#/tab=inscritos');

        $app->redirect(substr_replace($url, "", -1), $status_code);
    }

    public static function listSubscribedHandle($array, $getData)
    {
        $array['regs'] = self::oportunityRegistrationByStatus($getData['idopportunityReport']);
        if (empty($array['regs']['regs'])) {
            self::handleRedirect('Ops! Não tem inscrito nessa oportunidade.', 401, $getData['idopportunityReport']);
        }
        $array['title'] = 'Relatório de inscritos na oportunidade';
        $array['template'] = 'pdf/subscribers';

        return $array;
    }

    public static function listPreliminaryHandle($array, $getData)
    {
        $array['regs'] = self::oportunityRegistrationByStatus($getData['idopportunityReport']);
        if (empty($array['regs']['regs'])) {
            self::handleRedirect('Ops! A oportunidade deve estar publicada.', 401, $getData['idopportunityReport']);
        }

        $verifyResource = self::verifyResource($getData['idopportunityReport']);
        if (isset($verifyResource[0])) {
            $array['claimDisabled'] = $verifyResource[0]->value;
        }
        $array['title'] = 'Resultado Preliminar do Certame';
        $array['template'] = 'pdf/preliminary';

        return $array;
    }

    public static function listDefinitiveHandle($app, $array, $period = false, $getData)
    {
        $id = $getData['idopportunityReport'];

        $dqlOpMeta = "SELECT op FROM MapasCulturais\Entities\OpportunityMeta op
            WHERE op.owner = :owner";

        $query = $app->em->createQuery($dqlOpMeta);
        $query->setParameter('owner', $id);

        $resultOpMeta = $query->getResult();

        $dateInit = $dateEnd = $hourInit = $hourEnd = "";

        foreach ($resultOpMeta as $key => $valueOpMeta) {
            if ($valueOpMeta->key == 'date-initial') {
                $dateInit = $valueOpMeta->value;
            }
            if ($valueOpMeta->key == 'hour-initial') {
                $hourInit = $valueOpMeta->value;
            }
            if ($valueOpMeta->key == 'date-final') {
                $dateEnd = $valueOpMeta->value;
            }
            if ($valueOpMeta->key == 'hour-final') {
                $hourEnd = $valueOpMeta->value;
            }
        }

        $dateHourNow = new DateTime();
        $dateAndHourInit = $dateInit . ' ' . $hourInit;
        $dateVerifyPeriod = DateTime::createFromFormat('d/m/Y H:i:s', $dateAndHourInit);
        if ($dateHourNow > $dateVerifyPeriod) {
            $period = true;
        }
        if ($period) {
            $array['regs'] = self::oportunityRegistrationByStatus($getData['idopportunityReport'], 10);
            if (empty($array['regs']['regs'])) {
                self::handleRedirect('Ops! Para gerar o relatório definitivo a oportunidade deve estar publicada.', 401, $getData['idopportunityReport']);
            }

            // SELECT AOS RECURSOS
            $dql = "SELECT r FROM Recourse\Entities\Recourse r
                WHERE r.opportunity = :opportunity";
            $query = $app->em->createQuery($dql);
            $query->setParameter('opportunity', $id);

            $resource = $query->getResult();

            $countPublish = 0; // INICIANDO VARIAVEL COM 0
            foreach ($resource as $key => $value) {
                if ($value->replyPublish == 1 && $value->opportunity->publishedRegistrations == 1) {
                    $countPublish++; // SE ENTRAR INCREMENTA A VARIAVEL
                }
            }
            if ($countPublish == count($resource) && $countPublish > 0 && count($resource) > 0) {
                $array['regs'] = self::oportunityRegistrationByStatus($getData['idopportunityReport'], 10);
                $array['title'] = 'Resultado Definitivo do Certame';
                $array['template'] = 'pdf/definitive';
            } else if ($countPublish == count($resource) && $countPublish == 0 && count($resource) == 0) {

                $array['regs'] = self::oportunityRegistrationByStatus($getData['idopportunityReport'], 10);

                if (empty($array['regs']['regs'])) {
                    self::handleRedirect('Ops! Você deve publicar a oportunidade para esse relatório.', 401, $getData['idopportunityReport']);
                }

                $verifyResource = self::verifyResource($getData['idopportunityReport']);

                if (isset($verifyResource[0])) {
                    $array['claimDisabled'] = $verifyResource[0]->value;
                }

                if (isset($regs['regs'][0]) && empty($verifyResource) || $array['claimDisabled'] == 1) {
                    $array['title'] = 'Resultado Definitivo do Certame';
                    $array['template'] = 'pdf/definitive';
                } else if (isset($regs['regs'][0]) && empty($verifyResource) || $array['claimDisabled'] == 0) {
                    $array['title'] = 'Resultado Definitivo do Certame';
                    $array['template'] = 'pdf/definitive';
                } else {
                    $app->redirect($app->createUrl('oportunidade/' . $getData['idopportunityReport'] . '#/tab=inscritos'), 401);
                }
            } else {
                $array['regs'] = self::oportunityRegistrationByStatus($getData['idopportunityReport'], 10);
                $array['title'] = 'Resultado Definitivo do Certame';
                $array['template'] = 'pdf/definitive';
            }
        } else {
            self::handleRedirect('Ops! Ocorreu um erro inesperado.', 401, $getData['idopportunityReport']);
        }
        return $array;
    }

    public static function listContactsHandle($array, $getData)
    {
        $array['regs'] = self::oportunityRegistrationByStatus($getData['idopportunityReport'], 10);
        if (empty($array['regs']['regs'])) {
            self::handleRedirect('', 401, $getData['idopportunityReport']);
        }
        $array['title'] = 'Relatório de contato';
        $array['template'] = 'pdf/contact';

        return $array;
    }

    public static function getSectionNote($opp, $registration, $section_id)
    {
        $total = 0.00;
        $committee = $opp->getEvaluationCommittee();
        $users = [];
        foreach ($committee as $item) {
            $users[] = $item->agent->user->id;
        }

        // AS INSCRIÇÕES AVALIADAS E ENVIADAS
        $status = [
            \MapasCulturais\Entities\RegistrationEvaluation::STATUS_EVALUATED,
            \MapasCulturais\Entities\RegistrationEvaluation::STATUS_SENT
        ];
        $evaluations = App::i()->repo('RegistrationEvaluation')->findByRegistrationAndUsersAndStatus($registration, $users, $status);
        foreach ($evaluations as $eval) {
            $cfg = $eval->getEvaluationMethodConfiguration();
            $category = $eval->registration->category;
            $totalSection = 0.00;
            foreach ($cfg->criteria as $cri) {
                if ($section_id == $cri->sid) {
                    $key = $cri->id;
                    if (!isset($eval->evaluationData->$key)) {
                        return null;
                    } else {
                        $val = floatval($eval->evaluationData->$key);
                        $totalSection += is_numeric($val) ? floatval($cri->weight) * floatval($val) : 0;
                    }
                }
            }
            $total += floatval($totalSection);
        }

        // TOTAL DE AVALIAÇÕES
        $num = count($evaluations);
        // SE TIVER UMA OU MAIS AVALIAÇÃO
        if ($num > 0) {
            // NOTA DA AVALIAÇÃO DIVIDIDA PELO TOTAL DE AVALIAÇÃO
            return  number_format($total / $num, 2);
        } else {
            return null;
        }
    }

    public static function clearCPF_CNPJ($valor)
    {
        $valor = trim($valor);
        $valor = str_replace(".", "", $valor);
        $valor = str_replace(",", "", $valor);
        $valor = str_replace("-", "", $valor);
        $valor = str_replace("/", "", $valor);

        return $valor;
    }

    /**
     * Metodo que converte uma string de json em array
     *
     * @param [type] $value o valor que vem do campo $valueMetas
     * @param [type] $field string do nome do campo do indice array
     * @param [type] $nameField string do nome do campo do valor do array
     * @return void showDecode($valueMeta->value, 'title')
     */
    public static function showDecode($valueStr, $field = null, $nameField)
    {
        $stringDecodeJson = json_decode($valueStr, true);
        if (!is_iterable($stringDecodeJson)) {
            $stringDecodeJson = [[$nameField => $stringDecodeJson]];
        }

        $arrayItens = [];
        foreach ($stringDecodeJson as $item) {
            if (!is_null($field)) {
                if (isset($item[$field])) {
                    $arrayItens[] = "<strong>Titulo: </strong>" . $item[$field] . " - " . $item[$nameField];
                } else {
                    $arrayItens[] = $item[$nameField];
                }
            } else {
                $arrayItens[] = $item[$nameField];
            }
        }
        echo implode(", ", $arrayItens);
    }

    public static function showAddress($metaData)
    {
        $endereco = json_decode($metaData, true);

        $additional     = (isset($endereco['En_Complemento']) && $endereco['En_Complemento'] != '') ? ", " . $endereco['En_Complemento'] : "";
        $neighborhood   = (isset($endereco['En_Bairro']) && $endereco['En_Bairro'] != '') ? ", " . $endereco['En_Bairro'] : "";
        $city           = (isset($endereco['En_Municipio']) && $endereco['En_Municipio'] != '') ? ", " . $endereco['En_Municipio'] : "";
        $state          = (isset($endereco['En_Estado']) && $endereco['En_Estado'] != '') ? ", " . $endereco['En_Estado'] : "";
        $cep            = (isset($endereco['En_CEP']) && $endereco['En_CEP'] != '') ? ", " . $endereco['En_CEP'] : "";
        $address_number = (isset($endereco['En_Num']) && $endereco['En_Num'] != '') ? ", " . $endereco['En_Num'] : "";
        $street         = (isset($endereco['En_Nome_Logradouro']) && $endereco['En_Nome_Logradouro'] != '') ? $endereco['En_Nome_Logradouro'] : "";
        // montando endereço caso o $endereco == null
        $address = $street .  $address_number . $additional . $neighborhood . $cep . $city . $state;
        echo $address;
    }

    public static function showItensCheckboxes($str)
    {
        $strToarray = explode(',', $str);
        $items = "";
        foreach ($strToarray as $options) {
            $item = trim(preg_replace('/\PL/u', ' ', $options)) . ",";
            $items .= ' ' . $item;
        }
        echo substr($items, 0, -1);
    }

    public static function showAgenteOwnerField($field, $metaData)
    {
        $valueField = null;
        $configEntityField = $field['config']['entityField'];

        if (isset($metaData)) {
            if ($configEntityField == '@location') {
                $location = json_decode($metaData, true);
                if (isset($location['En_Complemento'])) {
                    $valueField = "CEP: " . $location['En_CEP'] . ', 
                    Logradouro: ' . $location['En_Nome_Logradouro'] . ', 
                    Nº: ' . $location['En_Num'] . ', Comp: ' . $location['En_Complemento'] . ', 
                    Bairro: ' . $location['En_Bairro'] . ', 
                    Cidade: ' . $location['En_Municipio'] . ', 
                    UF: ' . $location['En_Estado'];
                } else {
                    $valueField = "CEP: " . $location['En_CEP'] . ', 
                    Logradouro: ' . $location['En_Nome_Logradouro'] . ', 
                    Nº: ' . $location['En_Num'] . ', 
                    Bairro: ' . $location['En_Bairro'] . ', 
                    Cidade: ' . $location['En_Municipio'] . ', 
                    UF: ' . $location['En_Estado'];
                }
            } elseif (
                $configEntityField == '@terms:area' ||
                $configEntityField == 'longDescription'
            ) {
                $valueField = trim(preg_replace('/\PL/u', ' ', $metaData));
            } elseif (
                $configEntityField == 'name' || $configEntityField == 'nomeCompleto' || $configEntityField == 'shortDescription' ||
                $configEntityField == "genero" || $configEntityField == 'telefone1' || $configEntityField == 'telefone2' || $configEntityField == 'emailPrivado' || $configEntityField == 'emailPublico' || $configEntityField == 'rg'
            ) {
                $valueField = str_replace('"', '', json_decode($metaData));
            } elseif (
                $configEntityField == "facebook" || $configEntityField == "intagram" ||
                $configEntityField == "twitter" || $configEntityField == "site" ||
                $configEntityField == "googleplus"
            ) {
                $valueField = str_replace(array('\\', '"'), '', $metaData);
            } elseif ($configEntityField == 'dataDeNascimento') {
                $metaData = str_replace('"', '', $metaData);
                if ($metaData != '' && strtotime($metaData)) {
                    $date = new DateTime($metaData);
                    $valueField = $date->format('d/m/Y');
                }
            } elseif ($configEntityField == 'documento') { // PARA FORMATAR CPF OU CNPJ
                $doc = self::clearCPF_CNPJ(str_replace('"', '', $metaData)); // retirando formatação caso venha
                $str = strlen($doc); // total de carecteres
                if ($str == 11) {
                    $valueField = self::mask($doc, '###.###.###-##');
                } else {
                    $valueField = self::mask($doc, '##.###.###/####-##');
                }
            }
        }

        if (!is_null($valueField)) {
            echo $valueField;
        } else {
            echo '<span class="my-reg-font-10">Não informado</span>';
        }
    }

    public static function showAgentCollectiveField($field, $metaData)
    {
        if ($field == '@location') {
            self::showAddress($metaData);
        } else 
        if (
            $field == 'name' || $field == '@terms:area' ||
            $field == 'shortDescription' || $field == 'longDescription' ||
            $field == 'telefone1' || $field == 'telefone2'
        ) {
            echo trim(preg_replace('/\PL/u', ' ', $metaData)) . "";
        } else 
        if ($field == '@links') {
            self::showDecode($metaData, 'title', 'value');
        } else
        if ($field == "facebook" || $field == "intagram" || $field == "twitter" || $field == "site") {
            echo str_replace(array('\\', '"'), '', $metaData);
        }
    }

    public static function showSpaceField($field, $metaData)
    {
        if ($field == '@location') {
            self::showAddress($metaData);
        } else
        if (
            $field == 'name' || $field == '@terms:area' ||
            $field == 'shortDescription' ||
            $field == 'longDescription'
        ) {
            echo trim(preg_replace('/\PL/u', ' ', $metaData));
        } else
        if ($field == '@links') {
            self::showDecode($metaData, 'title', 'value');
        } else
        if ($field == 'telefone1' || $field == 'telefone2') {
            echo str_replace(array('\'', '"'), '', $metaData);
        } else
        if ($field == "facebook" || $field == "intagram" || $field == "twitter" || $field == "site") {
            echo str_replace(array('\\', '"'), '', $metaData);
        } else {
            echo trim(preg_replace('/\PL/u', ' ', $metaData));
        }
    }

    public static function getDependenciesField($registration, $fields)
    {
        $app = App::i();
        $show = true;
        $fieldRegMeta = '';
        $valueRegMeta = '';

        if ($fields['fieldType'] !== 'file') {
            if (is_array($fields['config'])) {
                foreach ($fields['config'] as $keyConf => $valConf) {
                    if (isset($valConf['value'])) {
                        $valueRegMeta = $valConf['value'];
                        $fieldRegMeta = $valConf['field'];
                    }
                }
            }

            $regField = $app->repo('RegistrationMeta')->findBy([
                'owner' => $registration,
                'key' => $fieldRegMeta
            ]);

            foreach ($regField as $key => $valregField) {
                if ($valueRegMeta !== $valregField->value) {
                    $show = false;
                }
            }
        }

        return $show;
    }

    /**
     * Metodo para verificação dos arquivos enviados na oportunidade
     *
     * @param [type] $registration
     * @param [type] $fileGroup
     * @return void
     */
    public static function getFileRegistration($registration, $fileGroup)
    {
        $file = App::i()->repo('RegistrationFile')->findBy([
            'owner' => $registration,
            'group' => $fileGroup
        ]);

        if (count($file) > 0) {
            return $file;
        }
    }

    public static function showAllFieldAndFile($registration)
    {
        $fields = []; // array vazio

        $registrationOpportunity = $registration->opportunity;
        foreach ($registrationOpportunity->registrationFieldConfigurations as $field) {
            //ATRIBUINDO ARRAY DOS CAMPOS AO ARRAY
            array_push($fields, [
                'displayOrder' => $field->displayOrder,
                'id' => $field->id,
                'title' => $field->title,
                'description' => $field->description,
                'fieldType' => $field->fieldType,
                'config' => $field->config,
                'owner' => $field->owner,
                'categories' => $field->categories
            ]);
        }
        //VERIFICANDO DE TEM ARQUVIOS
        if (count($registrationOpportunity->registrationFileConfigurations) > 0) {
            foreach ($registrationOpportunity->registrationFileConfigurations as $key => $file) {
                $fileRegistration = self::getFileRegistration($registration, $file->fileGroupName);

                $registrationFile = (array) $fileRegistration;
                $config = [];
                //
                if ($file->multiple) {
                    foreach ($registrationFile as $key => $fileValue) {
                        array_push($config, [
                            'id'    => $fileValue->id,
                            'group' => $fileValue->group,
                            'name'  => $fileValue->name,
                            'owner' => $fileValue->owner
                        ]);
                    }
                } else {
                    foreach ($registrationFile as $key => $conf) {
                        array_push($config, [
                            'id'    => $conf->id,
                            'group' => $conf->group,
                            'name'  => $conf->name,
                            'owner' => $conf->owner
                        ]);
                    }
                }
                array_push($fields, [
                    'displayOrder' => $file->displayOrder,
                    'id' => $file->id,
                    'title' => $file->title,
                    'description' => $file->description,
                    'fieldType' => 'file',
                    'config' => $config,
                    'owner' => $file->owner,
                    'multiple' => $file->multiple,
                    'categories' => $file->categories
                ]);
            }
        }
        $column_order = array_column($fields, 'displayOrder');
        array_multisort($column_order, SORT_ASC, $fields);

        return $fields;
    }

    public static function sortArrayForNAEvaluations($sub, $opp)
    {

        $app = App::i();

        $committee = $opp->getEvaluationCommittee();

        $users = [];
        foreach ($committee as $item) {
            $users[] = $item->agent->user->id;
        }

        $status = [
            \MapasCulturais\Entities\RegistrationEvaluation::STATUS_EVALUATED,
            \MapasCulturais\Entities\RegistrationEvaluation::STATUS_SENT
        ];

        usort($sub, function ($item1, $item2) use ($app, $users, $status) {
            // Comparação das notas para saber se os itens possuem a mesma nota consolidade para que possamos fazer a verificação das notas dos criterios
            if ($item1->consolidatedResult == $item2->consolidatedResult) {
                // Pegando as avaliações dos objetos que estão sendo comparados para ordenação;
                $evaluations_1 = $app->repo('RegistrationEvaluation')->findByRegistrationAndUsersAndStatus($item1, $users, $status);
                $evaluations_2 = $app->repo('RegistrationEvaluation')->findByRegistrationAndUsersAndStatus($item2, $users, $status);
                $eval_1 = null;
                $eval_2 = null;
                foreach ($evaluations_1 as $eval) {
                    if (empty($eval_1)) {
                        $eval_1 = $eval->evaluationData;
                    } else {
                        $notes = $eval->evaluationData;
                        foreach ($notes as $key => $value) {
                            if ($key != 'na' && $key != 'obs') {
                                if ($eval_1->$key == "" && $value == "") {
                                } else if ($eval_1->$key == "") {
                                    $eval_1->$key = $value;
                                } else if ($value == "") {
                                } else {
                                    $eval_1->$key += $value;
                                    $eval_1->$key = $eval_1->$key / count($users);
                                }
                            }
                        }
                    }
                }
                foreach ($evaluations_2 as $eval) {
                    if (empty($eval_2)) {
                        $eval_2 = $eval->evaluationData;
                    } else {
                        $notes = $eval->evaluationData;
                        foreach ($notes as $key => $value) {
                            if ($key != 'na' && $key != 'obs') {
                                if ($eval_2->$key == "" && $value == "") {
                                } else if ($eval_2->$key == "") {
                                    $eval_2->$key = $value;
                                } else if ($value == "") {
                                } else {
                                    $eval_2->$key += $value;
                                    $eval_2->$key = $eval_2->$key / count($users);
                                }
                            }
                        }
                    }
                }
                foreach ($eval_1 as $key => $value) {
                    if ($key != 'na' && $key != 'obs') {
                        if ($value != "" && $eval_2->$key == "") {
                            return -1;
                        } else if ($value == "" && $eval_2->$key != "") {
                            return 1;
                        } else if ($value < $eval_2->$key) {
                            return 1;
                        } else if ($value > $eval_2->$key) {
                            return -1;
                        }
                    }
                }
                return 0;
            }
            return ($item1->consolidatedResult < $item2->consolidatedResult) ? 1 : -1;
        });
        return $sub;
    }
}
