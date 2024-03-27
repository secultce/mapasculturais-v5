<?php use MapasCulturais\i; ?>
<script>
     $(document).ready(function () {
        $("#btn-save-diligence").hide();
        $("#label-save-content-diligence").hide();
        if($(this).val() > 0){
            $("#btn-save-diligence").show();
        }
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
        <h2>Ficha</h2>
    </div>
    <div id="diligence-diligence">
        <label>
            <strong>Diligência ao proponente</strong>
        </label>
        <div style="" class="div-diligence">
            <p style="">
                A sua Diligência ainda não foi enviada
            </p>
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
                onclick="saveDiligence()"
            >
                Salvar
                <i class="fas fa-save"></i>
            </button>
            <button 
                class="btn-send-diligence"
                title="Salva e envia para o proponente"
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
            function saveDiligence()
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
                        status: 0,
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
                        if(res.status == 200){
                            $("#descriptionDiligence").val(res.message)
                            $("#btn-save-diligence").show();
                        }
                    }
                });
            }
           
        </script>
    </div>
</div>
<?php $this->applyTemplateHook('tabs','after'); ?>