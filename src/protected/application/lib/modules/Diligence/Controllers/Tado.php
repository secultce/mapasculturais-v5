<?php
namespace Diligence\Controllers;

use \MapasCulturais\App;
use \Diligence\Entities\DiligenceMeta;

class Tado extends \MapasCulturais\Controller
{
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
        $diliMeta = new DiligenceMeta();
        $diliMeta->key = 'conclusion';
        $diliMeta->value = $this->data['conclusion'];
        dump($diliMeta);
        // $this->json(['Message' => $diliMeta]);
    }

}