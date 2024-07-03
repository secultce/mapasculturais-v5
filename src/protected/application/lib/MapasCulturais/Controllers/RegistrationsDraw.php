<?php /** @noinspection PhpDynamicAsStaticMethodCallInspection */

namespace MapasCulturais\Controllers;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use MapasCulturais\App;
use MapasCulturais\Entities\{Opportunity as OpportunityEntity,
    Registration as RegistrationEntity,
    RegistrationsRanking};
use Shuchkin\SimpleXLSXGen;

class RegistrationsDraw extends \MapasCulturais\Controller
{
    public function POST_draw(): void
    {
        /**
         * @var OpportunityEntity $opportunity
         */

        $app = App::i();

        $this->requireAuthentication();

        $opportunity = $app->repo('Opportunity')->find($this->data['id']);
        $category = $this->data['category'];
        if (in_array($category, $opportunity->drawedRegistrationsCategories))
            $this->json(['message' => 'Ranking previously generated'], 400);

        try {
            $ranking = $this->drawRanking($opportunity, $category);
        } catch (\Exception $e) {
            $this->json(['message' => $e->getMessage()], 500);
        }

        if (is_null($ranking))
            $this->json(['message' => 'Not exists approved registrations in category'], 400);

        $this->json($ranking, 201);
    }

    public function GET_downloadcsv(): void
    {
        $app = App::i();

        $criteria = ['opportunity' => $this->data['id']];
        if(isset($criteria['category']))
            $criteria['category'] = $this->data['category'];

        $rankingList = $app->repo('RegistrationsRanking')->findBy($criteria, ['category' => 'asc','rank' => 'asc']);

        $output = [['Inscrição','','Oportunidade','','Posição', 'Categoria', 'Data do sorteio','Responsável pelo sorteio','']];
        foreach ($rankingList as $rankingPosition) {
            $outputLine[0] = $rankingPosition->registration->number;
            $outputLine[1] = $rankingPosition->registration->singleUrl;
            $outputLine[2] = $rankingPosition->opportunity->name;
            $outputLine[3] = $rankingPosition->opportunity->singleUrl;
            $outputLine[4] = $rankingPosition->rank;
            $outputLine[5] = $rankingPosition->category;
            $outputLine[6] = $rankingPosition->createTimestamp;
            $outputLine[7] = $rankingPosition->owner->name;
            $outputLine[8] = $rankingPosition->owner->singleUrl;

            $output[] = $outputLine;
        }

        SimpleXLSXGen::fromArray($output)
            ->mergeCells('A1:B1')
            ->mergeCells('C1:D1')
            ->mergeCells('H1:I1')
            ->downloadAs('planilha.xlsx');
    }

    public function POST_publish(): void
    {
        $app = App::i();
        $opportunity = $app->repo('Opportunity')->find($this->data['id']);
        $opportunity->isPublishedDraw = true;
        $opportunity->save();

        $this->json(['message' => 'Published'], 204);
    }

    /**
     * @throws \Exception
     */
    private function drawRanking(OpportunityEntity $opportunity, string $category = ''): ?array
    {
        $app = App::i();

        $registrations = $app->repo('Registration')->findBy([
            'opportunity' => $opportunity,
            'status' => RegistrationEntity::STATUS_APPROVED,
            'category' => $category,
        ]);
        // Caso não existam inscrições que obdeçam aos critérios, para a execução e retorna 'null'
        if(empty($registrations))
            return null;

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
