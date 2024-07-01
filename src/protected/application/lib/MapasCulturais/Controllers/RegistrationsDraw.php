<?php

namespace MapasCulturais\Controllers;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use MapasCulturais\App;
use MapasCulturais\Entities\{Opportunity as OpportunityEntity,
    Registration as RegistrationEntity,
    RegistrationsRanking};
use Shuchkin\SimpleXLSXGen;
use \Mpdf\Mpdf as PDF;

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

    public function GET_pdf() {
        // dump($this->data);
        $app = App::i();
        $draw = $app->repo('RegistrationsRanking')->findBy(['opportunity' => $this->data['id']]);
        $opp = $app->repo('Opportunity')->find($this->data['id']);
        // dump($opp); die;
        $pdf = new PDF( [
            'tempDir' => dirname(__DIR__) . '/vendor/mpdf/mpdf/tmp', 'mode' =>
            'utf-8', 'format' => 'A4',
            'pagenumPrefix' => 'Página ',
            'pagenumSuffix' => '  ',
            'nbpgPrefix' => ' de ',
            'nbpgSuffix' => ''
        ]);

        //INSTANCIA DO TIPO ARRAY OBJETO
        $app->view->regObject = new \ArrayObject;
        $app->view->regObject['draw'] = $draw;
        $app->view->regObject['opp'] = $opp;

        ob_start();  
        $content = $app->view->fetch('draw/pdf');
        $pdf->WriteHTML($content);
        $pdf->Output();
        exit;
        // $html = '<bookmark content="Start of the Document" /><div>Section 1 text</div>';

        // $mpdf = new \Mpdf\Mpdf();
        // $mpdf->WriteHTML($html);
        // $mpdf->Output();
        // exit;
// //Buscando o tado gerado
// $td = new RepoDiligence();
// $tado = $td->getTado($reg);

// //INSTANCIA DO TIPO ARRAY OBJETO
// $app->view->regObject = new \ArrayObject;
// $app->view->regObject['reg'] = $reg;
// $app->view->regObject['tado'] = $tado;

// $mpdf = new \Mpdf\Mpdf();
// ob_start();
// $content = $app->view->fetch('tado/html-gerar');
// $footerPage = $app->view->fetch('tado/footer-pdf');
// $mpdf->SetTitle('Secult/CE - Relatório TADO');
// $stylesheet = file_get_contents(MODULES_PATH . 'Diligence/assets/css/diligence/multi.css');
// // Adicione o CSS ao mPDF
// $mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);

// $mpdf->WriteHTML(ob_get_clean());
// $mpdf->WriteHTML($content);
// $mpdf->SetHTMLFooter($footerPage);
// $pdf = $mpdf->Output('Tado.pdf', \Mpdf\Output\Destination::DOWNLOAD);
    }
}
