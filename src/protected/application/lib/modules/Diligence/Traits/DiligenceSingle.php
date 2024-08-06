<?php

namespace Diligence\Traits;

use \MapasCulturais\App;
use Mpdf\Mpdf;

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

    static public function mpdfConfig() : \Mpdf\Mpdf
    {
        return new \Mpdf\Mpdf([
            'tempDir' => '/tmp',
            'mode' => 'utf-8', 
            'format' => 'A4',
            'pagenumPrefix' => 'Página ',
            'pagenumSuffix' => '  ',
            'nbpgPrefix' => ' de ',
            'nbpgSuffix' => '',
            'margin_top' => 45,
            'margin_bottom' => 20,
            'margin_left' => 0,
            'margin_right' => 0,
        ]);
        
    }

    static public function mdfBodyMulti(\Mpdf\Mpdf $mpdf, $fileHtmlBody, $titleReport, $pathCss)
    {
        $app        = App::i();
        $content = $app->view->fetch($fileHtmlBody);
        $stylesheet = file_get_contents(MODULES_PATH . $pathCss);
        $footerPage = $app->view->fetch('pdf/footer-pdf');
        // Adicione o CSS ao mPDF
        $mpdf->SetTitle($titleReport);
        $mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);

        $mpdf->SetHTMLFooter($footerPage);
        $mpdf->WriteHTML($content);
        $mpdf->WriteHTML(ob_get_clean());
        $mpdf->Output();
    }
}