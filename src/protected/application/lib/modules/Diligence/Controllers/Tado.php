<?php
namespace Diligence\Controllers;

use \MapasCulturais\App;
use Diligence\Entities\Tado as EntityTado;
use Carbon\Carbon;
use Diligence\Entities\AnswerDiligence;
use MapasCulturais\Entity;

class Tado extends \MapasCulturais\Controller
{
    const STATUS_DRAFT = Entity::STATUS_DRAFT;
    const STATUS_FINISH = Entity::STATUS_ENABLED;

    use \Diligence\Traits\DiligenceSingle;
    use \MapasCulturais\Traits\ControllerUploads;

    function GET_emitir()
    {
        $app = App::i();
        $reg = $app->repo('Registration')->find($this->data['id']);
        $isEvaluator = $app->isEvaluator($reg->opportunity, $reg);
        $app->view->enqueueStyle('app', 'diligence', 'css/diligence/multi.css');
        $app->view->enqueueScript('app', 'tado', 'js/multi/tado.js');
        $app->view->enqueueScript('app', 'ckeditor-diligence', 'https://cdn.ckeditor.com/4.16.0/standard/ckeditor.js');
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
        $app = App::i();
        $reg = $app->repo('Registration')->find($this->data['id']);
        $app->view->enqueueStyle('app', 'diligence', 'css/diligence/multi.css');
        $this->render('gerar', ['reg' => $reg]);
    }

    function POST_saveTado()
    {
        $request = $this;
        $tado = new EntityTado();
        //Recebendo data preenchida ou data e hora atual
        $dateDay = Carbon::parse($this->data['dateDay'])->format('Y-m-d H:i ');
        if($dateDay == ""){
            $dateDay = Carbon::now();
        }
        //Validando para o Frontend
        $validateBack = $tado->validateForm($request);
        //Se tiver campos obrigatório vazio então dispara mensagem
        !empty($validateBack) ? $this->json(['data' => $validateBack, 'status' => 403]) : null;       
     
        $app = App::i();
       
        if(intval($request->data['idTado']) > 0)
        {
            $entity = self::update($request);
           
           
        }else{
            $reg = $app->repo('Registration')->find($this->data['id']);
            $tado = new EntityTado();
            $tado->number           = rand(0, 100);
            $tado->createTimestamp  = Carbon::now();
            $tado->periodFrom       = Carbon::parse('2024-05-01 00:00:00');
            $tado->periodTo         = Carbon::parse('2024-05-31 00:00:00');
            $tado->agent            =   $reg->owner;
            $tado->object           = $this->data['object'];
            $tado->registration     = $reg;
            $tado->conclusion       = $this->data['conclusion'];
            $tado->agentSignature   = $app->auth->getAuthenticatedUser()->profile;
            dump($tado);
            // $entity = self::saveEntity($tado);
            // self::returnJson($entity, $this);
        }
       
        
    }

    function update($request)
    {
        $app = App::i();
        $tado = $app->repo('Diligence\Entities\Tado')->find($request->data['idTado']);
        $tado->object           = $request->data['object'];
        $tado->conclusion       = $request->data['conclusion'];
        $tado->agentSignature   = $app->auth->getAuthenticatedUser()->profile;
        $tado->status           = $request->data['status'];
        $entity = self::saveEntity($tado);
        if(is_null($entity)){
            if($request->data['status'] == 1){
                self::returnRequestJson('Sucesso!', 'Tado finalizado e gerado com sucesso.', 200);
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


}