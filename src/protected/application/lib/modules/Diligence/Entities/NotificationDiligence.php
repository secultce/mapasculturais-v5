<?php

namespace Diligence\Entities;
use \MapasCulturais\App;
use MapasCulturais\Entities\Notification;
use Diligence\Repositories\Diligence as DiligenceRepo;

class NotificationDiligence {

    public function create($class, $type)
    {
       
        $app = App::i();
        $notification = new Notification();
        $userNotifi = null;
        if($type == 'diligence') {
            $agent = $app->repo('Agent')->find($class->data['agent']);
            $url = $app->createUrl('inscricao', $class->data['registration']);
            //Mensagem para notificação na plataforma
            $numberRegis = '<a href="'.$url.'">'.$class->data['registration'].'</a>';
            $msgSend = 'Um parecerista abriu uma diligência para você responder na inscrição de número: '.$numberRegis;
            $userNotifi = $agent->user;
        }else{
            $dili = $app->repo('Diligence\Entities\Diligence')->findOneBy(['registration' => $class->data['registration']]);
            $numberRegis = '<a href="'.$app->createUrl('inscricao', $class->data['registration']).'">'.$class->data['registration'].'</a>';
            $msgSend = "Houve uma resposta para prestação de conta de número: ".$numberRegis;
            $userNotifi = $dili->openAgent->user;
        }
        $notification->message  = $msgSend;
        $notification->user     = $userNotifi;
        
        $app->disableAccessControl();
        $notification->save(true);
        $app->enableAccessControl();
    }

    public function userDestination($class) : array
    {
        $regs = DiligenceRepo::getRegistrationAgentOpenAndAgent(
            $class->data['registration'],
            $class->data['openAgent'],
            $class->data['agent']
        );
        //Array para fila
        return [
            'name' => $regs['agent']->name,
            'email' => $regs['agent']->user->email, 
            'number' => $regs['reg']->id,
            'days' => $regs['reg']->opportunity->getMetadata('diligence_days')
        ]; 
    }

//    public function getNotificationAudictor($app, $class)
//    {
//        $dili = $app->repo('Diligence\Entities\Diligence')->findOneBy(['registration' => $class->data['registration']]);
//        $numberRegis = '<a href="'.$app->createUrl('inscricao', $class->data['registration']).'">'.$class->data['registration'].'</a>';
//        $notification->create($class, sprintf(\MapasCulturais\i::__("Houve uma resposta da prestação de conta: ". $numberRegis)));
//    }

}