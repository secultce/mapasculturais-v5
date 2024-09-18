<?php

use MapasCulturais\App;

$opportunity = $registration->opportunity;
$url = $registration->singleUrl . '#/tab=diligence-principal';

?>

<article class="objeto clearfix">
    <?php if ($avatar = $opportunity->avatar): ?>
        <div class="thumb">
            <img src="<?php echo $avatar->transform('avatarSmall')->url ?>">
        </div>
    <?php endif; ?>

    <h1>
        <a href="<?php echo $url; ?>"><?php echo $registration->number ?> - <?php echo $opportunity->name ?></a>
    </h1>

    <div class="objeto-meta">
        <div>
            <span class="label"><?php \MapasCulturais\i::esc_attr_e("Responsável:"); ?></span> <?php echo $registration->owner->name ?>
        </div>
        <?php
        foreach (App::i()->getRegisteredRegistrationAgentRelations() as $def):
            if (isset($registration->relatedAgents[$def->agentRelationGroupName])):
                $agent = $registration->relatedAgents[$def->agentRelationGroupName][0];
        ?>
                <div><span class="label"><?php echo $def->label ?>:</span> <?php echo $agent->name; ?></div>

        <?php
            endif;
        endforeach;
        ?>
        <?php if ($opportunity->registrationCategories): ?>
            <div><span class="label"><?php echo $opportunity->registrationCategTitle ?>:</span> <?php echo $registration->category ?></div>
        <?php endif; ?>
    </div>
</article>
