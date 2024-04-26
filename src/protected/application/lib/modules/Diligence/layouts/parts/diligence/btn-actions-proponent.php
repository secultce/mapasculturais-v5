<?php 
use \MapasCulturais\App;

$entityDiligence = App::i()->repo('Diligence\Entities\Diligence')->findOneBy(['registration' => $entity]);
?>
<div class="widget flex-items" id="div-btn-actions-proponent">
   <!-- <h3 class="editando">Enviar arquivo(s)</h3> -->

    <button class="btn-save-diligence mr-10" 
        title="Salva o conteúdo mas não envia sua resposta"
        id="btn-save-diligence-proponent"
        onclick="saveAnswerProponente(0)"
    >
        Salvar
        <i class="fas fa-save"></i>
    </button>
    <button id="btn-send-diligence-proponente" 
        class="btn-save-diligence"
        title="Salva e envia a sua resposta para a comissão avaliadora."
        onclick="saveAnswerProponente(3)">
        Enviar resposta
        <i class="fas fa-paper-plane"></i>
    </button>
    <div ng-app="DiligenceAngular" class="main-content">
    <a class="add btn btn-default js-open-editbox hltip" data-target="#file-diligence" href="#"> Enviar arquivo</a>
        <div id="file-diligence" class="js-editbox mc-left" title="Anexar arquivo" data-submit-label="Enviar">
            <?php $this->ajaxUploader($entity, 'file-diligence', 'diligence', '', '', '', false, false, true)?>
        </div>

<edit-box 
   id="id-da-caixa" 
   position="right" 
   title="Título da caixa" 
   spinner-condition="data.processando"
   on-open="" 
   on-cancel="" 
   on-submit="envioArquivoDiligencia" 
   close-on-cancel='true'>
   
        <!-- <button 
        class="btn btn-primary"
        title="Salva e envia a sua resposta para a comissão avaliadora."
        >
        Enviar arquivo
        <i class="fas fa-paper-plane"></i>
    </button> -->
   
</edit-box>
    </div>

    <button id="btn-send-diligence-proponente" 
        class="btn-save-diligence"
        title="Salva e envia a sua resposta para a comissão avaliadora."
        onclick="saveAnswerProponente(3)">
        Enviar resposta
        <i class="fas fa-paper-plane"></i>
    </button>
</div>