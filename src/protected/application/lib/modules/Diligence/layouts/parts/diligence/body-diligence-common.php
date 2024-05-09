<?php

use MapasCulturais\i;
use MapasCulturais\App;
use Diligence\Entities\Diligence as EntityDiligence;
use Diligence\Entities\AnswerDiligence;
use Carbon\Carbon;
$app = App::i();
        //Verificando se tem resposta para se relacionar a diligencia
        $dql = "SELECT ad, d
        FROM  Diligence\Entities\Diligence d 
        LEFT JOIN  Diligence\Entities\AnswerDiligence ad WITH ad.diligence = d
        WHERE d.registration = :reg 
        and ad.registration = :regAnswer" ;
         $query = $app->em->createQuery($dql)->setParameters(['reg' => $entity->id, 'regAnswer' => $entity->id]);

$result = $query->getResult();
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
        i::_e('Diligência ao proponente');
        dump($result);
        ?>
    </strong>
</label>
<div class="div-diligence" id="div-diligence">
    <p id="paragraph_info_status_diligence"></p>
</div>
<div style="margin-top: 30px; width: 100%;" id="div-content-all-diligence-send">
    <label class="label-diligence-send">Diligência:</label>
    <!-- <p id="paragraph_content_send_diligence"></p>
    <p id="paragraph_createTimestamp" class="paragraph-createTimestamp"></p> -->
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
<div id="accordion" class="head">
    <?php 
   
    foreach($result as $key => $results):
        Carbon::setLocale('pt_BR');
        $dt = Carbon::parse($results->sendDiligence);
        if($results instanceof EntityDiligence)
        {
    ?>
        <div style="display: flex; justify-content: space-between;" id="div-accordion-diligence" class="div-accordion-diligence">
            <label style="font-size: 14px">Diligência </label>
            <label style="color: #085E55; font-size: 14px" class="title-hide-show-accordion">Visualizar <i class="fas fa-angle-down arrow"></i></label>
        </div>
    <div class="content">
      <p>
        <?php 
                echo $results->description;
          
            ?>
      </p>
      <p id="paragraph_createTimestamp_answer" class="paragraph-createTimestamp">
        <?php echo $dt->isoFormat('LLL');   ?>
      </p>
    </div>
    <?php 
        }

        if($results instanceof AnswerDiligence)
        {
    ?>
        <div style="display: flex; justify-content: space-between;" id="div-accordion-diligence" class="div-accordion-diligence">
            <label style="font-size: 14px">
                <strong>Resposta Recebida</strong>
            </label>
            <label style="color: #085E55; font-size: 14px" class="title-hide-show-accordion">Visualizar <i class="fas fa-angle-down arrow"></i></label>
        </div>
    <div class="content" style="background-color: #F3F3F3;">
      <p>
        <?php 
                echo $results->answer;
          
            ?>
      </p>
      <p id="paragraph_createTimestamp_answer" class="paragraph-createTimestamp">
        <?php echo $dt->isoFormat('LLL');   ?>
      </p>
    </div>
    <?php 
        }
        
endforeach; ?>

    <!-- <h3>Section 1</h3>
  <div>
    <p>Mauris mauris ante, blandit et, ultrices a, suscipit eget, quam. Integer ut neque. Vivamus nisi metus, molestie vel, gravida in, condimentum sit amet, nunc. Nam a nibh. Donec suscipit eros. Nam mi. Proin viverra leo ut odio. Curabitur malesuada. Vestibulum a velit eu ante scelerisque vulputate.</p>
  </div>
  <h3>Section 2</h3>
  <div>
    <p>Sed non urna. Donec et ante. Phasellus eu ligula. Vestibulum sit amet purus. Vivamus hendrerit, dolor at aliquet laoreet, mauris turpis porttitor velit, faucibus interdum tellus libero ac justo. Vivamus non quam. In suscipit faucibus urna. </p>
  </div>
  <h3>Section 3</h3>
  <div>
    <p>Nam enim risus, molestie et, porta ac, aliquam ac, risus. Quisque lobortis. Phasellus pellentesque purus in massa. Aenean in pede. Phasellus ac libero ac tellus pellentesque semper. Sed ac felis. Sed commodo, magna quis lacinia ornare, quam ante aliquam nisi, eu iaculis leo purus venenatis dui. </p>
    <ul>
      <li>List item one</li>
      <li>List item two</li>
      <li>List item three</li>
    </ul>
  </div>
  <h3>Section 4</h3>
  <div>
    <p>Cras dictum. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Aenean lacinia mauris vel est. </p><p>Suspendisse eu nisl. Nullam ut libero. Integer dignissim consequat lectus. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. </p>
  </div> -->


  </div>

<div>
    <textarea name="description" id="descriptionDiligence" cols="30" rows="10" placeholder="<?= $placeHolder; ?>" class="diligence-context-open"></textarea>
</div>
<label class="diligence-label-save" id="label-save-content-diligence">
    <i class="fas fa-check-circle mr-10"></i>
    Suas alterações foram salvas
</label>

<?php endif; ?>
