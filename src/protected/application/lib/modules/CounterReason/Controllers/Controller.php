<?php
namespace CounterReason\Controllers;
ini_set('display_errors', 1);
use CounterReason\Entities\CounterReason as CounterReasonEntity;
use CounterReason\Repositories\CounterReasonRepository;
use CounterReason\Services\CounterReasonService;
use MapasCulturais\App;
use Carbon\Carbon;
use MapasCulturais\Entities\Agent;
use MapasCulturais\Entities\Registration;
use MapasCulturais\Services\SentryService;


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
            if (empty($this->data['text'])) {
                return $this->json([
                    'error' => 'Precisa ter o texto da contrarrazão.'
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
                $app->log->debug('Contrarrazão editada com sucesso.');
            } else {
                // Cria
                $entity = CounterReasonService::create($app, $this->data);
                $app->log->debug('Contrarrazão criada com sucesso.');
            }
            $this->json(['message' => 'success', 'status' => 200, 'entityId' => $entity]);
        }catch (\Exception $e){
            SentryService::captureExceptions($e);
        }

    }

    public function GET_todas()
    {
        $this->requireAuthentication();
        $app = App::i();

        $controller = $this;
        $agent = $app->repo('Agent')->find($this->data['id']);
        $cr = CounterReasonRepository::getCounterReasonByAgent($agent, $app);
        if($this->getVerifyUser($cr[0]->agent))
        {
            $this->render('counter-reason-list', [
                'cr' => $cr,
                'scope' => $controller
            ]);
        }else{
            // redirecionar com alerta que não tem contrarrazão
        }

    }

    public function getVerifyUser(Agent $agent) : bool
    {
        $app = App::i();
        return $app->getAuth()->getAuthenticatedUser()->profile == $agent ? true : false;
    }


}
