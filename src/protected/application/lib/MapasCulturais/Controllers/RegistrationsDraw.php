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
            //Evita de continuar o codigo em casos de não está logado
            $this->requireAuthentication();
            $opportunity = $app->repo('Opportunity')->find($this->data['id']);
            if(is_null($opportunity))
                $this->json(['message' => 'Opportunity not found'], 404);
            $ranking = $this->drawRanking($opportunity, $this->data['category']);
        } catch (\Exception $e) {
            if($e->getMessage() === 'Ranking previously generated')
                $this->json(['message' => $e->getMessage()], 400);
            else
                $this->json(['message' => $e->getMessage()], 500);
        }

        if(empty($ranking['ranking']))
            $this->json(['message' => 'Not exists approved registrations in category'], 404);

        $this->json($ranking, 201);
    }

    public function GET_downloadcsv(): void
    {
        $app = App::i();

        $criteria = ['opportunity' => $this->data['id']];
        if(isset($this->data['category']) && !empty($this->data['category']))
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

    /**
     * Metodo que devolve uma pdf com os dados do sorteio
     *
     * @return void
     */
    public function GET_pdf() {
        $this->requireAuthentication();
        $app = App::i();
        $draw = $app->repo('RegistrationsRanking')->findBy(['opportunity' => $this->data['id']]);
        $opp = $app->repo('Opportunity')->find($this->data['id']);
       
        $pdf = new PDF( [
            'tempDir' => dirname(__DIR__) . '/vendor/mpdf/mpdf/tmp', 
            'mode' => 'utf-8',
            'default_font' => 'dejavusans',
            'format' => 'A4',
            'pagenumPrefix' => 'Pagina ',
            'pagenumSuffix' => '  ',
            'nbpgPrefix' => ' de ',
            'nbpgSuffix' => ''
        ]);

        //INSTANCIA DO TIPO ARRAY OBJETO
        $app->view->regObject = new \ArrayObject;
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
