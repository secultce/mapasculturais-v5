<?php
if (!$sendEvaluation) :
?>
<div class="flex-items align-btn-actions-diligence ">
<button class="btn-save-diligence mr-10" 
        title="Salva o conteúdo mas não envia sua resposta"
        id="btn-save-diligence"
        onclick="saveDiligence(0, 0, $('#id-input-diligence').val())">
        Salvar
        <i class="fas fa-save"></i>
    </button>
    <button id="btn-send-diligence" 
        class="btn-send-diligence" 
        title="Salva e envia a sua resposta para a comissão avaliadora." 
        onclick="saveDiligence(3, 3, $('#id-input-diligence').val())">
        Enviar Diligência
        <i class="fas fa-paper-plane"></i>
    </button>
</div>
<?php endif; ?>