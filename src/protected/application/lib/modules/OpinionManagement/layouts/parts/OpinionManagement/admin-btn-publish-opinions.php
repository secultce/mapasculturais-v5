<?php
/** @var \MapasCulturais\Entities\Opportunity $opportunity */
/** @var bool $allEvaluationsSent */
?>
<?php $this->applyTemplateHook('opinions-section','before'); ?>
<section class="opinions-section" ng-controller="OpinionPublicationController as opinions" data-opportunity-id="<?= $opportunity->id ?>" data-manual="<?= $manualPublication ? '1' : '0' ?>">
    <h3>Pareceres</h3>
    <?php if ($manualPublication): ?>
    <p>Selecione na tabela as inscrições cujos pareceres deseja publicar. A publicação libera a consulta aos proponentes e envia as notificações.</p>
    <?php endif; ?>
    <p ng-if="opinions.loading" role="status">Verificando os pareceres das inscrições…</p>
    <p ng-if="opinions.error" class="alert danger" role="alert">{{opinions.error}} <button type="button" class="btn btn-default" ng-click="opinions.refresh()">Tentar novamente</button></p>
    <?php if ($manualPublication): ?>
    <div class="opinions-selection-actions">
        <label><input type="checkbox" ng-checked="opinions.allSelected()" ng-click="opinions.toggleAll()" ng-disabled="opinions.busy || opinions.loading || !opinions.available().length"> Selecionar inscrições disponíveis nesta lista</label>
        <button type="button" class="btn btn-primary" ng-click="opinions.publish()" ng-disabled="opinions.busy || opinions.loading || !opinions.selected().length">Publicar pareceres das selecionadas ({{opinions.selected().length}})</button>
        <span>Até 500 inscrições por publicação. A seleção é limpa ao alterar os filtros.</span>
    </div>
    <?php else: ?>
    <p>Os pareceres serão publicados automaticamente ao publicar o resultado.</p>
    <?php endif; ?>
    <?php if ($manualPublication): ?>
    <div class="opinions-publish-all">
        <?php $this->applyTemplateHook('opinions-section.buttons','begin'); ?>
        <?php if ($allEvaluationsSent): ?>
            <button
                class="btn btn-primary"
                onclick="publishOpinions(this)"
                data-id="<?= $opportunity->id ?>"
            ><?= \MapasCulturais\i::__('Publicar todos os pareceres') ?></button>
        <?php else: ?>
            <span
                class="hltip"
                title="<?php \MapasCulturais\i::esc_attr_e('É necessário aguardar o envio de todas as avaliações antes de publicar os pareceres.') ?>"
            >
                <button
                    class="btn btn-primary disabled"
                    disabled
                    aria-disabled="true"
                ><?= \MapasCulturais\i::__('Publicar todos os pareceres') ?></button>
            </span>
        <?php endif; ?>
        <?php $this->applyTemplateHook('opinions-section.buttons','end'); ?>
    </div>
    <?php endif; ?>
    <hr>
</section>
<?php $this->applyTemplateHook('opinions-section','after'); ?>
