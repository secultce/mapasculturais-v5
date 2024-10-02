<?php

namespace Diligence\Controllers;

use \MapasCulturais\App;
use Diligence\Entities\Diligence as EntitiesDiligence;
use Diligence\Entities\Tado;
use Diligence\Repositories\Diligence as DiligenceRepo;

class Refo extends \MapasCulturais\Controller
{
    use \Diligence\Traits\DiligenceSingle;

    function GET_report()
    {
        $app = App::i();
        $dili = DiligenceRepo::getDiligenceAnswer($this->data['id'], true, true);
        if(!is_null($dili[0])){
            $reg = $dili[0]->registration;
        } else {
            $reg = $app->repo('Registration')->find($this->data['id']);
        }

        //INSTANCIA DO TIPO ARRAY OBJETO
        $app->view->regObject = new \ArrayObject;
        $app->view->regObject['diligence'] = $dili;
        $app->view->regObject['registration'] = $reg;
        $mpdf = self::mpdfConfig();
        self::mdfBodyMulti(
            $mpdf,
            'refo/report-finance',
            'Secult/CE - Relatório Financeiro',
            'Diligence/assets/css/diligence/multi.css'
        );
    }

    public function POST_deleteFinancialReport()
    {
        $app = App::i();
        $conn = $app->em->getConnection();

        $file = $app->repo('File')->find((int) $this->data['fileId']);
        $generatedTado = DiligenceRepo::getTado($file->owner);

        if (!$generatedTado || $generatedTado->status !== Tado::STATUS_ENABLED) {
            $stmt = $conn->prepare('DELETE FROM file WHERE id = :id');
            $stmt->bindParam('id', $this->data['fileId']);
            $stmt->executeStatement();

            unlink($file->path);

            $this->json($file);
        }

        $this->json($file, 400);
    }

    function POST_situacion()
    {
        $app = App::i();
        $entity = $app->repo('Registration')->find($this->data['entity']);

        if (is_null($entity->getMetadata('situacion_diligence'))) {
            $metaData = EntitiesDiligence::createSituacionMetadata($this, $entity);
            self::saveEntity($metaData);
            self::returnJson(null, $this);
        } else {
            $meta = $app->repo('RegistrationMeta')->findOneBy([
                'owner' => $entity,
                'key' => 'situacion_diligence'
            ]);
            $meta->value = $this->data['situacion'];
            self::saveEntity($meta);
            self::returnJson(null, $this);
        }
    }

    // Retorna a situação da pc de conta para selecionar a opção na view
    function GET_getSituacionPC(): void
    {
        $app = App::i();
        $entity = $app->repo('Registration')->find($this->data['id']);
        $repoDiligence = new DiligenceRepo();
        $this->json(['situacion' => $repoDiligence->getSituacionPC($entity)]);
    }
}
