<?php

use MapasCulturais\i;
use Diligence\Entities\Diligence as EntityDiligence;
use Diligence\Entities\AnswerDiligence;
use Carbon\Carbon;

$descriptionDraft = true;

?>

<?php
if ($diligenceAndAnswers) :
?>
    <?php if ($diligenceAndAnswers[0]->status == EntityDiligence::STATUS_SEND) : ?>
        <div>
            <h5>
                <?php
                    i::_e('Diligência ao proponente');
                ?>
            </h5>
            <div style="margin-top: 25px;">
                <div style="font-size: 14px; padding: 10px; margin-bottom: 10px;">
                    <label>
                        <b>Diligência:</b>
                    </label>
                    <p style="margin: 10px 0px;">
                        <?php
                            echo $diligenceAndAnswers[0]->description;
                        ?>
                    </p>
                    <span style="font-size: 12px; font-weight: 700; color: #404040;">
                        <?php
                            echo Carbon::parse($diligenceAndAnswers[0]->sendDiligence)->isoFormat('LLL');
                        ?>
                    </span>
                </div>
                <?php if (!is_null($diligenceAndAnswers[1]) && $diligenceAndAnswers[1]->status == AnswerDiligence::STATUS_SEND) : ?>
                    <div style="font-size: 14px; background-color: #F5F5F5; padding: 10px;">
                        <label>
                            <b>Resposta recebida:</b>
                        </label>
                        <p style="margin: 10px 0px;">
                            <?php
                                echo $diligenceAndAnswers[1]->answer;
                            ?>
                        </p>
                        <span style="font-size: 12px;">
                            <?php
                                echo Carbon::parse($diligenceAndAnswers[1]->createTimestamp)->isoFormat('LLL');
                            ?>
                        </span>
                    </div>
                <?php else : ?>
                    <div style="text-align: center; border: 1px solid #ccc; border-radius: 5px; padding: 25px;">
                        <p>Sua diligência foi enviada.</p>
                        <p>
                            <b>Aguarde a resposta.</b>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
    
<?php endif; ?>

<div class="div-diligence" id="div-diligence">
    <p id="paragraph_info_status_diligence"></p>
</div>
