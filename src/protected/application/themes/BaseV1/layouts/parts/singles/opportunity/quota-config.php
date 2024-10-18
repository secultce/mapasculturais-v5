<?php

use MapasCulturais\Utils;

$hasSecultSeal = Utils::checkUserHasSeal(env('SECULT_SEAL_ID'));

?>

<?php if ($hasSecultSeal && $entity->evaluationMethodConfiguration->type->id === 'technical' && $this->isEditable()): ?>
    <div ng-class="{'registration-fieldset': data.isEditable}">
        <h4>
            <?php \MapasCulturais\i::_e("Configuração de Cotas"); ?>
        </h4>

        <p>
            <span class="label">Será disponibilizado vagas para cotistas:</span>
            <span
                class="js-editable"
                data-edit="hasVacanciesForQuotaHolders"
                data-value="<?= $entity->getMetadata('hasVacanciesForQuotaHolders') ?>">
            </span>
        </p>
        <p>
            <span class="label">Número de vagas da oportunidade:</span>
            <span
                class="js-editable"
                data-edit="numberVacancies"
                data-value="<?= $entity->getMetadata('numberVacancies') ?>">
            </span>
        </p>
        <p class="registration-help">
            <?php \MapasCulturais\i::_e("Esse número será usado para calcular a quantidade de vagas para cotistas."); ?>
        </p>
    </div>
<?php endif; ?>
