<?php

use MapasCulturais\i;
use \MapasCulturais\App;

$entityDiligence = App::i()->repo('Diligence\Entities\Diligence')->findOneBy(['registration' => $entity]);
$url = $app->createUrl('eventimporter','processFile');
$files = $entity->getFiles('file-diligence');
$template = '
<div  ng-if="id > 0">
<article id="file-diligence-{{id}}" class="objeto">
    <h1><a href="{{url}}" rel="noopener noreferrer">{{description}}</a></h1> 
    <div class="botoes">
        <a href="#" onclick="deleteFileDiligence({{id}})" class="btn btn-small btn-danger" >Excluir</a>
    </div>
</article></div>';
?>
<div class="widget flex-items" id="div-btn-actions-proponent">
   
    <div style="width: 50%">
         <span class="title-send-file">ENVIAR ARQUIVO</span> <br>
        <a class="js-open-editbox hltip" data-target="#file-diligence" href="#"> Anexar arquivo 01</a>
        <div id="file-diligence" class="js-editbox mc-left" title="Anexar arquivo 01" data-submit-label="Enviar">
            <?php $this->ajaxUploader($entity, 'file-diligence', 'append', '.import-diligence', $template , '', false, false, true) ?>
        </div>

    </div>
    <button class="btn-save-diligence mr-10" title="Salva o conteúdo mas não envia sua resposta" id="btn-save-diligence-proponent" onclick="saveAnswerProponente(0)">
        Salvar
        <i class="fas fa-save"></i>
    </button>
    <button id="btn-send-diligence-proponente" class="btn-save-diligence" title="Salva e envia a sua resposta para a comissão avaliadora." onclick="saveAnswerProponente(3)">
        Enviar resposta
        <i class="fas fa-paper-plane"></i>
    </button>
</div>
<div class="import-diligence" style="width: 100%">
    <?php echo $template; 
    dump($files );
    
    ?>
</div>