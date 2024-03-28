<?php use MapasCulturais\i; ?>
<script>
     $(document).ready(function () {
        $("#btn-save-diligence").hide();
        $("#label-save-content-diligence").hide();
        $("#div-info-send").hide();
        $("#div-content-all-diligence-send").hide();
        $("#descriptionDiligence").show();
        if($(this).val() > 0){
            $("#btn-save-diligence").show();
        }
        $("#paragraph_info_status_diligence").html('A sua Diligência ainda não foi enviada');
        getContentDiligence();
        $("#descriptionDiligence").on("keyup", function() {
        var texto = $(this).val(); // Obtém o valor do textarea
            if(texto.slice(-1) == '')
            {
                $("#btn-save-diligence").hide()
            }else{
                $("#btn-save-diligence").show()
            }
        });
    });
</script>
<?php $this->applyTemplateHook('tabs','before'); ?>
<ul class="abas clearfix">
    <li class="active">
        <a href="#diligence-principal" rel="noopener noreferrer" onclick="showRegistration()"
         id="tab-main-content-diligence-principal">Ficha</a>
    </li>
    <li class="">
        <a href="#diligence-diligence" rel="noopener noreferrer" onclick="hideRegistration()" id="tab-main-content-diligence-diligence">Diligência</a>
    </li>
</ul>
<div class="tabs-content">
    <div id="diligence-principal">
       
    </div>
    <div id="diligence-diligence">
        <label>
            <strong>
                <?php \MapasCulturais\i::_e('Diligência ao proponente') ?>
            </strong>
        </label>
        <div class="div-diligence" id="div-diligence">
            <p id="paragraph_info_status_diligence"></p>            
        </div>
        <div style="margin-top: 30px; width: 100%;" id="div-content-all-diligence-send">
            <label class="label-diligence-send">Diligência:</label>
            <p id="paragraph_content_send_diligence"></p>
            <p id="paragraph_createTimestamp" class="paragraph-createTimestamp"></p>
            <div style="width: 100%; display: flex;justify-content: space-between;flex-wrap: wrap; ">
                <div class="item-col"></div>
                    <div class="item-col" style="padding: 8px;">
                        <p>
                         <?php \MapasCulturais\i::_e('O Proponente tem apenas ' .
                         $entity->opportunity->getMetadata('diligence_days') .
                         ' dias para responder essa diligência.'); ?>
                        </p>
                    </div>
                <div class="item-col"></div>
            </div>
            <div id="div-info-send" class="div-info-send">
                <p>
                    <?php \MapasCulturais\i::_e('Sua diligência já foi enviada') ?>                    
                </p>
            </div>
        </div>
        <div>
            <textarea name="description" id="descriptionDiligence" cols="30" rows="10"
                placeholder="Escreva aqui a sua diligência"
                class="diligence-context-open"
            ></textarea>
        </div>
        <div style="" class="div-btn-send-diligence">
        <label class="diligence-label-save" id="label-save-content-diligence">
            <i class="fas fa-check-circle mr-10"></i>
            Suas alterações foram salvas
        </label>
            <button 
                class="btn-send-diligence mr-10"
                title="Salva o conteúdo mas não envia para o proponente"
                id="btn-save-diligence"
                onclick="saveDiligence(0)"
            >
                Salvar
                <i class="fas fa-save"></i>
            </button>
            <button 
                id="btn-send-diligence"
                class="btn-send-diligence"
                title="Salva e envia para o proponente"
                onclick="saveDiligence(3)"
            >
                Enviar
                <i class="fas fa-paper-plane"></i>
            </button>
           
        </div>
        <script>
            function hideRegistration() {
                $("#registration-content-all").hide();
            }
            function showRegistration() {
                $("#registration-content-all").show();
            }
            function saveDiligence(status)
            {
                $.ajax({
                    type: "POST",
                    url: MapasCulturais.createUrl('diligence', 'save'),
                    data: {
                        registration: MapasCulturais.entity.id,
                        openAgent: MapasCulturais.userProfile.id,
                        agent: MapasCulturais.entity.ownerId,
                        createTimestamp: moment().format("YYYY-MM-DD"),
                        description: $("#descriptionDiligence").val(),
                        status: status,
                    },
                    dataType: "json",
                    success: function (res) {
                        if(res.status == 200) {
                            $("#label-save-content-diligence").show()
                            setTimeout(() => {
                                $("#label-save-content-diligence").hide()
                            }, 2000);
                       }
                    },
                    error: function(err) {
                        console.log({err})
                    }
                });
            }
            function getContentDiligence()
            {
                $.ajax({
                    type: "GET",
                    url: MapasCulturais.createUrl('diligence', 'getcontent/'+MapasCulturais.entity.id),
                    dataType: "json",
                    success: function (res) {
                        console.log(res);
                        if(res.status == 200){
                            $("#descriptionDiligence").val(res.data.description)
                            $("#btn-save-diligence").show();                            
                        }
                        if(res.data.status == 3){
                            $("#paragraph_info_status_diligence").html('');
                            $("#paragraph_content_send_diligence").html(res.data.description);
                            $("#paragraph_createTimestamp").html(moment(res.data.createTimestamp.date).format("LLL"));
                            $("#div-diligence").hide();
                            $("#descriptionDiligence").hide();
                            $("#btn-save-diligence").hide();
                            $("#btn-send-diligence").hide();
                            $("#div-info-send").show();
                            $("#div-content-all-diligence-send").show();
                        }
                    }
                });
            }
         
           
        </script>
    </div>
</div>
<?php $this->applyTemplateHook('tabs','after'); ?>