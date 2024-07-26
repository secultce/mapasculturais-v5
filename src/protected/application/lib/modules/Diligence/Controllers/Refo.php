<?php
namespace Diligence\Controllers;

use DateTime;
use Carbon\Carbon;
use \MapasCulturais\App;
use MapasCulturais\Entity;
use Diligence\Repositories\Diligence;

class Refo extends \MapasCulturais\Controller
{
    use \Diligence\Traits\DiligenceSingle;

    function GET_report()
    {
        $app = App::i();
        $dili = Diligence::getDiligenceAnswer($this->data['id']);
        // dump($dili); die;
        $reg = [];
        if(!is_null($dili[0])){
            $reg = $dili[0]->registration;
        }else{
            $reg = $app->repo('Registration')->find($this->data['id']);
        }

        //INSTANCIA DO TIPO ARRAY OBJETO
        $app->view->regObject = new \ArrayObject;
        $app->view->regObject['diligence'] = $dili;
        $app->view->regObject['registration'] = $reg;
        $mpdf = self::mpdfConfig();
        self::mdfBodyMulti($mpdf,
        'refo/report-finance', 
        'Secult/CE - Relatório Financeiro',
        'Diligence/assets/css/diligence/multi.css');
    }

    public function POST_deleteFinancialReport()
    {
        $app = App::i();
        $conn = $app->em->getConnection();

        $file = $app->repo('File')->findBy(['id' => (int) $this->data['fileId']])[0];

        $stmt = $conn->prepare('DELETE FROM file WHERE id = :id');
        $stmt->bindParam('id', $this->data['fileId']);
        $stmt->executeStatement();

        unlink($file->path);

        $this->json($file);
    }
}
