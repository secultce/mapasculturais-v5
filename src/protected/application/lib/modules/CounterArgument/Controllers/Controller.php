<?php

namespace CounterArgument\Controllers;

use MapasCulturais\App;
use MapasCulturais\Entities\CounterArgument;
use MapasCulturais\Exceptions\PermissionDenied;
use MapasCulturais\Services\CounterArgumentService;
use MapasCulturais\Services\SentryService;
use MapasCulturais\entities\CounterArgument as EntityCounterArgument;
use MapasCulturais\Utils;
use Mpdf\Mpdf;
use Mpdf\HTMLParserMode;

class Controller extends \MapasCulturais\Controller
{
    private $counterArgumentService;

    public function __construct()
    {
        $this->counterArgumentService = new CounterArgumentService();
    }

    public function POST_send()
    {
        $this->requireAuthentication();

        $data = $this->getPostData();
        $registration = App::i()->repo('Registration')->find($data['registration']);

        $this->validateRegistrationOwner($registration);
        $this->validatePeriod($registration->opportunity, 'O período para envio de contrarrazões está encerrado.');

        try {
            $this->counterArgumentService->send($data['text'], $registration);
        } catch (\Throwable $th) {
            SentryService::captureExceptions($th);
            return;
        }

        $this->json(['message' => 'Contrarrazão enviada com sucesso. Aguarde a resposta.'], 201);
    }

    public function POST_update()
    {
        $this->requireAuthentication();

        $data = $this->getPostData();
        $counterArgument = App::i()->repo('CounterArgument')->find($data['id']);

        $this->validateRegistrationOwner($counterArgument->registration);
        $this->validatePeriod($counterArgument->registration->opportunity, 'O período para editar a contrarrazão está encerrado.');

        try {
            $this->counterArgumentService->update($data['text'], $counterArgument);
        } catch (\Throwable $th) {
            SentryService::captureExceptions($th);
            return;
        }

        $this->json(['message' => 'Contrarrazão atualizada com sucesso. Aguarde a resposta.']);
    }

    public function POST_removeFile()
    {
        $this->requireAuthentication();

        $data = $this->getPostData();
        $counterArgumentFile = App::i()->repo('CounterArgumentFile')->find($data['fileId']);

        $this->validateRegistrationOwner($counterArgumentFile->owner->registration);
        $this->validatePeriod($counterArgumentFile->owner->registration->opportunity, 'O período para remover arquivos da contrarrazão está encerrado.');

        try {
            $this->counterArgumentService->removeFile($counterArgumentFile);
        } catch (\Throwable $th) {
            SentryService::captureExceptions($th);
            return;
        }

        $this->json(['message' => 'O arquivo foi removido da contrarrazão']);
    }

    public function POST_respond()
    {
        $this->requireAuthentication();

        $data = $this->getPostData();
        $counterArgument = App::i()->repo('CounterArgument')->find($data['counterArgumentId']);

        $this->verifyResponsePermission($counterArgument->registration);
        $this->validateResponsePeriod($counterArgument->registration->opportunity);
        $this->verifyResponseOwner($counterArgument->response);
        $this->verifyPublishedResponse($counterArgument->response);

        try {
            $this->counterArgumentService->saveResponse($data);
        } catch (\Throwable $th) {
            SentryService::captureExceptions($th);
            return;
        }

        $this->json(['message' => 'Sua resposta para a contrarrazão foi salva com sucesso.']);
    }

    public function POST_publishResponses()
    {
        $this->requireAuthentication();

        $data = $this->getPostData();
        $opportunity = App::i()->repo('Opportunity')->find($data['opportunityId']);
        $counterArguments = App::i()->repo('CounterArgument')->getAllByOpportunityId($opportunity->id);

        $this->verifyPublishPermission($opportunity);
        $this->verifyCounterArgumentsWithoutResponse($counterArguments);

        try {
            $this->counterArgumentService->publishResponses($counterArguments);
        } catch (\Throwable $th) {
            SentryService::captureExceptions($th);
            return;
        }

        $this->json(['message' => 'As respostas foram publicadas com sucesso.']);
    }

    public function GET_getStatuses()
    {
        $this->requireAuthentication();

        $statuses = CounterArgument::STATUSES;
        unset($statuses[CounterArgument::STATUS_WAITING]);

        $this->json(['statuses' => $statuses]);
    }

    public function GET_printCounterArgument(): void
    {
        $this->requireAuthentication();

        $counterArgument = App::i()->repo(EntityCounterArgument::class)->find($this->data['counterArgumentId']);

        $counterArgument->registration->checkPermission('@control');

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

        $content = App::i()->view->fetch('counter-argument/print-counter-argument');

        $stylesheet = file_get_contents(MODULES_PATH . 'CounterArgument/assets/counter-argument/css/print.css');

        $mpdf->WriteHTML($stylesheet, HTMLParserMode::HEADER_CSS);

        $mpdf->WriteHTML($content);
        $mpdf->WriteHTML(ob_get_clean());

        $this->addAttachmentsToCounterArgumentPDF($mpdf, $counterArgument->files);

        $mpdf->Output();
    }

    private function addAttachmentsToCounterArgumentPDF($mpdf, $files)
    {
        $mpdf->WriteHTML('@page { odd-header-name: none; odd-footer-name: none; }', \Mpdf\HTMLParserMode::HEADER_CSS);

        foreach ($files as $file) {

            if (is_array($file)) {
                $filePath = $file[0]->path;
                $fileName = $file[0]->name;
            } else {
                $filePath = $file->getPath();
                $fileName = $file->name;
            }

            if (!$filePath) {
                continue;
            }

            try {
                $pageCount = $mpdf->SetSourceFile($filePath);

                for ($i = 1; $i <= $pageCount; $i++) {
                    $templateId = $mpdf->ImportPage($i);
                    $size = $mpdf->GetTemplateSize($templateId);
                    $orientation = $size['width'] > $size['height'] ? 'L' : 'P';

                    $mpdf->AddPageByArray([
                        'orientation' => $orientation,
                        'newformat' => [$size['width'], $size['height']],
                    ]);
                    $mpdf->UseTemplate($templateId);
                }
            } catch (\Throwable $e) {
                error_log("Erro ao renderizar anexo: " . $filePath . " - " . $e->getMessage());

                $mpdf->AddPage();
                $mpdf->WriteHTML('<p style="color:red; text-align: center;">Erro ao renderizar anexo: ' . htmlspecialchars($fileName) . '</p>');
            }
        }
    }

    private function validateRegistrationOwner($registration)
    {
        if (!$registration->owner->canUser('@control')) {
            throw new PermissionDenied(App::i()->getUser(), $registration, 'sendCounterArgument');
        }
    }

    private function validatePeriod($opportunity, $message)
    {
        if (!$this->counterArgumentService->isCounterArgumentPeriod($opportunity)) {
            $this->json(['message' => $message], 403);
            return;
        }
    }

    private function verifyResponsePermission($registration)
    {
        if (!$registration->canUser('evaluate') && !$registration->opportunity->canUser('@control')) {
            throw new PermissionDenied(App::i()->getUser(), $registration, 'respondCounterArgument');
        }
    }

    private function validateResponsePeriod($opportunity)
    {
        if (!$this->counterArgumentService->isResponsePeriod($opportunity)) {
            $this->json(['message' => 'Fora do período de resposta. Aguarde o fim do período de envio das contrarrazões.'], 403);
            return;
        }
    }

    private function verifyResponseOwner($response)
    {
        if ($response && !$response->owner->canUser('@control')) {
            $this->json(['message' => "Essa contrarrazão já foi respondida por {$response->owner->name}"], 403);
            return;
        }
    }

    private function verifyPublishPermission($opportunity)
    {
        if (!$opportunity->canUser('@control')) {
            throw new PermissionDenied(App::i()->getUser(), $opportunity, 'publishCounterArgumentResponses');
        }
    }

    private function verifyCounterArgumentsWithoutResponse($counterArguments)
    {
        $counterArgumentsWithoutResponse = $this->counterArgumentService->getCounterArgumentsWithoutResponse($counterArguments);

        if ($counterArgumentsWithoutResponse) {
            $this->json(['message' => 'Existem contrarrazões sem resposta. Por favor, responda todas as contrarrazões antes de publicar as respostas.'], 403);
            return;
        }
    }

    private function verifyPublishedResponse($response)
    {
        if ($response && $response->published) {
            $this->json(['message' => 'Esta resposta já foi publicada e não pode mais ser editada.'], 403);
            return;
        }
    }
}
