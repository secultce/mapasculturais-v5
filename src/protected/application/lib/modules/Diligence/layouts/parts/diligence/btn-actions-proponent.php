<div class="widget">
    <!-- <h3 class="editando">Enviar arquivo(s)</h3>
    <a ng-click="editbox.open('id-da-caixa', $event)" title="Anexar arquivo para sua resposta">
        <i class="fas fa-paperclip"></i>
        Anexo 01
    </a>
    <edit-box id="id-da-caixa" position="right" title="Upload do primeiro arquivo" spinner-condition="data.processando" cancel-label="Fechar" on-open="" on-cancel="" close-on-cancel='true'>
        <?php
        $url = $app->createUrl('diligence', 'upload', ['entity' => $entity->id]);

        ?>
        <form method="post" action="/diligence/upload/id:99" enctype="multipart/form-data">
            <input type="file" name="download" />
            <input type="text" name="description[downloads]" />
            <button type="submit" onclick="sendFileDiligence()" class="submit-attach-opportunity">Enviar</button>
        </form>
    </edit-box> -->
    <button class="btn-send-diligence mr-10" title="Salva o conteúdo mas não envia sua resposta"
        id="btn-save-diligence-proponente" onclick="saveAnswerProponente(0)"
    >
        Salvar
        <i class="fas fa-save"></i>
    </button>
    <button id="btn-send-diligence-proponente" class="btn-send-diligence" title="Salva e envia a sua resposta para a comissão avaliadora." onclick="">
        Enviar
        <i class="fas fa-paper-plane"></i>
    </button>




</div>