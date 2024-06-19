<?php

namespace MapasCulturais\Repositories;

use MapasCulturais\Entities\Opportunity;
use MapasCulturais\Repository;

class RegistrationsRanking extends Repository
{
    public static function saveRanking(array $registrationsOrdered): bool
    {
        // @todo: implements save ranking

        return false;
    }

    /**
     * @param Opportunity $opportunity
     * @param string $category
     * @return \MapasCulturais\Entities\RegistrationsRanking[]
     */
    public static function getRanking(Opportunity $opportunity, string $category): array
    {
        return self::findBy([
            'opportunity' => $opportunity,
            'category' => $category,
        ], ['rank']);
    }
}
