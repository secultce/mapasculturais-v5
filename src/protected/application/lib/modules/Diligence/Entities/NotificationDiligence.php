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
        // Mensagem para notificação na plataforma
        if($type == 'diligence') {
            $agent = $app->repo('Agent')->find($class->data['agent']);
            $notification->message = $this->generateMessage($class, $type);
            $notification->user = $agent->user;
        }else{
            $dili = $app->repo('Diligence\Entities\Diligence')->findOneBy(['registration' => $class->data['registration']]);
            $notification->message = $this->generateMessage($class, $type);
            $notification->user = $dili->openAgent->user;
        }
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
        // Array para fila
        return [
            'name' => $regs['agent']->name,
            'email' => $regs['agent']->user->email, 
            'number' => $regs['reg']->id,
            'days' => $regs['reg']->opportunity->getMetadata('diligence_days')
        ]; 
    }
    /**
     * Gera o contexto da mensagem para a notificação
     * @param $class
     * @param $type
     * @return string
     */
    public function generateMessage($class, $type) : string
    {
        $app = App::i();
        if($type == 'diligence') {
            // Mensagem para o proponente
            $numberRegis = '<a href="'. $app->createUrl('inscricao', $class->data['registration']).'">'.$class->data['registration'].'</a>';
            return 'Um parecerista abriu uma diligência para você responder na inscrição de número: '.$numberRegis;
        } else {
            // Mensagem para o fiscal
            $numberRegis = '<a href="'.$app->createUrl('inscricao', $class->data['registration']).'">'.$class->data['registration'].'</a>';
            return "Houve uma resposta para prestação de conta de número: ".$numberRegis;
        }
    }

}