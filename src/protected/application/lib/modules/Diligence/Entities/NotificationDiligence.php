<?php

namespace Diligence\Entities;
use \MapasCulturais\App;
use MapasCulturais\Entities\Notification;
use Diligence\Repositories\Diligence as DiligenceRepo;

class NotificationDiligence {

    public function create($class)
    {
        $app = App::i();
        $notification = new Notification();
        $agent = $app->repo('Agent')->find($class->data['agent']);
        
        $url = $app->createUrl('inscricao', $class->data['registration']);
        //Mensagem para notificação na plataforma
        $numberRegis = '<a href="'.$url.'">'.$class->data['registration'].'</a>';
        $message = 'Um parecerista abriu uma diligência para você responder na inscrição de número: '.$numberRegis;

        $notification->message  = $message;
        $notification->user     = $agent->user;
        
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


}