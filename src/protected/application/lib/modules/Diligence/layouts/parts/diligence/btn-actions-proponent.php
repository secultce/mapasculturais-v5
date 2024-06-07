<?php

use Diligence\Entities\AnswerDiligence;
use \MapasCulturais\App;
use Diligence\Repositories\Diligence as DiligenceRepo;

$app = App::i();

$diligence = DiligenceRepo::findBy('Diligence\Entities\Diligence', ['registration' => $entity->id]);
$diligenceId = $diligence ? $diligence[0]->id : null;

// Buscando os arquivos da diligência
$files = DiligenceRepo::getFilesDiligence($diligenceId);
$this->jsObject['countFileUpload'] = count($files);
?>

<div class="widget flex-items" id="div-btn-actions-proponent">
    <div style="width: 50%">
        <span class="title-send-file">ENVIAR ARQUIVO</span><br>
        <?php if ($showText) { ?>
            <div id="div-upload-file-count">
                <a class="js-open-editbox hltip" data-target="#answer-diligence" href="#" title="Click para anexar arquivo">Anexar arquivo</a>
                <div id="answer-diligence" class="js-editbox mc-left" title="Anexar arquivo" data-submit-label="Enviar">
                    <form class="js-ajax-upload" id="upload-file-diligence" data-action="append" data-target=".import-diligence" data-group="answer-diligence" method="post" action="<?php echo $app->createUrl('diligence', 'upload', ['id' => $diligenceId]) ?>" enctype="multipart/form-data">
                        <div class="alert danger hidden"></div>
                        <input type="file" name="answer-diligence" />
                    </form>
                    <div class="js-ajax-upload-progress">
                        <div class="progress inactive">
                            <div class="bar"></div>
                            <div class="percent">0%</div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } elseif (!$showText) {
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

<div style="width: 100%" id="attachment-info">
    <span>Informações sobre o anexo:</span>
    <ul style="color: #505050;">
        <li>Você poderá enviar até 02 (dois) anexos, <strong>01 por vez</strong></li>
        <li>Tamanho de arquivo suportado: <strong><?php echo $this->jsObject['maxUploadSizeFormatted']; ?></strong></li>
        <li>Tipos de arquivos suportados: <strong>.pdf|.gif|jpeg|pjpeg|png</strong></li>
    </ul>
</div>
<div class="import-diligence" style="width: 100%">
    <?php
    if ($diligence && (is_null($diligence[0]->answer) || $diligence[0]->answer->status != AnswerDiligence::STATUS_SEND)) {
        foreach ($files as $file) {
            $id = $file["id"];
            echo '<article id="file-diligence-up-' . $id . '" class="objeto" style="margin-top: 20px;">
                    <span>Arquivo</span>
                    <h1>
                        <a href="/arquivos/privateFile/' . $id . '" class="attachment-title ng-binding ng-scope" target="_blank" rel="noopener noreferrer">
                            ' . $file["name"] . '
                        </a>
                    </h1>
                    <div class="botoes footer-btn-delete-file-diligence">
                        <a data-href="/diligence/deleteFile/' . $id . '/registration/' . $entity->id . '"
                            data-target="#file-diligence-up-' . $id . '"
                            data-configm-message="Remover este arquivo?"
                            class="btn btn-small btn-danger delete hltip js-remove-item"
                            title="Excluir arquivo">
                            Excluir
                        </a>
                    </div>
                </article>';
        }
    }
    ?>
</div>
