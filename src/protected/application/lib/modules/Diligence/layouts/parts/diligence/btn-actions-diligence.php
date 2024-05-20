<?php
if (!$sendEvaluation) :
?>
    <button class="btn-save-diligence mr-10" title="Salva o conteúdo mas não envia sua resposta" id="btn-save-diligence" onclick="saveDiligence(0, 0)">
        Salvar
        <i class="fas fa-save"></i>
    </button>
    <button id="btn-send-diligence" class="btn-send-diligence" title="Salva e envia a sua resposta para a comissão avaliadora." onclick="saveDiligence(3, 3)">
        Enviar Diligência
        <i class="fas fa-paper-plane"></i>
    </button>
<?php endif; ?>