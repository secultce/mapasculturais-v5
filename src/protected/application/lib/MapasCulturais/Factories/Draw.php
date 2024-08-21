<?php

namespace MapasCulturais\Factories;

use Doctrine\Common\Collections\ArrayCollection;
use MapasCulturais\Entities\DrawRegistrations;

class Draw
{
    /**
     * @param array $registrations
     * @return \MapasCulturais\Entities\Draw
     * @throws \MapasCulturais\Exceptions\PermissionDenied
     * @throws \MapasCulturais\Exceptions\WorkflowRequest
     * @throws \Exception
     */
    public static function createFromRegistrations(array $registrations): \MapasCulturais\Entities\Draw
    {
        $app = \MapasCulturais\App::getInstance();
        try {
            $draw = new \MapasCulturais\Entities\Draw();
            $draw->category = $registrations[0]->category;
            $draw->opportunity = $registrations[0]->opportunity;
            $draw->published = false;
            $draw->createTimestamp = new \DateTime();
            $draw->user = $app->user;
        } catch (\Exception $e) {
            throw new \Exception('Erro ao criar sorteio');
        }

        $registrations = array_map(static function ($registration, $index) use ($draw) {
            $drawRegistration = new DrawRegistrations();
            $drawRegistration->draw = $draw;
            $drawRegistration->rank = $index + 1;
            $drawRegistration->registration = $registration;
            return $drawRegistration;
        }, array_values($registrations), array_keys($registrations));

        $draw->drawRegistrations = $registrations;
        return $draw;
    }
}
