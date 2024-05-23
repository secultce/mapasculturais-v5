<?php 
declare(strict_types=1);
use PHPUnit\Framework\TestCase;
use MapasCulturais\Entities\Agent;
use MapasCulturais\App;

final class AgentTest extends TestCase
{
    public function testIsUser()
    {
        $agent = $this->user = 30490;

        $this->assertIsInt($agent, 'Ops!');
        
    }
}