<?php

use MapasCulturais\i;
use Diligence\Entities\Diligence as EntityDiligence;
use Diligence\Entities\AnswerDiligence;
use Carbon\Carbon;

$descriptionDraft = true;
if (!$sendEvaluation) :
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
            i::_e('Diligências enviadas:');
            ?>
        </strong>
    </label>
    <div class="div-diligence" id="div-diligence">
        <p id="paragraph_info_status_diligence"></p>
    </div>
    <div id="accordion-2" class="head">
        <div style="display: flex; justify-content: space-between;" class="diligence-active div-accordion-diligence">
            <label style="font-size: 14px">
                Diligência
            </label>
        </div>
        <div class="content" style="background-color: #F3F3F3;">
            <p>
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Cum, totam libero. 
                Explicabo, itaque eius non expedita iure, magnam voluptatibus rerum rem porro,
                    aut commodi nam excepturi maiores ducimus corporis amet?
            </p>
        </div>
        <div style="display: flex; justify-content: space-between;" class="div-accordion-diligence">
            <label style="font-size: 14px">
                <strong>Resposta Recebida</strong>
            </label>
        </div>
        <div class="content" style="background-color: #F3F3F3;">
            <p>
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Cum, totam libero. 
                Explicabo, itaque eius non expedita iure, magnam voluptatibus rerum rem porro,
                    aut commodi nam excepturi maiores ducimus corporis amet?
            </p>
        </div>
    </div>
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
    <?php 
    // dump($diligenceAndAnswers);
    if(!is_null($diligenceAndAnswers)) : ?>
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
                        <label style="font-size: 14px">Diligência </label>
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
                            <strong>Resposta Recebida</strong>
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
            if (is_null($resultsDiligence))
            {
                $descriptionDraft = false;
                echo ' <div style="display: flex; justify-content: space-between;" class="div-accordion-diligence">
                    <label style="font-size: 14px">Aguardando Resposta.</label>
                    <label style="color: #085E55; font-size: 14px" class="title-hide-show-accordion">Visualizar <i class="fas fa-angle-down arrow"></i></label>
                </div><div class="content"><p>Aguardando respostasss</p></div>';
                
            }
            if ($resultsDiligence instanceof AnswerDiligence && !is_null($resultsDiligence) && $resultsDiligence->status == 0)
            {
                $descriptionDraft = false;
                echo ' <div style="display: flex; justify-content: space-between;" class="div-accordion-diligence">
                <label style="font-size: 14px">Aguardando Resposta.</label>
                <label style="color: #085E55; font-size: 14px" class="title-hide-show-accordion">Visualizar <i class="fas fa-angle-down arrow"></i></label>
            </div><div class="content"><p>Aguardando resposta</p></div>';            
        }
       
        if($key > 1) {
            // echo '<hr/>';
         }
        endforeach; ?>
        </div>
   
    <?php 
    endif;//endif id=accordion
    ?>
    
<?php endif; ?>