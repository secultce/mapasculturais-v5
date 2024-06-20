<?php

namespace MapasCulturais\Controllers;

use MapasCulturais\App;
use MapasCulturais\Entities\{Opportunity as OpportunityEntity,
    Registration as RegistrationEntity,
    RegistrationsRanking};
use WideImage\Exception\Exception;

class RegistrationsDraw extends \MapasCulturais\Controller
{
    public function GET_draw(): void
    {
        /**
         * @var OpportunityEntity $opportunity
         * @var RegistrationEntity[] $registrations
         */

        $app = App::i();
        $opportunity = $app->repo('Opportunity')->find($this->data['id']);
        $ranking = $this->drawRanking($opportunity, $this->data['category']);

        if(empty($ranking))
            $this->json(['message' => 'Not exists approved registrations in category'], 404);

        $this->json($ranking, 201);
    }

    /**
     * @throws \Exception
     */
    private function drawRanking(OpportunityEntity $opportunity, string $category = ''): array
    {
        $app = App::i();

        $registrations = $app->repo('Registration')->findBy([
            'opportunity' => $opportunity,
            'status' => RegistrationEntity::STATUS_APPROVED,
            'category' => $category,
        ]);

        $randomMax = count($registrations) * 100;
        $randomizedArray = [];
        foreach ($registrations as $registration) {
            do {
                $random = rand(1,$randomMax);
            } while (array_key_exists($random, $randomizedArray));

            $randomizedArray[$random] = $registration;
        }
        ksort($randomizedArray);

        $ranking = [];
        $i = 1;
        foreach ($randomizedArray as $registration) {
            $ranking[] = new RegistrationsRanking($registration, $registration->opportunity, $i, $category);
            $i++;
        }

        $saved = $app->repo('RegistrationsRanking')->saveRanking($ranking);
        if(!$saved)
            throw new Exception('Not saved');

        return $ranking;
    }
}
