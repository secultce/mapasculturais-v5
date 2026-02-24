<?php

namespace MapasCulturais\Repositories;

use MapasCulturais\App;
use MapasCulturais\Entities\CounterArgument as CounterArgumentEntity;

class CounterArgument extends \MapasCulturais\Repository
{
    public function getAllByUserId(int $userId): array
    {
        $qb = App::i()->em->createQueryBuilder();
        $qb->select('ca')
            ->from(CounterArgumentEntity::class, 'ca')
            ->innerJoin('ca.registration', 'r')
            ->innerJoin('r.owner', 'a')
            ->where('a.user = :userId')
            ->setParameter('userId', $userId);

        return $qb->getQuery()->getResult();
    }

    public function getAllByOpportunityId($opportunityId): array
    {
        $qb = App::i()->em->createQueryBuilder();
        $qb->select('ca')
            ->from(CounterArgumentEntity::class, 'ca')
            ->innerJoin('ca.registration', 'r')
            ->where('r.opportunity = :opportunityId')
            ->setParameter('opportunityId', $opportunityId);

        return $qb->getQuery()->getResult();
    }
}
