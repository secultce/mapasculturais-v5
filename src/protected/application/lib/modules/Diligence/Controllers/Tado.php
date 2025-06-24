<?php

namespace Diligence\Controllers;

use Carbon\Carbon;
use Diligence\Repositories\Diligence;
use \MapasCulturais\App;
use MapasCulturais\Entity;
use Diligence\Entities\Tado as EntityTado;
use Diligence\Entities\Diligence as EntityDiligence;
use Diligence\Repositories\Diligence as RepoDiligence;
use Diligence\Entities\NotificationDiligence;

class Tado extends \MapasCulturais\Controller
{
    const STATUS_DRAFT = Entity::STATUS_DRAFT;
    const STATUS_FINISH = Entity::STATUS_ENABLED;

    use \Diligence\Traits\DiligenceSingle;
    use \MapasCulturais\Traits\ControllerUploads;

    function GET_emitir()
    {
        $this->requireAuthentication();
        $app = App::i();
        $reg = $app->repo('Registration')->find($this->data['id']);
        $isEvaluator = $app->isEvaluator($reg->opportunity, $reg);
        $app->view->enqueueStyle('app', 'diligence', 'css/diligence/multi.css');

        $app->view->enqueueScript('app', 'tado', 'js/multi/tado.js');
        $app->view->enqueueScript('app', 'diligence-message', 'js/diligence/diligenceMessage.js');
        $app->view->enqueueScript('app', 'ckeditor-diligence', 'https://cdnjs.cloudflare.com/ajax/libs/froala-editor/4.2.1/js/froala_editor.pkgd.min.js');
        $app->view->enqueueScript('app', 'jquery-cookies', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js');
        $app->view->enqueueStyle('app', 'ckeditor-diligence', 'https://cdnjs.cloudflare.com/ajax/libs/froala-editor/4.2.1/css/froala_editor.pkgd.min.css');
        //Acesso ao avaliador, superAdmin+ ou admin da oportunidade
        if($isEvaluator || $app->user->is('superAdmin') || $reg->opportunity->canUser('@control'))
        {
            return $this->render('emitir', ['reg' => $reg]);
        };       
        //Redireciona se nao tiver permissão
        return $app->redirect($app->createUrl('oportunidade', $reg->opportunity->id), 401);
    }

    function GET_gerar()
    {
        $this->requireAuthentication();
        $app = App::i();
        $td = new RepoDiligence();
        $reg = $app->repo('Registration')->find($this->data['id']);
        //INSTANCIA DO TIPO ARRAY OBJETO
        $app->view->regObject = new \ArrayObject;
        //Buscando o tado gerado        
        $app->view->regObject['tado'] = $td->getTado($reg);
        $app->view->regObject['reg']  = $reg;
        $mpdf = self::mpdfConfig();
        self::mdfBodyMulti($mpdf,
            'tado/gerar',
            'Secult/CE - TADO',
            'Diligence/assets/css/diligence/multi.css');

    }

    function POST_saveTado()
    {
        $request = $this;
        $tado = new EntityTado();

        //Recebendo data preenchida ou data e hora atual
        $dateDay = Carbon::createFromFormat('d/m/Y H:i', "{$request->data["dateDay"]} 00:00");
        if($dateDay == ""){
            $dateDay = Carbon::now()->setTimezone('America/Fortaleza')->format('Y-m-d H:i');
        }
        //Validando para o Frontend
        $validateBack = $tado->validateForm($request);
        //Se tiver campo obrigatório vazio então dispara mensagem
        !empty($validateBack) ? $this->json(['data' => $validateBack, 'status' => 403]) : null;
     
        $app = App::i();

        if (intval($request->data['idTado']) > 0) {
            self::update($request);           
        } else {
            $reg = $app->repo('Registration')->find($this->data['id']);
            $tado = new EntityTado();
            $tado->number           = $this->data['numbertec'];
            $tado->createTimestamp  = $dateDay;
            $tado->periodFrom       = Carbon::createFromFormat('d/m/Y H:i', "{$request->data["datePeriodInitial"]} 00:00");
            $tado->periodTo         = Carbon::createFromFormat('d/m/Y H:i', "{$request->data["datePeriodEnd"]} 00:00");
            $tado->agent            = $reg->owner;
            $tado->object           = $this->data['object'];
            $tado->registration     = $reg;
            $tado->conclusion       = $this->data['conclusion'];
            $tado->status           = $this->data['status'];
            $tado->agentSignature   = $app->auth->getAuthenticatedUser()->profile;
            $tado->cpfManager       = $this->data['cpfManager'];

            $entity = self::saveEntity($tado);
            if ($entity["entityId"]) {
                if ($this->data['status'] == 1) {
                    Diligence::updateStatusByRegistration((int)$this->data['id'], EntityDiligence::STATUS_COMPLETE);
                    self::returnRequestJson(
                        'O seu documento foi gerado!',
                        'TADO finalizado e realizado o download para o seu computador.',
                        200
                    );
                }
                self::returnRequestJson('Sucesso!', 'Rascunho criado com sucesso.', 200);
            }
        }
    }

    //Atualizando a instância
    function update($request)
    {
        $app = App::i();
        $tado = $app->repo('Diligence\Entities\Tado')->find($request->data['idTado']);
        $app->repo('Registration')->find($request->data['id']);
        $tado->number           = $request->data['numbertec'];
        $tado->periodFrom       = Carbon::createFromFormat('d/m/Y H:i', "{$request->data["datePeriodInitial"]} 00:00");
        $tado->periodTo         = Carbon::createFromFormat('d/m/Y H:i', "{$request->data["datePeriodEnd"]} 00:00");
        $tado->object           = $request->data['object'];
        $tado->conclusion       = $request->data['conclusion'];
        $tado->agentSignature   = $app->auth->getAuthenticatedUser()->profile;
        $tado->status           = $request->data['status'];
        $tado->nameManager      = $request->data['nameManager'];
        $tado->cpfManager       = $request->data['cpfManager'];
        $entity = self::saveEntity($tado);

        self::sendNotificationTagoGeneration();

        if($entity["entityId"]){
            if ($request->data['status'] == 1) {
                Diligence::updateStatusByRegistration((int)$this->data['id'], EntityDiligence::STATUS_COMPLETE);
                self::returnRequestJson(
                    'O seu documento foi gerado!',
                    'TADO finalizado e realizado o download para o seu computador.',
                    200
                );
            }else{
                self::returnRequestJson('Sucesso!', 'Tado alterado com sucesso', 200);
            }            
        }else{
            self::returnRequestJson('Ops!', 'Ocorreu um erro inesperado', 401);
        }
        
    }

    /**
     * Função local da classe somente para enviar mensagem 
     */
    private function returnRequestJson($title, $message, $status)
    {
        $this->json([
            'title' => $title,
            'message' => $message,
            'status' => $status
        ]);
    }

    //Notificação via plataforma do mapa cultural ao proponente
    public function sendNotificationTagoGeneration()
    {
        $app = App::i();
        $ag = $app->repo('Registration')->find($this->data['id']);
        //Inscrição, agente fiscal e agente proponente
        $notifi = [
            'registration' => $ag->id,
            'openAgent' => $app->user->profile->id,
            'agent' => $ag->owner->id
        ];
        
        $notification = new NotificationDiligence();
        $class = new class {
            public $data = [];
        };

        $class->data = $notifi;
        $notification->create($class, EntityDiligence::TYPE_NOTIFICATION_TADO);
    }
}
