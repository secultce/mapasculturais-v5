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

        // Registra nos metadados que houve o sorteio para essa categoria
        try {
            $opportunity = $registrationsRanked[0]->opportunity;
            $category = $registrationsRanked[0]->category;
            $drawedCategories = $opportunity->drawedRegistrationsCategories;
            $drawedCategories[] = $category;
            $opportunity->drawedRegistrationsCategories = $drawedCategories;
            $opportunity->save(true);
        } catch (\Exception $e) {
            $app->em->rollback();
            throw $e;
        }
        $app->em->commit();

        return true;
    }

    /**
     * @param Opportunity $opportunity
     * @param string $category
     * @return \MapasCulturais\Entities\RegistrationsRanking[]
     */
    public function findRanking(Opportunity $opportunity, string $category): array
    {
        return $this->findBy([
            'opportunity' => $opportunity,
            'category' => $category,
        ], ['rank' => 'asc']);
    }
}
