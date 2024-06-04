<?php
namespace Diligence\Controllers;

use \MapasCulturais\App;
use Diligence\Entities\Tado as EntityTado;
use Carbon\Carbon;

class Tado extends \MapasCulturais\Controller
{
    use \Diligence\Traits\DiligenceSingle;

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
        dump($this->data);
        $app = App::i();
        $entityGet = self::getrequestedEntity($this);
        $reg = $app->repo('Registration')->find($this->data['id']);
        // dump($reg); die;
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

        $entity = self::saveEntity($tado);
        self::returnJson($entity, $this);
        // dump($entity);
        // $this->json(['Message' => $diliMeta]);
    }

}