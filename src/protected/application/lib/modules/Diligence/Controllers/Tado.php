<?php
namespace Diligence\Controllers;

use \MapasCulturais\App;
use Diligence\Entities\AnswerDiligence;
use Diligence\Service\NotificationInterface;
use Diligence\Entities\NotificationDiligence;
use MapasCulturais\Entities\RegistrationMeta;
use Diligence\Entities\Diligence as EntityDiligence;
use Diligence\Repositories\Diligence as DiligenceRepo;
use Carbon\Carbon;

class Tado extends \MapasCulturais\Controller
{
    function GET_emitirTado()
    {
        $app = App::i();
        $reg = $app->repo('Registration')->find($this->data['id']);
        $app->view->enqueueStyle('app', 'diligence', 'css/diligence/multi.css');
        // $app->view->enqueueScript('app', 'ckeditor-diligence', 'js/diligence/ckeditor/ckeditor.js');
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
    //vomito, soando, dores forte, Marcio


}