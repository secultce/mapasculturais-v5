<?php

namespace Diligence\Traits;

use \MapasCulturais\App;

trait DiligenceSingle{

    /**
     * Responsável para salvar os dados da entidade
     *
     * @param [object] $entity
     * @return void
     */
    static protected function saveEntity($entity)
    {

        $app        = App::i();
        $app->em->persist($entity);
        $app->em->flush();
        $app->disableAccessControl();
        $entity->save();
        $app->enableAccessControl();
        return ['entityId' => $entity->id];
    }

    static protected function returnJson($instance, $class)
    {
        if(is_null($instance)){
            $class->json(['message' => 'success', 'status' => 200], 200);
        }else{
            $class->json(['message' => 'Error: ', 'status' => 400], 400);
        }    
    }

    static public function mpdfConfig()
    {
        return new \Mpdf\Mpdf([
            'tempDir' => dirname(__DIR__) . '/vendor/mpdf/mpdf/tmp', 'mode' =>
            'utf-8', 'format' => 'A4',
            'pagenumPrefix' => 'Página ',
            'pagenumSuffix' => '  ',
            'nbpgPrefix' => ' de ',
            'nbpgSuffix' => ''
        ]);
        
    }

    static public function mdfBodyMulti(\Mpdf\Mpdf $mpdf, $fileHtmlBody, $titleReport, $pathCss)
    {
        $app        = App::i();
        $content = $app->view->fetch($fileHtmlBody);
        $mpdf->SetTitle($titleReport);
        $stylesheet = file_get_contents(MODULES_PATH . $pathCss);
        
        $footerPage = $app->view->fetch('tado/footer-pdf');
        // Adicione o CSS ao mPDF

        $mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);

        $mpdf->WriteHTML(ob_get_clean());

        $mpdf->WriteHTML($content);
        $mpdf->SetHTMLFooter($footerPage);
        $mpdf->Output();
    }
}