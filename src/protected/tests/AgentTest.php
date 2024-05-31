<?php 
declare(strict_types=1);
require dirname(dirname(dirname(__DIR__))).'/tests/bootstrap.php';

class AgentTest extends MapasCulturais_TestCase
{
    public function testIsUser()
    {
       
        $diligence = 0;
       
        // $agent = $this->user = 30490;

        $this->assertIsInt($diligence, 'Ops!');
        
    }
}