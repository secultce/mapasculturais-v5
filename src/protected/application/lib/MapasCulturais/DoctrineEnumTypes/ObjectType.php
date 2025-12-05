<?php

namespace MapasCulturais\DoctrineEnumTypes;

use MapasCulturais\DoctrineEnumType;

class ObjectType extends DoctrineEnumType
{
    public static function getTypeName(): string
    {
        return 'object_type';
    }

    protected static function getKeysValues(): array
    {
        return [
            'Agent' => 'MapasCulturais\Entities\Agent',
            'AgentFile' => 'MapasCulturais\Entities\AgentFile',
            'ChatMessage' => 'MapasCulturais\Entities\ChatMessage',
            'ChatThread' => 'MapasCulturais\Entities\ChatThread',
            'EvaluationMethodConfiguration' => 'MapasCulturais\Entities\EvaluationMethodConfiguration',
            'Event' => 'MapasCulturais\Entities\Event',
            'EventFile' => 'MapasCulturais\Entities\EventFile',
            'File' => 'MapasCulturais\Entities\File',
            'Notification' => 'MapasCulturais\Entities\Notification',
            'Opportunity' => 'MapasCulturais\Entities\Opportunity',
            'OpportunityFile' => 'MapasCulturais\Entities\OpportunityFile',
            'Project' => 'MapasCulturais\Entities\Project',
            'ProjectFile' => 'MapasCulturais\Entities\ProjectFile',
            'Registration' => 'MapasCulturais\Entities\Registration',
            'RegistrationEvaluation' => 'MapasCulturais\Entities\RegistrationEvaluation',
            'RegistrationFieldConfiguration' => 'MapasCulturais\Entities\RegistrationFieldConfiguration',
            'RegistrationFileConfiguration' => 'MapasCulturais\Entities\RegistrationFileConfiguration',
            'Request' => 'MapasCulturais\Entities\Request',
            'Seal' => 'MapasCulturais\Entities\Seal',
            'SealFile' => 'MapasCulturais\Entities\SealFile',
            'Space' => 'MapasCulturais\Entities\Space',
            'SpaceFile' => 'MapasCulturais\Entities\SpaceFile',
            'Subsite' => 'MapasCulturais\Entities\Subsite',
            'SubsiteFile' => 'MapasCulturais\Entities\SubsiteFile',
            'Opinion' => 'Diligence\Entities\Opinion',
            'Diligence' => 'Diligence\Entities\Diligence',
            'AnswerDiligence' => 'Diligence\Entities\AnswerDiligence',
            'Tado' => 'Diligence\Entities\Tado',

        ];
    }
}
