<?php
/** @var \MapasCulturais\Entities\Opportunity $opportunity */
/** @var bool $allEvaluationsSent */
?>
<?php $this->applyTemplateHook('opinions-section','before'); ?>
<section class="opinions-section">
    <h3>Pareceres</h3>
    <div>
        <?php $this->applyTemplateHook('opinions-section.buttons','begin'); ?>
        <?php if ($allEvaluationsSent): ?>
            <button
                class="btn btn-primary"
                onclick="publishOpinions(this)"
                data-id="<?= $opportunity->id ?>"
            ><?= \MapasCulturais\i::__('Publicar Pareceres') ?></button>
        <?php else: ?>
            <span
                class="hltip"
                title="<?php \MapasCulturais\i::esc_attr_e('É necessário aguardar o envio de todas as avaliações antes de publicar os pareceres.') ?>"
            >
                <button
                    class="btn btn-primary disabled"
                    disabled
                    aria-disabled="true"
                ><?= \MapasCulturais\i::__('Publicar Pareceres') ?></button>
            </span>
        <?php endif; ?>
        <?php $this->applyTemplateHook('opinions-section.buttons','end'); ?>
    </div>
    <hr>
</section>
<?php $this->applyTemplateHook('opinions-section','after'); ?>
