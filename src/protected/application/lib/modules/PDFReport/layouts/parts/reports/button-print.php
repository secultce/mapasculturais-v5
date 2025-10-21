<div class="pdf-report-btn">
    <?php $this->applyTemplateHook('pdf-report-btn', 'before') ?>
    <a href="<?= $app->createUrl('pdf', 'imprimir_inscricao/' . $registration->id); ?>" class="btn btn-default" target="_blank" title="Imprima o formulário em PDF">
        Imprimir em PDF
    </a>
    <?php if ($registration->opportunity->parent) : ?>
        <a href="<?= $app->createUrl('pdf', 'imprimir_inscricao_todas_as_fases/' . $registration->id); ?>" class="btn btn-default" target="_blank" title="Imprima o formulário em PDF (Todas as fases)">
            Imprimir em PDF (Todas as fases)
        </a>
    <?php endif; ?>
    <?php $this->applyTemplateHook('pdf-report-btn', 'after') ?>
</div>
