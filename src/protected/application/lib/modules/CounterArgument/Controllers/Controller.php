<?php

namespace CounterArgument\Controllers;

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

        try {
            $this->counterArgumentService->send($data);
            $this->json(['message' => 'Contrarrazão enviada com sucesso. Aguarde a resposta.'], 201);
        } catch (\Throwable $th) {
            SentryService::captureExceptions($th);
        }
    }
}
