<?php
namespace CounterReason\Controllers;

use CounterReason\Entities\CounterReason as CounterReasonEntity;
use CounterReason\Repositories\CounterReasonRepository;
use CounterReason\Services\CounterReasonService;
use MapasCulturais\App;
use Carbon\Carbon;
use MapasCulturais\Entities\Registration;


class Controller extends \MapasCulturais\Controller
{

    public function GET_index()
    {

    }

    public function POST_save()
    {
        $app = App::i();
        try {
            // 1. Validação básica
            if (!isset($this->data['registration'])) {
                return $this->json([
                    'error' => 'O valor da inscrição é obrigatório.'
                ], 400);
            }

            // 2. Busca a inscrição
            $registration = $app->repo(Registration::class)->find($this->data['registration']);
            if (!$registration) {
                return $this->json([
                    'error' => 'Inscrição ou Contrarrazão não encontrada.'
                ], 404);
            }

            // 3. Verifica permissão: só o dono pode enviar/editar
            if ($registration->owner->id !== $app->user->profile->id) {
                return $this->json([
                    'error' => 'Você não tem permissão para editar esta inscrição.'
                ], 403);
            }

            // 4. Decide: create ou update?
            $existing = CounterReasonRepository::getCounterReason($registration, $app);
            if ($existing) {
                // Atualiza
                $entity = CounterReasonService::update($registration, $app, $this->data);
            } else {
                // Cria
                $entity = CounterReasonService::create($app, $this->data);
            }
            $this->json(['message' => 'success', 'status' => 200, 'entityId' => $entity]);
        }catch (\Exception $e){
            dump($e);
        }

    }
}
