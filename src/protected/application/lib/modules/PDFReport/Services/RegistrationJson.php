<?php

namespace PDFReport\Services;

use MapasCulturais\App;
use MapasCulturais\Entities\Opportunity;
use MapasCulturais\Entities\Registration;
use OpportunityPhases\Module as OpportunityPhases;
use PDFReport\Entities\Pdf;

/**
 * Monta, em array, o mesmo conteúdo do PDF imprimir_inscricao_todas_as_fases,
 * já formatado para ser serializado em JSON.
 */
class RegistrationJson
{
    // status das oportunidades que são fases publicadas
    const PHASE_PUBLISHED = -1;

    const AGENT_METADATA = [
        'site',
        'genero',
        'orientacaoSexual',
        'raca',
        'emailPrivado',
        'emailPublico',
        'telefonePublico',
        'telefone1',
        'telefone2',
        'curriculoLattes',
        'profissionais_graus_academicos',
    ];

    /**
     * @param Registration $reg inscrição informada na rota (de qualquer fase)
     * @param bool $untilLatest não corta a lista na fase informada e devolve a última inscrição
     * @param bool $summary devolve as fases sem o conteúdo (sections) e sem o agente
     */
    public static function buildAll(Registration $reg, $untilLatest = false, $summary = false)
    {
        $app = App::i();

        $opportunities = self::phaseOpportunities($reg, $untilLatest);
        $currentIndex = null;
        foreach ($opportunities as $i => $opportunity) {
            if ($opportunity->id == $reg->opportunity->id) {
                $currentIndex = $i;
            }
        }

        if ($currentIndex === null && !$untilLatest) {
            $opportunities[] = $reg->opportunity;
            $currentIndex = count($opportunities) - 1;
        }

        $phases = [];
        foreach ($opportunities as $i => $opportunity) {
            if (!$untilLatest && $i > $currentIndex) {
                break;
            }

            $phaseReg = $opportunity->id == $reg->opportunity->id
                ? $reg
                : $app->repo('Registration')->findOneBy(['opportunity' => $opportunity, 'number' => $reg->number]);

            if (!$phaseReg) {
                continue;
            }

            $next = isset($opportunities[$i + 1]) ? $opportunities[$i + 1] : null;

            $phase = [
                'order' => $i + 1,
                'isFirstPhase' => $i === 0,
                'registrationId' => $phaseReg->id,
                'number' => $phaseReg->number,
                'category' => self::emptyToNull($phaseReg->category),
                'status' => (int) $phaseReg->status,
                'sentDate' => self::formatDate($phaseReg->sentTimestamp, 'd/m/Y'),
                'opportunity' => [
                    'id' => $opportunity->id,
                    'name' => $opportunity->name,
                    'registrationFrom' => self::formatDate($opportunity->registrationFrom, DATE_ATOM),
                    'registrationTo' => self::formatDate($opportunity->registrationTo, DATE_ATOM),
                ],
                'hasNextPhase' => $next !== null,
                'nextPhase' => $next ? [
                    'opportunityId' => $next->id,
                    'registrationFrom' => self::formatDate($next->registrationFrom, DATE_ATOM),
                ] : null,
            ];

            if (!$summary) {
                $phase['sections'] = self::buildSections($phaseReg);
            }

            $phases[] = $phase;
        }

        $result = ['registration' => self::registrationInfo($reg)];

        if (!$summary) {
            $result['agent'] = self::agentInfo($reg->owner);
        }

        if ($untilLatest) {
            $latest = null;
            foreach ($phases as $phase) {
                if ($phase['status'] > Registration::STATUS_DRAFT) {
                    $latest = $phase;
                }
            }

            $result['latestRegistrationId'] = $latest ? $latest['registrationId'] : null;
            $result['latestPhase'] = $latest ? [
                'order' => $latest['order'],
                'opportunityId' => $latest['opportunity']['id'],
                'name' => $latest['opportunity']['name'],
            ] : null;
            $result['totalPhases'] = count($opportunities);
        }

        $result['phases'] = $phases;

        return $result;
    }

    /**
     * Fases do edital na ordem real do OpportunityPhases: primeira fase e depois getPhases().
     *
     * No modo $publishedOnly só entram fases publicadas, porque getPhases() inclui as
     * fases em rascunho para quem tem @control e o resultado variaria conforme o usuário.
     *
     * @return Opportunity[]
     */
    private static function phaseOpportunities(Registration $reg, $publishedOnly)
    {
        $first = $reg->opportunity->firstPhase;

        $opportunities = [];
        if ($first->status !== Opportunity::STATUS_TRASH) {
            $opportunities[] = $first;
        }

        foreach (OpportunityPhases::getPhases($first) as $phase) {
            if ($publishedOnly && $phase->status !== self::PHASE_PUBLISHED) {
                continue;
            }
            $opportunities[] = $phase;
        }

        return $opportunities;
    }

    private static function registrationInfo(Registration $reg)
    {
        $opportunity = $reg->opportunity;
        $avatar = isset($opportunity->files['avatar']) ? $opportunity->files['avatar'] : null;
        $header = $opportunity->getFile('img:header');

        return [
            'id' => $reg->id,
            'number' => $reg->number,
            'category' => self::emptyToNull($reg->category),
            'status' => (int) $reg->status,
            'sentTimestamp' => self::formatDate($reg->sentTimestamp, DATE_ATOM),
            'sentDate' => self::formatDate($reg->sentTimestamp, 'd/m/Y'),
            'opportunity' => [
                'id' => $opportunity->id,
                'name' => $opportunity->name,
                'owner' => $opportunity->ownerEntity->name,
                'avatarUrl' => $avatar ? $avatar->url : null,
                'headerUrl' => $header ? $header->url : null,
            ],
        ];
    }

    private static function agentInfo($owner)
    {
        $metadata = $owner->metadata;

        $agent = [
            'id' => $owner->id,
            'name' => self::emptyToNull($owner->name),
            'avatarUrl' => !empty($owner->avatar) ? $owner->avatar->transform('avatarSmall')->url : null,
        ];

        foreach (self::AGENT_METADATA as $key) {
            $agent[$key] = self::emptyToNull(isset($metadata[$key]) ? $metadata[$key] : null);

            if ($key === 'genero') {
                $agent['dataDeNascimento'] = self::formatBirthDate(
                    isset($metadata['dataDeNascimento']) ? $metadata['dataDeNascimento'] : null
                );
            }
        }

        return $agent;
    }

    /**
     * Seções e campos de uma inscrição. Cada campo `section` abre uma nova seção, como o
     * <hr><u>título</u> do PDF; os campos anteriores à primeira seção ficam em uma sem título.
     */
    private static function buildSections(Registration $reg)
    {
        $sections = [];
        $current = ['title' => null, 'fields' => []];

        foreach (Pdf::showAllFieldAndFile($reg) as $field) {
            if (!self::isVisibleForCategory($reg, $field) || !Pdf::getDependenciesField($reg, $field)) {
                continue;
            }

            if ($field['fieldType'] === 'section') {
                if ($current['title'] !== null || !empty($current['fields'])) {
                    $sections[] = $current;
                }
                $current = ['title' => $field['title'], 'fields' => []];
                continue;
            }

            $current['fields'][] = self::buildField($reg, $field);
        }

        if ($current['title'] !== null || !empty($current['fields'])) {
            $sections[] = $current;
        }

        return $sections;
    }

    private static function isVisibleForCategory(Registration $reg, array $field)
    {
        return empty($field['categories']) || in_array($reg->category, $field['categories']);
    }

    private static function buildField(Registration $reg, array $field)
    {
        $item = [
            'id' => $field['id'],
            'title' => $field['title'],
            'description' => self::emptyToNull($field['description']),
            'type' => $field['fieldType'],
        ];

        if ($field['fieldType'] === 'file') {
            $item['value'] = self::fileValue($field);
            return $item;
        }

        $metas = Pdf::getValueField($field['id'], $reg);
        $raw = empty($metas) ? null : $metas[0]->value;

        return array_merge($item, self::formatValue($field, $raw));
    }

    /**
     * @return array com `value` e, nos campos mascarados, `rawValue`
     */
    private static function formatValue(array $field, $raw)
    {
        $type = $field['fieldType'];
        $entityField = isset($field['config']['entityField']) ? $field['config']['entityField'] : '';

        if ($type === 'checkbox') {
            return ['value' => $raw ? $field['description'] : null, 'checked' => (bool) $raw];
        }

        if ($raw === null || $raw === '') {
            return ['value' => null];
        }

        switch ($type) {
            case 'cpf':
            case 'cnpj':
                $digits = preg_replace('/\D/', '', $raw);
                $mask = $type === 'cpf' ? '###.###.###-##' : '##.###.###/####-##';
                return ['value' => Pdf::mask($digits, $mask), 'rawValue' => $digits];

            case 'date':
                $time = strtotime($raw);
                return ['value' => $time ? date('d/m/Y', $time) : $raw];

            case 'persons':
                return ['value' => self::decodeList($raw, 'name', null)];

            case 'links':
                return ['value' => self::decodeList($raw, 'value', 'title')];

            case 'checkboxes':
                $items = json_decode($raw, true);
                if (!is_array($items)) {
                    $items = explode(',', $raw);
                }
                $items = array_values(array_filter(array_map('trim', $items), 'strlen'));
                return ['value' => $items ?: null];

            case 'space-field':
                return ['value' => self::capture(function () use ($entityField, $raw) {
                    Pdf::showSpaceField($entityField, $raw);
                })];

            case 'agent-collective-field':
                return ['value' => self::capture(function () use ($entityField, $raw) {
                    Pdf::showAgentCollectiveField($entityField, $raw);
                })];

            case 'agent-owner-field':
                if ($entityField === '@location' && !is_array(json_decode($raw, true))) {
                    return ['value' => null];
                }
                return ['value' => self::capture(function () use ($field, $raw) {
                    Pdf::showAgenteOwnerField($field, $raw);
                })];
        }

        return ['value' => $raw];
    }

    /**
     * Converte o JSON de `persons` e `links` em lista. Com $titleKey devolve
     * [{title, url}], senão devolve só os valores de $valueKey.
     */
    private static function decodeList($raw, $valueKey, $titleKey)
    {
        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            // valor simples (ex.: "" ou texto solto) em vez de lista
            $decoded = [[$valueKey => is_string($decoded) ? $decoded : $raw]];
        }

        $list = [];
        foreach ($decoded as $entry) {
            if (!is_array($entry) || !isset($entry[$valueKey]) || $entry[$valueKey] === '') {
                continue;
            }

            if ($titleKey === null) {
                $list[] = $entry[$valueKey];
            } else {
                $list[] = [
                    'title' => self::emptyToNull(isset($entry[$titleKey]) ? $entry[$titleKey] : null),
                    'url' => $entry[$valueKey],
                ];
            }
        }

        return $list ?: null;
    }

    private static function fileValue(array $field)
    {
        if (empty($field['config'])) {
            return null;
        }

        $app = App::i();
        $controllerId = $app->getControllerIdByEntity('MapasCulturais\Entities\File');

        $files = [];
        foreach ($field['config'] as $file) {
            $files[] = [
                'id' => $file['id'],
                'name' => $file['name'],
                'url' => $app->createUrl($controllerId, 'privateFile', [$file['id']]),
            ];
        }

        return $files;
    }

    /**
     * Os helpers `show*` de Entities/Pdf.php fazem echo de HTML; aqui o resultado
     * é capturado e reduzido a texto.
     */
    private static function capture(callable $show)
    {
        ob_start();
        try {
            $show();
        } catch (\Exception $e) {
            ob_end_clean();
            return null;
        }
        $output = ob_get_clean();

        $text = trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($output))));

        return $text === '' || $text === 'Não informado' ? null : $text;
    }

    private static function formatBirthDate($value)
    {
        $value = str_replace('"', '', (string) $value);
        $time = $value !== '' ? strtotime($value) : false;

        return $time ? date('d/m/Y', $time) : null;
    }

    private static function formatDate($date, $format)
    {
        return $date instanceof \DateTime ? $date->format($format) : null;
    }

    private static function emptyToNull($value)
    {
        return $value === null || $value === '' ? null : $value;
    }
}
