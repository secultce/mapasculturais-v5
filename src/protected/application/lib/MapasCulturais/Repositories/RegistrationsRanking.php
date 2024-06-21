<?php

namespace MapasCulturais\Repositories;

use MapasCulturais\App;
use MapasCulturais\Entities\Opportunity;
use MapasCulturais\Repository;

class RegistrationsRanking extends Repository
{

    /**
     * @param \MapasCulturais\Entities\RegistrationsRanking[] $registrationsRanked
     * @return bool
     * @throws \MapasCulturais\Exceptions\WorkflowRequest
     * @throws \MapasCulturais\Exceptions\PermissionDenied
     */
    public static function saveRanking(array $registrationsRanked): bool
    {
        $app = App::i();
        $app->em->beginTransaction();
        foreach ($registrationsRanked as $rankingPosition) {
            try {
                $rankingPosition->save(true);
            } catch (\Exception $e) {
                $app->em->rollback();
                throw $e;
            }
        }
        // @todo: Implementar registro de que a categoria já foi sorteada
        $app->em->commit();

        return true;
    }

    /**
     * @param Opportunity $opportunity
     * @param string $category
     * @return \MapasCulturais\Entities\RegistrationsRanking[]
     */
    public static function findRanking(Opportunity $opportunity, string $category): array
    {
        return self::findBy([
            'opportunity' => $opportunity,
            'category' => $category,
        ], ['rank']);
    }
}
