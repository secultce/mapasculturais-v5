<?php 
declare(strict_types=1);
require dirname(dirname(dirname(__DIR__))).'/tests/bootstrap.php';

use MapasCulturais\App;

class TadoTest extends MapasCulturais_TestCase
{
    public function testEmitir()
    {       
        $app = App::i();
        $agent = $app->repo('Agent')->find(138804);      
        $user = $agent->user;
        
        $this->assertIsObject($user, 'Ops');//se é um usuario
        $this->assertFalse($user->is('guest'), 'Ops');//Se está logadoo
        $this->assertFalse($user->is('admin'), 'Ops');//Se é um admin
        $this->assertTrue($this->isEvaluation(), 'Ops');//Se é um admin
        
    }

    public function isEvaluation()
    {
        
        $app = App::i();
        $reg = $app->repo('Registration')->find(1580933290);
        $agent = $app->repo('Agent')->find(138053);
        return $reg->canUser('evaluate', $agent->user);
    }
}