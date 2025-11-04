<div class="opportunity-claim-button" style="padding-top: 0.5rem">
        <a class="btn btn-success"
            href="<?php echo $baseUrl . 'painel/inscricoes/' . $entity->id .'#tab=enviadas'?>"
            title="Será redirecionado para contrarrazão"
        >
            Ir para página contrarrazão.
        </a>
</div>
<script>
    $(function() {
        $(".opportunity-claim-box").hide();
    });
</script>
