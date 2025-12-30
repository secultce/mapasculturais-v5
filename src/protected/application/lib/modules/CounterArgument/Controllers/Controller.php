<?php

namespace CounterArgument\Controllers;

use MapasCulturais\App;
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
        $data = $this->getPostData();
        $registration = App::i()->repo('Registration')->find($data['registration']);

        try {
            $this->counterArgumentService->send($data['text'], $registration);
            $this->json(['message' => 'Contrarrazão enviada com sucesso. Aguarde a resposta.'], 201);
        } catch (\Throwable $th) {
            SentryService::captureExceptions($th);
        }
    }

    public function POST_update()
    {
        $data = $this->getPostData();

        try {
            $this->counterArgumentService->update($data);
            $this->json(['message' => 'Contrarrazão atualizada com sucesso. Aguarde a resposta.']);
        } catch (\Throwable $th) {
            SentryService::captureExceptions($th);
        }
    }

    public function POST_removeFile()
    {
        $data = $this->getPostData();

        try {
            $this->counterArgumentService->removeFile((int)$data['fileId']);
            $this->json(['message' => 'O arquivo foi removido da contrarrazão']);
        } catch (\Throwable $th) {
            SentryService::captureExceptions($th);
        }
    }
}
