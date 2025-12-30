<?php if ($isCounterArgumentPeriod && !$hasCounterArgument) : ?>
    <a class="btn btn-primary" data-registration="<?= $registration->id ?>" open-counter-argument>
        Abrir Contrarrazão
    </a>
<?php elseif ($hasCounterArgument) : ?>
    <span class="badge badge-info">Contrarrazão enviada</span>
<?php endif; ?>
