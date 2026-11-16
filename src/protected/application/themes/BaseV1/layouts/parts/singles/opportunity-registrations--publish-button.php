<?php if ($entity->canUser('@control')): ?>
    <?php
    $registrationsOpen = $entity->registrationTo >= new \DateTime();
    $hasPendingEvaluations = !$registrationsOpen
        && $entity->autopublishOpinions === 'Sim'
        && class_exists(\OpinionManagement\Module::class)
        && \OpinionManagement\Module::isEvaluationMethodValid($entity)
        && !\OpinionManagement\Module::areAllEvaluationsSent($entity);
    ?>
    <?php if ($entity->publishedRegistrations): ?>
        <div class="clearfix">
            <p class='alert success'><?php \MapasCulturais\i::_e("O resultado já foi publicado");?></p>
        </div>
    <?php else: ?>
        <div class="clearfix">
            <?php if ($entity->canUser('publishRegistrations')): ?>
                <a id="btn-publish-results" class="btn btn-primary" href="javascript:void(0)" onclick="alertPublish('<?=$entity->id?>')"><?php \MapasCulturais\i::_e("Publicar resultados");?></a>
            <?php else: ?>
                <a
                    id="btn-publish-results"
                    class="btn btn-primary disabled hltip"
                    title="<?php
                        if ($registrationsOpen) {
                            \MapasCulturais\i::esc_attr_e('Você só pode publicar a lista de aprovados após o término do período de inscrições.');
                        } elseif ($hasPendingEvaluations) {
                            \MapasCulturais\i::esc_attr_e('É necessário aguardar o envio de todas as avaliações antes de publicar os resultados.');
                        } else {
                            \MapasCulturais\i::esc_attr_e('Você não tem permissão para publicar os resultados desta oportunidade.');
                        }
                    ?>"
                ><?php \MapasCulturais\i::_e("Publicar resultados");?></a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>
