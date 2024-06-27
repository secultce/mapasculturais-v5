<?php

namespace MapasCulturais\Controllers;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use MapasCulturais\App;
use MapasCulturais\Entities\{Opportunity as OpportunityEntity,
    Registration as RegistrationEntity,
    RegistrationsRanking};

class RegistrationsDraw extends \MapasCulturais\Controller
{
    public function POST_draw(): void
    {
        /**
         * @var OpportunityEntity $opportunity
         * @var RegistrationEntity[] $registrations
         */

        $app = App::i();
        try{
            $opportunity = $app->repo('Opportunity')->find($this->data['id']);
            $ranking = $this->drawRanking($opportunity, $this->data['category']);
        } catch (\Exception $e) {
            if($e->getMessage() === 'Ranking previously generated')
                $this->json(['message' => $e->getMessage()], 400);
            else
                $this->json(['message' => $e->getMessage()], 500);
        }

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

        $randomMax = count($registrations) * 10;
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
            $ranking[] = new RegistrationsRanking($registration, $registration->opportunity, $i, $category, $app->user->profile);
            $i++;
        }

        try {
            $app->repo('RegistrationsRanking')->saveRanking($ranking);
        } catch (UniqueConstraintViolationException $e) {
            throw new \Exception('Ranking previously generated');
        } catch (\Exception $e) {
            throw new \Exception('Unexpected exception');
        }

        return [
            'owner' => $app->user->profile,
            'createTimestamp' => $ranking[0]->createTimestamp,
            'ranking' => $ranking,
        ];
    }
}
