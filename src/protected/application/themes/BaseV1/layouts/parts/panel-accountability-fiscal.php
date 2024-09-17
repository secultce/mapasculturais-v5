<?php

$registration = ['from' => $opportunity->registrationFrom, 'to' => $opportunity->registrationTo];
$registration_dates = [];

if ($registration['from'] instanceof DateTime) {
    $registration_dates['from'] = $registration['from']->format('d/m/Y');
}

if ($registration['to'] instanceof DateTime) {
    $registration_dates['to'] = $registration['to']->format('d/m/Y');
}

?>

<article class="objeto clearfix">
    <?php if ($avatar = $opportunity->avatar): ?>
        <div class="thumb" style="background-image: url(<?php echo $avatar->transform('avatarSmall')->url; ?>)"></div>
    <?php else: ?>
        <div class="thumb"></div>
    <?php endif; ?>

    <h1>
        <a href="<?= $opportunity->singleUrl ?>"><?php echo $opportunity->name; ?></a>
    </h1>

    <div class="objeto-meta">
        <div>
            <span class="label">Tipo:</span> <?php echo $opportunity->type->name ?>
        </div>
        <?php if (is_array($registration) && ($registration['from'] || $registration['to'])): ?>
            <div>
                <span class="label"><?php \MapasCulturais\i::_e("Inscrições:"); ?></span>

                <?php
                if ($opportunity->isRegistrationOpen())
                    \MapasCulturais\i::_e("Abertas ");

                if ($registration['from'] && !$registration['to'])
                    echo \MapasCulturais\i::__("a partir de ") . $registration_dates['from'];
                elseif (!$registration['from'] && $registration['to'])
                    echo \MapasCulturais\i::__(' até ') . $registration_dates['to'];
                else
                    echo \MapasCulturais\i::__('de ') . $registration_dates['from'] . \MapasCulturais\i::__(' a ') . $registration_dates['to'];
                ?>
            </div>
        <?php endif; ?>

        <div>
            <span class="label"><?php \MapasCulturais\i::_e("Organização:"); ?></span> <?php echo $opportunity->owner->name; ?>
        </div>
        <?php if ($opportunity->originSiteUrl): ?>
            <div>
                <span class="label">Url: </span><?php echo $opportunity->originSiteUrl; ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="entity-actions">
        <a class="btn btn-small btn-primary" href="<?php echo $opportunity->singleUrl; ?>#/tab=evaluations">
            <?php \MapasCulturais\i::_e("Visualizar prestações de contas"); ?>
        </a>
    </div>
</article>
