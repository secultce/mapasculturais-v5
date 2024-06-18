<?php $this->applyTemplateHook('nav.panel.accountability', 'before'); ?>
<li>
    <a <?php if ($this->template == 'panel/accountabilitys') echo 'class="active"'; ?> href="<?php echo $app->createUrl('panel', 'accountability') ?>">
        <span class="icon icon-opportunity"></span>
        <?php \MapasCulturais\i::_e("Minhas prestações de contas"); ?>
    </a>
</li>
<?php $this->applyTemplateHook('nav.panel.accountability', 'after'); ?>
