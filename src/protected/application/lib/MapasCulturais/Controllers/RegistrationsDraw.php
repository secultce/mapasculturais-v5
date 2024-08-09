<?php

/** @noinspection PhpDynamicAsStaticMethodCallInspection */

namespace MapasCulturais\Controllers;

use MapasCulturais\App;
use MapasCulturais\Exceptions\PermissionDenied;
use MapasCulturais\Exceptions\WorkflowRequest;
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
            throw $e;
            exit();
            $this->json(['message' => 'An unexpected error occured'], 500);
            return;
        }

        $this->json($draw, 201);
    }

    public function GET_downloadcsv(): void
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
        $footerLinesIndexes = [];
        foreach ($draws as $draw) {
            $output[] = [
                '<style bgcolor="#555555" color="#ffffff">Inscrição</style>', '',
                '<style bgcolor="#555555" color="#ffffff">Oportunidade</style>', '',
                '<style bgcolor="#555555" color="#ffffff">Posição</style>',
                '<style bgcolor="#555555" color="#ffffff">Categoria</style>'
            ];
            foreach ($draw->drawRegistrations->toArray() as $rankingPosition) {
                $outputLine[0] = $rankingPosition->registration->number;
                $outputLine[1] = $rankingPosition->registration->singleUrl;
                $outputLine[2] = $draw->opportunity->name;
                $outputLine[3] = $draw->opportunity->singleUrl;
                $outputLine[4] = $rankingPosition->rank;
                $outputLine[5] = $draw->category;

                $output[] = $outputLine;
            }

            $footerLine = [
                '<style bgcolor="#dddddd" color="#222222">' .
                'Sorteio realizado em ' .
                $draw->createTimestamp->format('d/m/Y \à\s h:i:s') .
                ' por ' . $draw->user->profile->name .
                '</style>'
            ];
            $output[] = $footerLine;
            $footerLinesIndexes[] = count($output);

            $output[] = [];
        }

        try {
            $spreadsheet = SimpleXLSXGen::fromArray($output)
                ->mergeCells('A1:B1')
                ->mergeCells('C1:D1');
            foreach ($footerLinesIndexes as $index) {
                $spreadsheet->mergeCells('A' . ($index + 2) . ':B' . ($index + 2))
                    ->mergeCells('C' . ($index + 2) . ':D' . ($index + 2))
                    ->mergeCells("A{$index}:F{$index}");
            }

            $spreadsheet->downloadAs('sorteios.xlsx');
        } catch (\Exception $e) {
            $this->json(['message' => 'An unexpected error occured'], 500);
            return;
        }
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
     * Essa função recebe uma opporunidade e uma categoria, lista todas as inscrições referentes, as reordena
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
        // Caso não existam inscrições que obdeçam aos critérios, para a execução e retorna 'null'
        if (empty($registrations)) {
            return null;
        }

        // Nesse bloco cada inscrição é atribuída a uma chave de um array. Cheve essa que é gerada aleatoriamente.
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
     * Endpoint que devolve um pdf com os dados do sorteio
     *
     * @throws MpdfException
     */
    public function GET_pdf(): void
    {
        $this->requireAuthentication();
        $app = App::i();
        $draw = $app->repo('RegistrationsRanking')->findBy(['opportunity' => $this->data['id']]);
        $opp = $app->repo('Opportunity')->find($this->data['id']);

        $pdf = new PDF([
            'tempDir' => dirname(__DIR__) . '/tmp',
            'mode' => 'utf-8',
            'default_font' => 'dejavusans',
            'format' => 'A4',
            'pagenumPrefix' => 'Pagina ',
            'pagenumSuffix' => '  ',
            'nbpgPrefix' => ' de ',
            'nbpgSuffix' => ''
        ]);

        //INSTANCIA DO TIPO ARRAY OBJETO
        $app->view->regObject = new \ArrayObject();
        $app->view->regObject['draw'] = $draw;
        $app->view->regObject['opp'] = $opp;

        //Add estilo
        $stylesheet = file_get_contents(MODULES_PATH . 'RegistrationsDraw/assets/css/prize-draw.css');
        $pdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);

        //Configuração para o footer
        $footerPage = file_get_contents(THEMES_PATH . 'BaseV1/views/pdf/footer-pdf.php');
        $footerDocumentPage = file_get_contents(THEMES_PATH . 'BaseV1/views/pdf/footer-document.php');
        $pdf->SetHTMLFooter($footerPage);
        $pdf->SetHTMLFooter($footerPage, 'E');
        $pdf->writingHTMLfooter = true;
        //Gerando o pdf
        $pdf->SetTitle('Secult/CE - Relatório de sorteio');
        $content = $app->view->fetch('draw/pdf');
        $pdf->WriteHTML($content);
        $pdf->SetHTMLFooter($footerPage . $footerDocumentPage);
        $pdf->Output();
        exit;
    }
}
