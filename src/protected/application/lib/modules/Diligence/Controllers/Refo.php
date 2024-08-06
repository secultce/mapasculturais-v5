<?php
namespace Diligence\Controllers;

use \MapasCulturais\App;
use Diligence\Repositories\Diligence;
use Diligence\Entities\Diligence as EntitiesDiligence;

class Refo extends \MapasCulturais\Controller
{
    use \Diligence\Traits\DiligenceSingle;

    function GET_report()
    {
        $app = App::i();
        $dili = Diligence::getDiligenceAnswer($this->data['id']);
        if(!is_null($dili[0])){
            $reg = $dili[0]->registration;
        }else{
            $reg = $app->repo('Registration')->find($this->data['id']);
        }

        //INSTANCIA DO TIPO ARRAY OBJETO
        $app->view->regObject = new \ArrayObject;
        $app->view->regObject['diligence'] = $dili;
        $app->view->regObject['registration'] = $reg;
        $mpdf = self::mpdfConfig();
        self::mdfBodyMulti($mpdf,
        'refo/report-finance', 
        'Secult/CE - Relatório Financeiro',
        'Diligence/assets/css/diligence/multi.css');
    }

    function POST_situacion()
    {
        $app = App::i();
        $entity = $app->repo('Registration')->find($this->data['entity']);
      
        if(is_null($entity->getMetadata('situacion_diligence'))) 
        {
            $metaData = EntitiesDiligence::createSituacionMetadata($this, $entity);
            self::saveEntity($metaData);
            self::returnJson(null, $this);
        }else{
            $meta = $app->repo('RegistrationMeta')->findOneBy([
                'owner' => $entity,
                'key' => 'situacion_diligence'
            ]);
            $meta->value = $this->data['situacion'];         
            self::saveEntity($meta);
            self::returnJson(null, $this);
        }       
    }

    //Retorna a situação da pc de conta para selecionar a opção na view
    function GET_getSituacionPC() : void
    {
        $app = App::i();
        $entity = $app->repo('Registration')->find($this->data['id']);
        $repoDiligence = new Diligence();
        $this->json(['situacion' => $repoDiligence->getSituacionPC($entity)]);
    }
}