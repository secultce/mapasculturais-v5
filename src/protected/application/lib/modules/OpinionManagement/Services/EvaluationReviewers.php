<?php

declare(strict_types=1);

namespace OpinionManagement\Services;

use MapasCulturais\Entities\EvaluationMethodConfigurationAgentRelation;
use MapasCulturais\Entities\Opportunity;

class EvaluationReviewers
{
    /** @return int[] */
    public static function activeUserIds(Opportunity $opportunity): array
    {
        $configuration = $opportunity->evaluationMethodConfiguration;
        if (!$configuration) {
            return [];
        }

        $userIds = [];
        foreach ($configuration->getAgentRelations(null, true) as $relation) {
            if ($relation->group !== 'group-admin' || $relation->status === EvaluationMethodConfigurationAgentRelation::STATUS_DISABLED) {
                continue;
            }

            $userId = $relation->agent->user->id ?? null;
            if ($userId) {
                $userIds[$userId] = $userId;
            }
        }

        return array_values($userIds);
    }
}
