<?php
namespace Diligence\Controllers;

require dirname(__DIR__).'/Entities/Diligence.php';
require dirname(__DIR__).'/Repositories/Diligence.php';

use DateTime;
use \MapasCulturais\App;
use PhpParser\Node\Stmt\TryCatch;
use Diligence\Repositories\Diligence as DiligenceRepo;
use Diligence\Entities\Diligence as EntityDiligence;
// use MapasCulturais\Entities\EntityRevision as Revision;
// use \MapasCulturais\Entities\EntityRevisionData;
// use MapasCulturais\Traits;
// use Recourse\Entities\Recourse as EntityRecourse;


class Controller extends \MapasCulturais\Controller{

    public function GET_index() {
        $app = App::i();
        $diligence = new EntityDiligence();
        dump($diligence);
    }
    public function POST_save()
    {      
        $this->requireAuthentication();
        $app = App::i();  
        $reg = $app->repo('Registration')->find($this->data['registration']);
        $openAgent = $app->repo('Agent')->find($this->data['openAgent']);
        $agent = $app->repo('Agent')->find($this->data['agent']);
      
        //Se tiver registro com a inscrição passada na requisição
        $diligenceRepository = DiligenceRepo::findBy($this->data['registration']);
        if(count($diligenceRepository) > 0) {
            self::updateContent($diligenceRepository, $this->data['description'], $reg, 0);
        }
        //Instanciando para gravar no banco de dados
        $diligence = new EntityDiligence;
        $diligence->registration    = $reg;
        $diligence->openAgent       = $openAgent;
        $diligence->agent           = $agent;
        $diligence->createTimestamp =  new DateTime();
        $diligence->description     = $this->data['description'];
        $diligence->status          = 0;  
       
        $app->em->persist($diligence);
        $app->em->flush();
        $app->disableAccessControl();
        $save = $diligence->save(true);
        $app->enableAccessControl();      
        self::returnJson($save);      
    }

     /**
     * Busca o conteúdo de uma diligencia salva ou enviada
     *
     * @return void
     */
    public function GET_getcontent() : string
    {
        
        if(isset($this->data['id'])){
            //Repositorio da Diligencia
            $diligence = DiligenceRepo::findBy($this->data['id']);
            $this->json(['message' =>$diligence[0]->description, 'status' => 200], 200);
        }
        //Validação caso nao tenha a inscrição na URL
        $this->json(['message' => 'Falta a inscrição', 'status' => 'error'], 400);
    }
    /**
     * Metodo para alterar o valor do conteudo da mensagem da Diligencia
     *
     * @param [object] $diligences
     * @param [string] $description
     * @param [object] $registration
     * @param [int] $status
     * @return void
     */
    public function updateContent($diligences, $description, $registration, $status = 0) : void
    {
        $this->requireAuthentication();
        $app = App::i();
        $save = null;
        foreach ($diligences as $diligence) {
            $diligence->description      = $description;
            $diligence->registration    = $registration;
            $diligence->createTimestamp =  new DateTime();
            $diligence->description     = $this->data['description'];
            $diligence->status          = $status;
            $app->em->persist($diligence);
            $app->em->flush();
            $app->disableAccessControl();
            $save = $diligence->save();
            $app->enableAccessControl();
        }        
        self::returnJson($save);
    }

    public function returnJson($instance)
    {
        if(is_null($instance)){
            // EntityDiligence::sendQueue($userDestination);
            $this->json(['message' => 'success', 'status' => 200], 200);
        }else{
            $this->json(['message' => 'Error: ', 'status' => 400], 400);
        }    
    }
}