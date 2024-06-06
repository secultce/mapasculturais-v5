<table class="js-registration-list registrations-table"><!-- adicionar a classe registrations-results quando resultados publicados-->
    <?php $this->applyTemplateHook('opportunity-diligences--table-thead','before'); ?>
    <thead>
    <?php $this->applyTemplateHook('opportunity-diligences--table-thead','begin'); ?>

    <tr>
        <?php $this->applyTemplateHook('opportunity-diligences--table-thead-tr','begin'); ?>

        <th class="registration-id-col">
            <?php \MapasCulturais\i::_e("Inscrição");?>
        </th>
        <th class="registration-id-col">
            <mc-select placeholder="<?php \MapasCulturais\i::esc_attr_e("Avaliador"); ?>" model="evaluationsFilters['valuer:id']" data="data.evaluationCommittee"></mc-select>
        </th>
        <th ng-if="data.entity.registrationCategories" class="registration-option-col">
            <mc-select placeholder="<?php \MapasCulturais\i::esc_attr_e("Categoria"); ?>" model="evaluationsFilters['registration:category']" data="data.registrationCategoriesToFilter"></mc-select>
        </th>

        <th class="registration-agents-col">
            <?php \MapasCulturais\i::_e("Agente Responsável");?>
        </th>
        <th class="registration-status-col">
            <mc-select placeholder="<?php \MapasCulturais\i::esc_attr_e("Status"); ?>" model="evaluationsFilters['status']" data="data.evaluationStatuses"></mc-select>
        </th>
        <th class="registration-status-col">
            <?php \MapasCulturais\i::esc_attr_e("Avaliação"); ?>
        </th>

        <?php $this->applyTemplateHook('opportunity-diligences--table-thead-tr','end'); ?>
    </tr>
    <?php $this->applyTemplateHook('opportunity-diligences--table-thead','end'); ?>
    </thead>
    <?php $this->applyTemplateHook('opportunity-diligences--table-thead','after'); ?>

    <?php $this->applyTemplateHook('opportunity-diligences--table-tbody','before'); ?>
    <tbody>
    <?php $this->applyTemplateHook('opportunity-diligences--table-tbody','begin'); ?>
    <tr>
        <td colspan='10'>
            <span ng-if="data.evaluations.length === 0"><?php \MapasCulturais\i::_e("Nenhuma avaliação enviada.");?></span>
            <span ng-if="data.evaluations.length === 1"><?php \MapasCulturais\i::_e("1 avaliação encontrada.");?></span>
            <span ng-if="data.evaluations.length > 1">{{data.evaluations.length}}
                    <span ng-if="data.evaluationsAPIMetadata.count > 0">
                        <i> de {{ data.evaluationsAPIMetadata.count }}</i>
                    </span>
                 <?php \MapasCulturais\i::_e("Avaliações");?>
             </span>
        </td>
    </tr>

    <?php $this->applyTemplateHook('opportunity-diligences--table-tbody-tr','begin'); ?>
    <tr ng-repeat="evaluation in data.evaluations" id="registration-{{evaluation.registration.id}}" >
        <?php $this->applyTemplateHook('opportunity-diligences--table-tbody-tr','before'); ?>

        <td class="registration-id-col">
            <a href='{{evaluation.evaluation.singleUrl || evaluation.registration.singleUrl}}' rel='noopener noreferrer'>
                <strong>{{evaluation.registration.number}}</strong>
            </a>
        </td>
        <td class="registration-id-col">{{evaluation.valuer.name}}</td>
        <td ng-if="data.entity.registrationCategories" class="registration-option-col">{{evaluation.registration.category}}</td>
        <td class="registration-agents-col">
            <p>
                <span class="label"><?php \MapasCulturais\i::_e("Responsável");?></span><br />
                <a href="{{evaluation.registration.owner.singleUrl}}" rel='noopener noreferrer'>{{evaluation.registration.owner.name}}</a>
            </p>
        </td>
        <td class="registration-status-col">
            {{getEvaluationStatusLabel(evaluation)}}
        </td>
        <td class="registration-status-col">
            {{getEvaluationResultString(evaluation)}}
        </td>
        <?php $this->applyTemplateHook('opportunity-diligences--table-tbody-tr','end'); ?>
    </tr>
    <?php $this->applyTemplateHook('opportunity-diligences--table-tbody-tr','after'); ?>
    <?php $this->applyTemplateHook('opportunity-diligences--table-tbody','end'); ?>
    </tbody>
    <?php $this->applyTemplateHook('opportunity-diligences--table-tbody','after'); ?>
</table>
<?php
