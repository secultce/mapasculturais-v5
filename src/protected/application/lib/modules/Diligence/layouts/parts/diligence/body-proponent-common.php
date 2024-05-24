<?php

use MapasCulturais\i;
use Diligence\Entities\Diligence as EntityDiligence;
use Diligence\Entities\AnswerDiligence;
use Carbon\Carbon;

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
            i::_e('Diligências recebidas');
        ?>
    </h5>
    <div style="margin-top: 25px;">
        <?php if (!is_null($diligenceAndAnswers[0]) && $diligenceAndAnswers[0]->status == 3) : ?>
            <div style="font-size: 14px; padding: 10px; margin-bottom: 10px;">
                <label>
                    <b>Diligência (atual):</b>
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
        <?php if (!is_null($diligenceAndAnswers[1]) && $diligenceAndAnswers[1]->status == 3) : ?>
            <div style="font-size: 14px; background-color: #F5F5F5; padding: 10px;">
                <label>
                    <b>Minha resposta:</b>
                </label>
                <p style="margin: 10px 0px;">
                    <?php
                        echo $diligenceAndAnswers[1]->answer;
                    ?>
                </p>
                <span style="font-size: 12px; font-weight: 700; color: #404040;">
                    <?php
                        echo Carbon::parse($diligenceAndAnswers[1]->createTimestamp)->isoFormat('LLL');
                    ?>
                </span>
            </div>
        <?php endif; ?>
    </div>
    <?php 
    if(!is_null($diligenceAndAnswers) && count($diligenceAndAnswers) > 2) : ?>
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
        foreach ($diligenceAndAnswers as $key => $resultsDiligence) :
            Carbon::setLocale('pt_BR');
            $dt = null;
            $dtSend = "";

            if ($resultsDiligence !== null) {
                $dt             = Carbon::parse($resultsDiligence->sendDiligence);
                $dtSend         = $dt->isoFormat('LLL');
                
            }
            
            if($key > 1) {
                if ($resultsDiligence instanceof EntityDiligence && !is_null($resultsDiligence) && $resultsDiligence->status == 3) { ?>
                    <div style="display: flex; justify-content: space-between;" class="div-accordion-diligence">
                        <label style="font-size: 14px">
                            <b>Diligência:</b>
                        </label>
                        <label style="color: #085E55; font-size: 14px" class="title-hide-show-accordion">Visualizar <i class="fas fa-angle-down arrow"></i></label>
                    </div>
                    <div class="content">
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
                if ($resultsDiligence instanceof AnswerDiligence && !is_null($resultsDiligence) && $resultsDiligence->status == 3) {
                    $dtAnswer       = Carbon::parse($resultsDiligence->createTimestamp);
                    $dtSendAnswer   = $dtAnswer->isoFormat('LLL');
                    
                ?>
                    <div style="display: flex; justify-content: space-between;" class="div-accordion-diligence">
                        <label style="font-size: 14px">
                            <b>Resposta:</b>
                        </label>
                        <label style="color: #085E55; font-size: 14px" class="title-hide-show-accordion">Visualizar <i class="fas fa-angle-down arrow"></i></label>
                    </div>
                    <div class="content" style="background-color: #F3F3F3;">
                        <p>
                            <?php
                            echo $resultsDiligence->answer;
                            
                            ?>
                        </p>
                        <p class="paragraph-createTimestamp paragraph_createTimestamp_answer">
                            <?php echo $dtSendAnswer;   ?>
                        </p>
                    </div>                
                <?php
                }
            }
            /* if (is_null($resultsDiligence))
            {
                $descriptionDraft = false;
                echo ' <div style="display: flex; justify-content: space-between;" class="div-accordion-diligence">
                    <label style="font-size: 14px">Aguardando Resposta.</label>
                    <label style="color: #085E55; font-size: 14px" class="title-hide-show-accordion">Visualizar <i class="fas fa-angle-down arrow"></i></label>
                </div><div class="content"><p>Aguardando respostasss</p></div>';
                
            } */
        //     if ($resultsDiligence instanceof AnswerDiligence && !is_null($resultsDiligence) && $resultsDiligence->status == 0)
        //     {
        //         $descriptionDraft = false;
        //         echo ' <div style="display: flex; justify-content: space-between;" class="div-accordion-diligence">
        //         <label style="font-size: 14px">Aguardando Resposta.</label>
        //         <label style="color: #085E55; font-size: 14px" class="title-hide-show-accordion">Visualizar <i class="fas fa-angle-down arrow"></i></label>
        //     </div><div class="content"><p>Aguardando resposta</p></div>';            
        // }
       
        if($key > 1) {
            // echo '<hr/>';
         }
        endforeach; ?>
        </div>
   
    <?php 
    endif;//endif id=accordion
    ?>
    
<?php endif; ?>

<div class="div-diligence" id="div-diligence">
    <p id="paragraph_info_status_diligence"></p>
</div>
