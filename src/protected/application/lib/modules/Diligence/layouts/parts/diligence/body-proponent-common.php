<?php

use MapasCulturais\i;
use Carbon\Carbon;
use Diligence\Entities\Diligence as EntityDiligence;
use Diligence\Entities\AnswerDiligence;
use Diligence\Repositories\Diligence as DiligenceRepo;

$descriptionDraft = true;

?>

<p id="paragraph_loading_content">
    <label for="">
        Carregando ... <img id="img-loading-content" />
    </label>
    <br />
    <br />
</p>

<?php
if ($diligenceAndAnswers) :
?>
    <h5>
        <?php
            i::_e('Diligência enviada a você');
        ?>
    </h5>
    <div style="margin-top: 25px;">
        <?php if (!is_null($diligenceAndAnswers[0]) && $diligenceAndAnswers[0]->status == EntityDiligence::STATUS_SEND) : ?>
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
        <?php endif; ?>
        <?php if (!is_null($diligenceAndAnswers[1]) && $diligenceAndAnswers[1]->status == AnswerDiligence::STATUS_SEND) : ?>
            <div style="font-size: 14px; background-color: #F5F5F5; padding: 10px;">
                <label>
                    <b>Minha resposta:</b>
                </label>
                <p style="margin: 10px 0px;">
                    <?php
                        echo $diligenceAndAnswers[1]->answer;
                    ?>
                </p>
                <?php
                    $files = DiligenceRepo::getFilesDiligence($diligenceAndAnswers[1]->diligence->id);

                    foreach ($files as $file) {
                        echo '
                            <p style="margin-bottom: 10px;">
                                <a href="/arquivos/privateFile/' . $file["id"] . '" target="_blank" rel="noopener noreferrer">
                                    ' . $file["name"] . '
                                </a>
                            </p>
                        ';
                    }
                ?>
                <span style="font-size: 12px; font-weight: 700; color: #404040;">
                    <?php
                        echo Carbon::parse($diligenceAndAnswers[1]->createTimestamp)->isoFormat('LLL');
                    ?>
                </span>
            </div>
        <?php endif; ?>
    </div>
    
<?php endif; ?>

<div class="div-diligence" id="div-diligence">
    <p id="paragraph_info_status_diligence"></p>
</div>
