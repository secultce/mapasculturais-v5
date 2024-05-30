<?php
namespace Diligence\Controllers;

use \MapasCulturais\App;
use Diligence\Entities\Tado as EntityTado;
use Carbon\Carbon;

class Tado extends \MapasCulturais\Controller
{
    use \Diligence\Traits\DiligenceSingle;

    function GET_emitirTado()
    {
        
        $app = App::i();
        $reg = $app->repo('Registration')->find($this->data['id']);
        $app->view->enqueueStyle('app', 'diligence', 'css/diligence/multi.css');
        $app->view->enqueueScript('app', 'tado', 'js/multi/tado.js');
        $app->view->enqueueScript('app', 'ckeditor-diligence', 'https://cdn.ckeditor.com/4.16.0/standard/ckeditor.js');
        $this->render('emitir', ['reg' => $reg]);
    }

    function GET_gerarTado()
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