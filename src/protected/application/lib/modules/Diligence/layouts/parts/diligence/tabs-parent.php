<?php

use MapasCulturais\i;
use Diligence\Entities\Diligence as EntityDiligence;



$this->jsObject['isProponent'] = EntityDiligence::isProponent($diligenceRepository, $entity);

$app->view->enqueueScript('app', 'diligence', 'js/diligence/diligence.js');
?>
<script>
    $(document).ready(function() {
               
    });

</script>

<?php 


    $this->applyTemplateHook('tabs', 'before');
    $this->part('diligence/ul-buttons');
?>

<div class="tabs-content">
    <div id="diligence-principal">

    </div>
    <div id="diligence-diligence">
        <?php 
            $this->part('diligence/body-diligence-common', [
                    'entity' => $entity,
                    'diligenceRepository' => $diligenceRepository, 
                    'term' => $term ,
                    'placeHolder' => $placeHolder
                ]); 
        ?>
        <div id="div-info-send" class="div-info-send">
            <p>
                <?php i::_e('Sua diligência já foi enviada') ?>
            </p>
        </div>
        

        <div class="div-btn-send-diligence flex-container">
            <div class="flex-items" id="btn-actions-diligence">
                <?php $this->part('diligence/btn-actions-diligence'); ?>
            </div>
           
        </div>
       
        <script>
          
            function sendFileDiligence() {
                console.log('sendFileDiligence')
            }
        </script>
    </div>
</div>
<?php $this->applyTemplateHook('tabs', 'after'); ?>