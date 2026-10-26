<?php

namespace PDFReport\Controllers;

use MapasCulturais\App;
use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;
use PDFReport\Entities\Pdf as EntitiesPdf;
use PDFReport\Services\RegistrationJson;

class Pdf extends \MapasCulturais\Controller
{
    const LIST_SUBSCRIBED =  [
        'id' => 1,
        'title' => 'Relação de Inscritos',
        'enabled' => true,
    ];
    const LIST_PRELIMINARY = [
        'id' => 2,
        'title' => 'Resultados preliminares',
        'enabled' => true,
    ];
    const LIST_DEFINITIVE = [
        'id' => 3,
        'title' => 'Resultados definitivos',
        'enabled' => true,
    ];
    const LIST_CONTACTS = [
        'id' => 4,
        'title' => 'Relação de contatos',
        'enabled' => false,
    ];

    public static function getReports($id = null)
    {
        $reports = [
            self::LIST_SUBSCRIBED,
            self::LIST_PRELIMINARY,
            self::LIST_DEFINITIVE,
            self::LIST_CONTACTS
        ];

        if ($id) {
            return $reports[$id];
        }

        return $reports;
    }

    public static function getReportsEnabled()
    {
        $reportsEnabled = [];
        foreach (self::getReports() as $report) {
            if ($report['enabled']) {
                $reportsEnabled[] = $report;
            }
        }

        return $reportsEnabled;
    }

    public function GET_gerarPdf()
    {
        $app = App::i();

        if ($app->user->is('guest')) {
            $app->auth->requireAuthentication();
        }

        $array = [
            'regs' => '',
            'title' => '',
            'template' => '',
            'appealEnabled' => null,
            'pluginConf' => [
                'tempDir' => '/tmp',
                'mode' => 'utf-8',
                'format' => 'A4',
                'pagenumPrefix' => 'Página ',
                'pagenumSuffix' => '  ',
                'nbpgPrefix' => ' de ',
                'nbpgSuffix' => '',
            ]
        ];

        if ($this->getData['selectRel'] == 0) EntitiesPdf::handleRedirect('Ops! Selecione uma opção', 401, $this->getData['idopportunityReport']);
        else if ($this->getData['selectRel'] == self::LIST_SUBSCRIBED['id']) $array = EntitiesPdf::listSubscribedHandle($array, $this->getData);
        else if ($this->getData['selectRel'] == self::LIST_PRELIMINARY['id']) $array = EntitiesPdf::listPreliminaryHandle($array, $this->getData);
        else if ($this->getData['selectRel'] == self::LIST_DEFINITIVE['id']) $array = EntitiesPdf::listDefinitiveHandle($app, $array, false, $this->getData);
        else if ($this->getData['selectRel'] == self::LIST_CONTACTS['id']) $array = EntitiesPdf::listContactsHandle($array, $this->getData);
        else $app->redirect($app->createUrl('oportunidade/' . $this->getData['idopportunityReport']), 401);

        $mpdf = new Mpdf($array['pluginConf']);

        $app->view->jsObject['subscribers'] = $array['regs']['regs'];
        $app->view->jsObject['opp'] = $array['regs']['opp'];
        $app->view->jsObject['appealEnabled'] = $array['appealEnabled'];
        $app->view->jsObject['title'] = $array['title'];

        $content = $app->view->fetch($array['template']);

        $footerPage = $app->view->fetch('pdf/footer-page-pdf');
        $footerDocumentPage = $app->view->fetch('pdf/footer-document-pdf');

        $mpdf->SetHTMLFooter($footerPage);
        $mpdf->SetHTMLFooter($footerPage, 'E');
        $mpdf->writingHTMLfooter = true;

        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetTitle('Mapas Culturais - Relatório');
        $stylesheet = file_get_contents(MODULES_PATH . 'PDFReport/assets/css/stylePdfReport.css');
        $mpdf->AddPage(
            '', // L - landscape, P - portrait 
            '',
            '',
            '',
            '',
            5, // margin_left
            5, // margin right
            10, // margin top
            20, // margin bottom
            0, // margin header
            3
        ); // margin footer
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->WriteHTML($content, 2);
        $mpdf->SetHTMLFooter($footerPage . $footerDocumentPage);
        $mpdf->Output('MapaCultural_Relatorio.pdf', 'I');
        exit;
    }

    public function GET_imprimir_inscricao()
    {
        $allPhases = false;
        $this->generatePdfRegistration($allPhases);
    }

    public function GET_imprimir_inscricao_todas_as_fases()
    {
        $allPhases = true;
        $this->generatePdfRegistration($allPhases);
    }

    /**
     * JSON formatado da inscrição com todas as fases, espelho do PDF
     * imprimir_inscricao_todas_as_fases.
     *
     * ?until_latest=1 não corta as fases na inscrição informada e devolve a última inscrição
     * ?summary=1 devolve as fases sem o conteúdo (sections)
     */
    public function GET_inscricao_json_todas_as_fases()
    {
        $app = App::i();

        if ($app->user->is('guest')) {
            $this->json(['error' => true, 'message' => 'Autenticação necessária'], 401);
        }

        $id = isset($this->data['id']) ? $this->data['id'] : null;
        $reg = is_numeric($id) ? $app->repo('Registration')->find((int) $id) : null;

        if (!$reg) {
            $this->json(['error' => true, 'message' => 'Inscrição não encontrada'], 404);
        }

        if (!$this->userCanAccessRegistration($reg)) {
            $this->json(['error' => true, 'message' => 'Você não tem permissão'], 403);
        }

        $this->json(RegistrationJson::buildAll($reg, !empty($this->getData['until_latest']), !empty($this->getData['summary'])));
    }

    private function userCanAccessRegistration($reg)
    {
        $app = App::i();

        if ($reg->owner->userId == $app->user->id) {
            return true;
        }

        foreach ($reg->opportunity->agentRelations as $relation) {
            if ($relation->agent->user->id == $app->user->id) {
                return true;
            }
        }

        return $reg->opportunity->owner->canUser('@control');
    }

    private function generatePdfRegistration($allPhases)
    {
        $app = App::i();

        // SOMENTE AUTENTICADO
        if ($app->user->is('guest')) {
            $app->auth->requireAuthentication();
        }

        $mpdf = new Mpdf([
            'tempDir' => '/tmp',
            'mode' => 'utf-8',
            'format' => 'A4',
            'pagenumPrefix' => 'Página ',
            'pagenumSuffix' => '  ',
            'nbpgPrefix' => ' de ',
            'nbpgSuffix' => '',
            'margin_top' => 45,
            'margin_bottom' => 30,
        ]);

        $reg = $app->repo('Registration')->find($this->data['id']);

        // SE O DONO DA INSCRIÇÃO NAO FOR O MESMO LOGADO, ENTÃO NÃO TEM PERMISSÃO DE ACESSAR.
        if ($reg->owner->userId != $app->user->id) {
            $userAdm = false;
            // Checkando para saber se o usuário está no grupo de adm
            foreach ($reg->opportunity->agentRelations as $agent) {
                if ($agent->agent->user->id == $app->user->id) {
                    $userAdm = true;
                }
            }
            // SE OS IDS FOREM DIFERENTE, VERIRICA SE ELE NAO É UM ADMIN PARA RETORNAR A PÁGINA ANTERIOR
            if (!$userAdm && !$reg->opportunity->owner->canUser('@control')) {
                $_SESSION['error'] = "Ops! Você não tem permissão";
                $app->redirect($app->request()->getReferer(), 403);
            }
        }

        $app->view->regObject = new \ArrayObject;
        $app->view->regObject['ins'] = $reg;
        $app->view->regObject['allPhases'] = $allPhases;

        $stylesheet = file_get_contents(MODULES_PATH . 'PDFReport/assets/css/stylePdfReport.css');
        $content = $app->view->fetch('pdf/my-registration');

        $mpdf->SetTitle('Mapas Culturais - Relatório');
        $mpdf->WriteHTML($stylesheet, HTMLParserMode::HEADER_CSS);
        $mpdf->WriteHTML($content);
        $mpdf->WriteHTML(ob_get_clean());
        $mpdf->Output();
    }
}
