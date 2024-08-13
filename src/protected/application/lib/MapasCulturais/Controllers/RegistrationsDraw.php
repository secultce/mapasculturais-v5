<?php

/** @noinspection PhpDynamicAsStaticMethodCallInspection */

namespace MapasCulturais\Controllers;

use MapasCulturais\App;
use MapasCulturais\Exceptions\PermissionDenied;
use MapasCulturais\Exceptions\WorkflowRequest;
use Mpdf\HTMLParserMode;
use Mpdf\MpdfException;
use MapasCulturais\Entities\{Draw, Opportunity as OpportunityEntity, Registration as RegistrationEntity};
use Shuchkin\SimpleXLSXGen;
use Mpdf\Mpdf as PDF;

class RegistrationsDraw extends \MapasCulturais\Controller
{
    public function POST_draw(): void
    {
        /**
         * @var OpportunityEntity $opportunity
         */

        $app = App::i();

        $this->requireAuthentication();

        $opportunity = $app->repo(OpportunityEntity::class)->find($this->data['id']);
        $category = $this->data['category'];

        $reorderedList = $this->randomSortRegistrations($opportunity, $category);

        if ($reorderedList === null) {
            $this->json(['message' => 'Not exists approved registrations in category'], 400);
            return;
        }

        try {
            $draw = \MapasCulturais\Factories\Draw::createFromRegistrations($reorderedList);
            $draw->save(true);
        } catch (\Exception $e) {
            $this->json(['message' => 'An unexpected error occurred'], 500);
            return;
        }

        $this->json($draw, 201);
    }

    public function GET_downloadxlsx(): void
    {
        $app = App::i();

        $criteria = ['opportunity' => $this->data['id']];
        if (isset($this->data['category']) && !empty($this->data['category'])) {
            $criteria['category'] = $this->data['category'];
        }

        /** @var Draw[] $draws */
        $draws = $app->repo(Draw::class)->findBy($criteria, ['category' => 'asc', 'createTimestamp' => 'desc']);

        // Em caso de usuário não autorizado, retornar 403
        if (!$draws[0]->opportunity->isUserAdmin($app->user)) {
            $this->json(['message' => 'Not authorized'], 403);
            return;
        }

        $output = [];
        $output[] = [
            '<style bgcolor="#555555" color="#ffffff">Inscrição</style>', '',
            '<style bgcolor="#555555" color="#ffffff">Oportunidade</style>', '',
            '<style bgcolor="#555555" color="#ffffff">Posição</style>',
            '<style bgcolor="#555555" color="#ffffff">Categoria</style>',
            '<style bgcolor="#555555" color="#ffffff">Data do sorteio</style>',
            '<style bgcolor="#555555" color="#ffffff">Reponsável pelo sorteio</style>', '',
        ];
        foreach ($draws as $draw) {
            foreach ($draw->drawRegistrations->toArray() as $rankingPosition) {
                $outputLine[0] = $rankingPosition->registration->number;
                $outputLine[1] = $rankingPosition->registration->singleUrl;
                $outputLine[2] = $draw->opportunity->name;
                $outputLine[3] = $draw->opportunity->singleUrl;
                $outputLine[4] = $rankingPosition->rank;
                $outputLine[5] = $draw->category;
                $outputLine[6] = $draw->createTimestamp->format('d/m/Y \à\s h:i:s');
                $outputLine[7] = $draw->user->profile->name;
                $outputLine[8] = $draw->user->profile->singleUrl;

                $output[] = $outputLine;
            }
        }

        try {
            $spreadsheet = SimpleXLSXGen::fromArray($output)
                ->mergeCells('A1:B1')
                ->mergeCells('C1:D1')
                ->mergeCells('H1:I1');

            $spreadsheet->downloadAs('sorteios.xlsx');
        } catch (\Exception $e) {
            $this->json(['message' => 'An unexpected error occurred'], 500);
            return;
        }
    }

    /**
     * Endpoint que devolve um pdf com os dados do sorteio
     *
     * @throws MpdfException
     */
    public function GET_downloadpdf(): void
    {
        $app = App::i();

        $criteria = ['opportunity' => $this->data['id']];
        if (isset($this->data['category']) && !empty($this->data['category'])) {
            $criteria['category'] = $this->data['category'];
        }

        /** @var Draw[] $draws */
        $draws = $app->repo(Draw::class)->findBy($criteria, ['category' => 'asc', 'createTimestamp' => 'desc']);

        // Em caso de usuário não autorizado, retornar 403
        if (!$draws[0]->opportunity->isUserAdmin($app->user)) {
            $this->json(['message' => 'Not authorized'], 403);
            return;
        }

        $pdf = new PDF([
            'tempDir' => '/tmp',
            'mode' => 'utf-8',
            'default_font' => 'dejavusans',
            'format' => 'A4',
            'pagenumPrefix' => 'Página ',
            'pagenumSuffix' => '  ',
            'nbpgPrefix' => ' de ',
        ]);
        $pdf->SetTitle('Secult/CE - Relatório de sorteio');

        $pdf->WriteHTML(
            "@page { footer: html_RodapeTimbrado; margin-footer: 0cm; margin-bottom: 3cm; }",
            HTMLParserMode::HEADER_CSS
        );

        $footerPageContent = $this->parseToHTML(THEMES_PATH . 'BaseV1/views/pdf/footer-pdf.php', [], '');

        $content = $this->parseToHTML('draw/pdf.php', [
            'draws' => $draws,
            'opportunity' => $draws[0]->opportunity,
        ]);
        $pdf->WriteHTML($footerPageContent . $content);

        $pdf->OutputHttpDownload('sorteios.pdf');
    }

    /**
     * @throws WorkflowRequest
     * @throws PermissionDenied
     */
    public function POST_publish(): void
    {
        $app = App::i();
        $opportunity = $app->repo(OpportunityEntity::class)->find($this->data['id']);
        $opportunity->isPublishedDraw = true;
        $opportunity->save();

        $this->json(['message' => 'Published'], 204);
    }

    /**
     * Essa função recebe uma oportunidade e uma categoria, lista todas as inscrições referentes, as reordena
     * numa nova lista e retorna essa nova lista.
     */
    private function randomSortRegistrations(OpportunityEntity $opportunity, string $category = ''): ?array
    {
        $app = App::i();

        $registrations = $app->repo(RegistrationEntity::class)->findBy([
            'opportunity' => $opportunity,
            'status' => RegistrationEntity::STATUS_APPROVED,
            'category' => $category,
        ]);
        // Caso não existam inscrições que obedeçam aos critérios, para a execução e retorna 'null'
        if (empty($registrations)) {
            return null;
        }

        // Nesse bloco cada inscrição é atribuída a uma chave de um array. Chave essa gerada aleatoriamente.
        $randomMax = count($registrations) * 10;
        $randomizedArray = [];
        foreach ($registrations as $registration) {
            do {
                $random = rand(1, $randomMax);
            } while (array_key_exists($random, $randomizedArray));

            $randomizedArray[$random] = $registration;
        }
        // Após atribuir uma chave a cada inscrição, ordena-se o array pela chave em ordem crescente
        ksort($randomizedArray);

        $reorderedList = [];
        foreach ($randomizedArray as $registration) {
            $reorderedList[] = $registration;
        }

        return $reorderedList;
    }

    /**
     * @param string $template
     * @param array<string, mixed> $data
     * @param false|string $basePath
     * @return string
     */
    private function parseToHTML(string $template, array $data = [], $basePath = false): string
    {
        if ($basePath === false) {
            $basePath = MODULES_PATH . 'RegistrationsDraw/views/';
        }

        extract($data);
        ob_start();
        require $basePath . $template;

        return ob_get_clean();
    }
}
