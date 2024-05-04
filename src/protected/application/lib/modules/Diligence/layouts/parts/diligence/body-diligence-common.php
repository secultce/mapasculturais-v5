<?php

use MapasCulturais\i;
use Diligence\Entities\Diligence as EntityDiligence;
if(!$sendEvaluation):
?>
<p id="paragraph_loading_content">
<label for="">
    Carregando ... <img id="img-loading-content" />
</label>
<br />

<br />
</p>
<label>
    <strong>
        <?php
        i::_e('Diligência ao proponente')
        ?>
    </strong>
</label>
<div class="div-diligence" id="div-diligence">
    <p id="paragraph_info_status_diligence"></p>
</div>
<div style="margin-top: 30px; width: 100%;" id="div-content-all-diligence-send">
    <label class="label-diligence-send">Diligência:</label>
    <p id="paragraph_content_send_diligence"></p>
    <p id="paragraph_createTimestamp" class="paragraph-createTimestamp"></p>
    <div style="width: 100%;  display: flex;justify-content: space-between;flex-wrap: wrap; ">
        <div class="item-col"></div>
        <div class="item-col" style="padding: 8px;">
            <p>
                <?php
                EntityDiligence::infoTerm($entity, $diligenceRepository, $diligenceDays);
                ?>
            </p>
        </div>
        <div class="item-col"></div>
    </div>
    <div id="answer_diligence" class="answer_diligence">
        <label for="" style="font-size: 14px; font-weight: 700;  font-family: inherit;  line-height: 19.07px;">
            Resposta do Proponente:
        </label>
        <p id="paragraph_content_send_answer" class="paragraph_content_send_answer"></p>
        <p id="paragraph_createTimestamp_answer" class="paragraph-createTimestamp"></p>
    </div>

</div>
<div>
    <textarea name="description" id="descriptionDiligence" cols="30" rows="10" placeholder="<?= $placeHolder; ?>" class="diligence-context-open"></textarea>
</div>
<label class="diligence-label-save" id="label-save-content-diligence">
    <i class="fas fa-check-circle mr-10"></i>
    Suas alterações foram salvas
</label>

<?php endif; ?>
