<?php

namespace CounterArgument\Controllers;

use MapasCulturais\App;
use MapasCulturais\Entities\CounterArgument;
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
        $this->requireAuthentication();

        $data = $this->getPostData();
        $counterArgument = App::i()->repo('CounterArgument')->find($data['counterArgumentId']);

        $this->verifyResponsePermission($counterArgument->registration);
        $this->validateResponsePeriod($counterArgument->registration->opportunity);
        $this->verifyResponseOwner($counterArgument->response);

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

        $this->json(['statuses' => CounterArgument::STATUSES]);
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
        if (!$opportunity->canUser('@control')) throw new PermissionDenied(App::i()->getUser(), $opportunity, 'publishCounterArgumentResponses');
    }

    private function verifyCounterArgumentsWithoutResponse($counterArguments)
    {
        $counterArgumentsWithoutResponse = $this->counterArgumentService->getCounterArgumentsWithoutResponse($counterArguments);

        if ($counterArgumentsWithoutResponse) {
            $this->json(['message' => 'Existem contrarrazões sem resposta. Por favor, responda todas as contrarrazões antes de publicar as respostas.'], 403);
            return;
        }
    }
}
