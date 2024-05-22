<?php

use \MapasCulturais\App;

$app = App::i();

$reload = $app->config['base.url'] . 'inscricao/' . $entity->id;

use Diligence\Repositories\Diligence as DiligenceRepo;
//Buscando os arquivos dessa diligencia
$files = DiligenceRepo::getFilesDiligence($entity->id);
$countFile = count($files);

$file = $entity->getFiles('file-diligence');
$countFile > 0 ? $this->jsObject['countFileUpload'] = $countFile : $this->jsObject['countFileUpload'] = 0;
//Template para geração apos o envio do arquivo.
$template = '
<div  ng-if="id > 0">
<article id="file-diligence-{{id}}" class="objeto">
    <h1><a href="{{url}}" rel="noopener noreferrer">{{description}}</a></h1> 
    <div class="botoes footer-btn-delete-file-diligence">
        <a data-href="/diligence/deleteFile/{{id}}/registration/' . $entity->id . '"
            data-target="#file-diligence-{{id}}"
            data-configm-message="Remover este arquivo?"
            class="btn btn-small btn-danger delete hltip js-remove-item" onclick="decre()">Excluir</a>    
    </div>
</article></div>';
?>

<div class="widget flex-items" id="div-btn-actions-proponent">
    <div style="width: 50%">
        <span class="title-send-file">ENVIAR ARQUIVO</span> <br>
        <?php
        if ($showText) { ?>
            <div id="div-upload-file-count">
                <a class="js-open-editbox hltip" data-target="#file-diligence" href="#" title="Click para anexar arquivo"> Anexar arquivo</a>
                <div id="file-diligence" class="js-editbox mc-left" title="Anexar arquivo" data-submit-label="Enviar">
                    <?php
                    $this->ajaxUploader($entity, 'file-diligence', 'append', '.import-diligence', $template, '', false, false, true);
                    // dump($diligence);
                    // $file_owner = $diligence->id;
                    // $file_group = 'file-diligence';
                    // $response_action = 'append';
                    // $response_target = '.import-diligence';
                    // $response_template = $template;
                    // $response_transform = '';
                    // $add_description = false;
                    // $file_types = false;
                    // $human_crop = false;
                    ?>

                </div>
            </div>
        <?php
        } elseif (!$showText) {
            echo "<span></span>";
        } else {
            echo '<span>Atingido o limite de arquivos. <button class="btn-reload-diligence" onClick="window.location.reload();" title="Recarregar arquivos"> <i class="fa fa-redo-alt"></i> </button></span>';
        }
        ?>
        <span id="info-title-limit-file-diligence"></span>
    </div>

    <div class="div-actions-proponent">
        <button class="btn-save-diligence mr-10" title="Salva o conteúdo mas não envia sua resposta" id="btn-save-diligence-proponent" onclick="saveAnswerProponente(0)">
            Salvar
            <i class="fas fa-save"></i>
        </button>
        <button id="btn-send-diligence-proponente" class="btn-save-diligence" title="Salva e envia a sua resposta para a comissão avaliadora." onclick="saveAnswerProponente(3)">
            Enviar resposta
            <i class="fas fa-paper-plane"></i>
        </button>
    </div>

</div>
<div style="width: 100%">
    <span>Informações sobre o anexo:</span>
    <ul style="color: #505050;">
        <li>Poderá enviar até 02 (dois) anexos, <strong>01 por vez</strong>.</li>
        <li>Tamanho de arquivo suportado: <strong><?php echo $this->jsObject['maxUploadSizeFormatted']; ?></strong></li>
        <li>Tipos de arquivos suportados: <strong>.pdf; .gif|jpeg|pjpeg|png;</strong></li>
    </ul>
</div>
<div class="import-diligence" style="width: 100%">
    <?php echo $template;
    foreach ($files as $file) {
        $id = $file["id"];
        echo  '<article id="file-diligence-up-' . $id . '" class="objeto" style="margin-top: 20px;">
        <span>Arquivo</span>
        <h1><a href="/arquivos/privateFile/' . $id . '" 
        class="attachment-title ng-binding ng-scope" target="_blank" rel="noopener noreferrer" 
        >' . $file["name"] . '</a></h1> 
        <div class="botoes footer-btn-delete-file-diligence">
            <a data-href="/diligence/deleteFile/' . $id . '/registration/' . $entity->id . '"
            data-target="#file-diligence-up-' . $id . '"
            data-configm-message="Remover este arquivo?"
            class="btn btn-small btn-danger delete hltip js-remove-item">Excluir</a>
        </div>
    </article>';
    } ?>
</div>