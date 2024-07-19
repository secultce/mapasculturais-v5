<?php

use MapasCulturais\i;
use Diligence\Entities\Diligence as EntityDiligence;
use Diligence\Entities\AnswerDiligence;
use Carbon\Carbon;
use Diligence\Repositories\Diligence as DiligenceRepo;

$descriptionDraft = true;

if ($diligenceAndAnswers) {
    $diligencesSent = array_filter($diligenceAndAnswers, function($value, $key) use ($diligenceAndAnswers) {
        return ($key % 2 != 0 && $diligenceAndAnswers[$key-1]->status == EntityDiligence::STATUS_SEND) || ($key % 2 == 0 && !is_null($value) && $value->status == EntityDiligence::STATUS_SEND);
    }, ARRAY_FILTER_USE_BOTH);
    $diligencesSentReindexed = array_values($diligencesSent);
}

?>

<p id="paragraph_loading_content">
    <label for="">
        Carregando ... <img id="img-loading-content" />
    </label>
    <br />
    <br />
</p>

<?php
if (isset($diligencesSentReindexed)) :
?>
    <h5>
        <?php
            i::_e('Diligências recebidas');
        ?>
    </h5>
    <div style="margin-top: 25px;">
        <?php if (isset($diligencesSentReindexed[0]) && $diligencesSentReindexed[0]->status == EntityDiligence::STATUS_SEND) : ?>
            <div style="font-size: 14px; padding: 10px; margin-bottom: 10px;">
                <label>
                    <b>Diligência (atual):</b>
                </label> <br>
                <label for="">
                    <?php $diligenceAndAnswers > 0 ? $diligenceAndAnswers[0]->getSubject() : ""; ?>
                </label>
                <p style="margin: 10px 0px;">
                    <?php
                        echo $diligencesSentReindexed[0]->description;
                    ?>
                </p>
                <span style="font-size: 12px; font-weight: 700; color: #404040;">
                    <?php
                        echo Carbon::parse($diligencesSentReindexed[0]->sendDiligence)->isoFormat('LLL');
                    ?>
                </span>
            </div>
        <?php endif; ?>
        <?php if (isset($diligencesSentReindexed[1]) && $diligencesSentReindexed[1]->status == AnswerDiligence::STATUS_SEND) : ?>
            <div style="font-size: 14px; background-color: #F5F5F5; padding: 10px;">
                <label>
                    <b>Minha resposta:</b>
                </label>
                <p style="margin: 10px 0px;">
                    <?php
                        echo $diligencesSentReindexed[1]->answer;
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
                        echo Carbon::parse($diligencesSentReindexed[1]->createTimestamp)->isoFormat('LLL');
                    ?>
                </span>
            </div>
        <?php endif; ?>
    </div>
    <?php 
    if(!is_null($diligencesSentReindexed) && count($diligencesSentReindexed) > 2) : ?>
        <div style="display: flex;
            justify-content: center;
            margin-top: 10px;
            margin-bottom: 10px;
            color: green;">
    
            <p style="color: #085E55; font-weight: 700; font-size: 14px;">
                Mensagens mais antigas
            </p>
            <p>
                <br>
            </p>
        </div>

        <div id="accordion" class="head">
            
        <?php     
        foreach ($diligencesSentReindexed as $key => $resultsDiligence) :
            Carbon::setLocale('pt_BR');
            $dt = null;
            $dtSend = "";

            if ($resultsDiligence !== null) {
                $dt             = Carbon::parse($resultsDiligence->sendDiligence);
                $dtSend         = $dt->isoFormat('LLL');
                
            }
            
            if($key > 1) {
                if ($resultsDiligence instanceof EntityDiligence && !is_null($resultsDiligence) && $resultsDiligence->status == EntityDiligence::STATUS_SEND) { ?>
                    <div style="display: flex; justify-content: space-between;" class="div-accordion-diligence">
                        <label style="font-size: 14px">
                            <b>Diligência:</b>
                        </label>
                        <label style="color: #085E55; font-size: 14px" class="title-hide-show-accordion">Visualizar <i class="fas fa-angle-down arrow"></i></label>
                    </div>
                    <div class="content">
                        <p>
                            <label for="">
                                <?php echo $resultsDiligence->getSubject(); ?>
                            </label>
                        </p>
                        <p>

                            <?php
                            echo $resultsDiligence->description;
                            ?>
                        </p>
                        <p class="paragraph-createTimestamp paragraph_createTimestamp_answer">
                            <?php echo $dtSend; ?>
                        </p>
                    </div>
                <?php
                }
                if ($resultsDiligence instanceof AnswerDiligence && !is_null($resultsDiligence) && $resultsDiligence->status == AnswerDiligence::STATUS_SEND) {
                    $dtAnswer       = Carbon::parse($resultsDiligence->createTimestamp);
                    $dtSendAnswer   = $dtAnswer->isoFormat('LLL');
                    
                ?>
                    <div style="display: flex; justify-content: space-between;" class="div-accordion-diligence">
                        <label style="font-size: 14px">
                            <b>Resposta:</b>
                        </label>
                        <label style="color: #085E55; font-size: 14px" class="title-hide-show-accordion">Visualizar <i class="fas fa-angle-down arrow"></i></label>
                    </div>
                    <div class="content" style="font-size: 14px; background-color: #F5F5F5; padding: 10px;">
                        <p style="margin: 10px 0px;">
                            <?php
                            echo $resultsDiligence->answer;
                            ?>
                        </p>
                        <?php
                            $files = DiligenceRepo::getFilesDiligence($resultsDiligence->diligence->id);

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
                            <?php echo $dtSendAnswer;   ?>
                        </span>
                    </div>                
                <?php
                }
            }

        endforeach; ?>
        </div>

    <?php endif; ?>
    
<?php endif; ?>

<div class="div-diligence" id="div-diligence">
    <p id="paragraph_info_status_diligence"></p>
</div>
