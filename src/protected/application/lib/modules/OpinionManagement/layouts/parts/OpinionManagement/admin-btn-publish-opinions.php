<?php
/** @var \MapasCulturais\Entities\Opportunity $opportunity */
?>
<?php $this->applyTemplateHook('opinions-section','before'); ?>
<section class="opinions-section">
    <h3>Pareceres</h3>
    <div>
        <?php $this->applyTemplateHook('opinions-section.buttons','begin'); ?>
        <button
            class="btn btn-primary"
            onclick="publishOpinions(this)"
            data-id="<?= $opportunity->id ?>"
        ><?= \MapasCulturais\i::__('Publicar Pareceres') ?></button>
        <?php $this->applyTemplateHook('opinions-section.buttons','end'); ?>
    </div>
    <hr>
</section>
<?php $this->applyTemplateHook('opinions-section','after'); ?>