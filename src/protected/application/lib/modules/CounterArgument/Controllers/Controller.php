<?php

namespace CounterArgument\Controllers;

use MapasCulturais\App;
use MapasCulturais\Exceptions\PermissionDenied;
use MapasCulturais\Services\CounterArgumentService;
use MapasCulturais\Services\SentryService;

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
        $data = $this->getPostData();

        try {
            $this->counterArgumentService->saveResponse($data);
            $this->json(['message' => 'Sua resposta para a contrarrazão foi salva com sucesso.']);
        } catch (\Throwable $th) {
            SentryService::captureExceptions($th);
        }
    }

    private function validateRegistrationOwner($registration)
    {
        if (!$registration->owner->canUser('@control')) throw new PermissionDenied(App::i()->getUser(), $registration, 'sendCounterArgument');
    }

    private function validatePeriod($opportunity, $message)
    {
        if (!$this->counterArgumentService->isCounterArgumentPeriod($opportunity)) {
            $this->json(['message' => $message], 403);
            return;
        }
    }
}
