<?php 
use Diligence\Entities\Diligence as EntityDiligence;
?>

<div style="margin-top: 30px; width: 100%;" id="div-content-all-diligence-send">
    <div style="width: 100%;  display: flex;justify-content: space-between;flex-wrap: wrap; ">
        <div class="item-col"></div>
        <div class="item-col" style="padding: 8px;">
            <p style="font-size: smaller;">
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
    </div>
</div>