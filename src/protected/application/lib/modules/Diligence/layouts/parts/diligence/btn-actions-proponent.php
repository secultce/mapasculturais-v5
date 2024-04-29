<?php
use \MapasCulturais\App;
$app = App::i();

$reload = $app->config['base.url'].'inscricao/'.$entity->id;

use Diligence\Repositories\Diligence as DiligenceRepo;
//Buscando os arquivos dessa diligencia
$files = DiligenceRepo::getFilesDiligence($entity->id);
$countFile = count($files);
$file = $entity->getFiles('file-diligence');
$this->jsObject['countFileUpload'] = 0;
//Template para geração apos o envio do arquivo.
$template = '
<div  ng-if="id > 0">
<article id="file-diligence-{{id}}" class="objeto">
    <h1><a href="{{url}}" rel="noopener noreferrer">{{description}}</a></h1> 
    <div class="botoes">
        <a data-href="/diligence/deleteFile/{{id}}/registration/'.$entity->id.'"
            data-target="#file-diligence-{{id}}"
            data-configm-message="Remover este arquivo?"
            class="btn btn-small btn-danger delete hltip js-remove-item" >Excluir</a>    
    </div>
</article></div>';
//    <a href="#" onclick="deleteFileDiligence({{id}})" class="btn btn-small btn-danger delete" >Excluir</a>
?>

<div class="widget flex-items" id="div-btn-actions-proponent">
   
    <div style="width: 50%">

         <span class="title-send-file">ENVIAR ARQUIVO</span> <br>
        
        <?php if($countFile <= 2): ?>
        <div id="div-upload-file-count">
            <a class="js-open-editbox hltip" data-target="#file-diligence" href="#"> Anexar arquivo 01</a>
            <div id="file-diligence" class="js-editbox mc-left" 
                title="Anexar arquivo 01" data-submit-label="Enviar"
                    
            >
                <?php $this->ajaxUploader($entity, 'file-diligence', 'append', '.import-diligence', $template , '', false, false, true) ?>
            </div>
        </div>
        <?php else:
            echo '<span>Atingido o limite de arquivos</span>';
        endif; ?>
        <span id="info-title-limit-file-diligence">
           
        </span>
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
    foreach($files as $file){
        $id = $file["id"];
       echo  '<article id="file-diligence-'.$id.'" class="objeto">
        <h1><a href="/arquivos/privateFile/'.$id.'" 
        class="attachment-title ng-binding ng-scope" target="_blank" rel="noopener noreferrer" 
        >'.$file["name"].'</a></h1> 
        <div class="botoes">
            <a data-href="/diligence/deleteFile/'.$id.'/registration/'.$entity->id.'"
            data-target="#file-diligence-'.$id.'"
            data-configm-message="Remover este arquivo?"
            class="btn btn-small btn-danger delete hltip js-remove-item" >Excluir</a>
        </div>
    </article>';
    } ?>
</div>