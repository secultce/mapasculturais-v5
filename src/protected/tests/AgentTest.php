<?php 
declare(strict_types=1);
require dirname(dirname(dirname(__DIR__))).'/tests/bootstrap.php';

use MapasCulturais\App;

class AgentTest extends MapasCulturais_TestCase
{
    public function testIsUser()
    {       
        $app = App::i();
        $agent = $app->repo('Agent')->find(138804);


        $this->assertIsObject($agent->user, 'Ops');//se é um usuario
        $this->assertFalse($agent->user->is('guest'), 'Ops');//Se está logadoo
        
    }
}