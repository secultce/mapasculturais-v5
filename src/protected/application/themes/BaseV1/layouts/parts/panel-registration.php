<?php
use MapasCulturais\Entities\Registration;

$app = MapasCulturais\App::i();

$url = $registration->status == Registration::STATUS_DRAFT ? $registration->editUrl : $registration->singleUrl;
$opportunity = $registration->opportunity;
?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    //organizes registration action buttons and badges into rows
    //add the appropriate classes to your buttons/badges:
    //.registration-panel-button for buttons
    //.registration-panel-badge for badges
    document.querySelectorAll('.registration-actions').forEach(container => {

        const badges = Array.from(
            container.querySelectorAll('.registration-panel-badge')
        );
        const buttons = Array.from(
            container.querySelectorAll('.registration-panel-button')
        );

        // Clear container completely
        container.innerHTML = '';

        // ----- Badges row -----
        if (badges.length) {
            const badgeRow = document.createElement('div');
            badgeRow.className = 'registration-badges-row';

            badgeRow.style.display = 'flex';
            badgeRow.style.flexWrap = 'wrap';
            badgeRow.style.gap = '8px';
            badgeRow.style.marginBottom = '8px';

            badges.forEach(badge => badgeRow.appendChild(badge));
            container.appendChild(badgeRow);
        }

        // ----- Buttons row -----
        if (buttons.length) {
            const buttonRow = document.createElement('div');
            buttonRow.className = 'registration-buttons-row';

            buttonRow.style.display = 'flex';
            buttonRow.style.flexWrap = 'wrap';
            buttonRow.style.gap = '8px';
            buttonRow.style.justifyContent = 'flex-end';

            buttons.forEach(button => buttonRow.appendChild(button));
            container.appendChild(buttonRow);
        }
    });
});
</script>
<?php $this->applyTemplateHook('panel-registration', 'before', [$registration]); ?>
<article class="objeto clearfix">
    <?php $this->applyTemplateHook('panel-registration', 'begin', [$registration]); ?>
    <?php if($avatar = $opportunity->avatar): ?>
    <div class="thumb">
        <img src="<?php echo $avatar->transform('avatarSmall')->url ?>" >
    </div>
    <?php endif; ?>

    <?php $this->applyTemplateHook('panel-registration-title', 'before', [$registration]); ?>
    <h1>
        <?php $this->applyTemplateHook('panel-registration-title', 'begin', [$registration]); ?>
        <a href="<?php echo $url; ?>"><?php echo $registration->number ?> - <?php echo $opportunity->name ?></a>
        <?php $this->applyTemplateHook('panel-registration-title', 'end', [$registration]); ?>
    </h1>
    <?php $this->applyTemplateHook('panel-registration-title', 'after', [$registration]); ?>

    <?php $this->applyTemplateHook('panel-registration-meta', 'before', [$registration]); ?>
    <div class="objeto-meta">
        <?php $this->applyTemplateHook('panel-registration-meta', 'begin', [$registration]); ?>

        <div><span class="label"><?php \MapasCulturais\i::esc_attr_e("Responsável:");?></span> <?php echo $registration->owner->name ?></div>
        <?php
        foreach($app->getRegisteredRegistrationAgentRelations() as $def):
            if(isset($registration->relatedAgents[$def->agentRelationGroupName])):
                $agent = $registration->relatedAgents[$def->agentRelationGroupName][0];
        ?>
        <div><span class="label"><?php echo $def->label ?>:</span> <?php echo $agent->name; ?></div>

        <?php
            endif;
        endforeach;
        ?>
        <?php if($opportunity->registrationCategories): ?>
        <div><span class="label"><?php echo $opportunity->registrationCategTitle ?>:</span> <?php echo $registration->category ?></div>
        <?php endif; ?>
        <?php $this->applyTemplateHook('panel-registration-meta', 'end', [$registration]); ?>
    </div>
    <div class="registration-actions">
        <?php $this->applyTemplateHook('panel-registration-meta', 'after', [$registration]); ?>
    </div>
    <?php $this->applyTemplateHook('panel-registration', 'end', [$registration]); ?>
</article>
<?php $this->applyTemplateHook('panel-registration', 'aft, [$registration]er');


