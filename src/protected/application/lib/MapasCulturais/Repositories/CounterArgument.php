<?php

namespace MapasCulturais\Repositories;

use MapasCulturais\App;
use MapasCulturais\Entities\CounterArgument as CounterArgumentEntity;

class CounterArgument extends \MapasCulturais\Repository
{
    public function getAllByAgentId($agentId): array
    {
        $qb = App::i()->em->createQueryBuilder();
        $qb->select('ca')
            ->from(CounterArgumentEntity::class, 'ca')
            ->innerJoin('ca.registration', 'r')
            ->where('r.owner = :agentId')
            ->setParameter('agentId', $agentId);

        return $qb->getQuery()->getResult();
    }
}
