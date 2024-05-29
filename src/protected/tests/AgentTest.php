<?php 
declare(strict_types=1);
include(dirname(__DIR__).'/application/lib/modules/Diligence/Entities/Diligence.php');

use PHPUnit\Framework\TestCase;
use Diligence\Entities\Diligence;

class AgentTest extends TestCase
{
    public function testIsUser()
    {
       
        $diligence = new Diligence();
        $diligence->getDiligence();
        // $agent = $this->user = 30490;

        $this->assertIsInt($diligence, 'Ops!');
        
    }
}