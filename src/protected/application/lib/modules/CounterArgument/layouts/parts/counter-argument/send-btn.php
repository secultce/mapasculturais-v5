<?php if ($isCounterArgumentPeriod && !$hasCounterArgument) : ?>
    <a class="btn btn-primary registration-panel-button" data-registration="<?= $registration->id ?>" open-counter-argument>
        Abrir Contrarrazão
    </a>
<?php elseif ($hasCounterArgument) : ?>
    <span class="badge badge-info registration-panel-badge">Contrarrazão enviada</span>
<?php endif; ?>
