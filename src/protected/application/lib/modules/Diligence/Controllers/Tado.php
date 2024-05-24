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
        $this->render('emitir', ['reg' => $reg]);
    }


}