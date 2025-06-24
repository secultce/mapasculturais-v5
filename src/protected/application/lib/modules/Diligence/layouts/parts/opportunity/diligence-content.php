<div id="diligences" class="aba-content">
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
                <select id="evaluator-filter" class="select-filter dropdown">
                    <option value="0" selected>Avaliador</option>
                    <?php /** @var \MapasCulturais\Entities\Agent[] $evaluators */
                    foreach ($evaluators as $evaluator): ?>
                    <option value="<?= $evaluator->id ?>" class="dropdown"><?= $evaluator->name ?></option>
                    <?php endforeach; ?>
                </select>
            </th>
            <th ng-if="data.entity.registrationCategories" class="registration-option-col">
                <mc-select placeholder="<?php \MapasCulturais\i::esc_attr_e("Categoria"); ?>" model="evaluationsFilters['registration:category']" data="data.registrationCategoriesToFilter"></mc-select>
            </th>

            <th class="registration-agents-col">
                <?php \MapasCulturais\i::_e("Agente Responsável");?>
            </th>
            <th class="registration-status-col">
                <?php \MapasCulturais\i::_e('Tipo de Diligência');?>
            </th>
            <th class="registration-status-col">
                <select id="status-filter" class="select-filter dropdown">
                    <option value="placeholder" selected disabled>Status</option>
                    <option value="all">Todos</option>
                    <option value="3">Enviado ao proponente</option>
                    <option value="4">Respondido</option>
                    <option value="10">TADO gerado</option>
                </select>
            </th>
            <th class="registration-status-col">
                <div id="subject-filter" class="select-filter dropdown">
                    <span>Assunto</span>
                    <div class="subject-options">
                        <label>
                            <input type="checkbox" name="subject-filter" value="Execução Física do Objeto.">
                            <?php \MapasCulturais\i::_e('Execução Física do Objeto');?>
                        </label>
                        <label>
                            <input type="checkbox" name="subject-filter" value="Relatório Financeiro.">
                            <?php \MapasCulturais\i::_e('Relatório Financeiro');?>
                        </label>
                    </div>
                </div>
            </th>
            <?php $this->applyTemplateHook('opportunity-diligences--table-thead-tr','end'); ?>
        </tr>
        <?php $this->applyTemplateHook('opportunity-diligences--table-thead','end'); ?>
        </thead>
        <?php $this->applyTemplateHook('opportunity-diligences--table-thead','after'); ?>

        <?php $this->applyTemplateHook('opportunity-diligences--table-tbody','before'); ?>
        <tbody>
        <?php $this->applyTemplateHook('opportunity-diligences--table-tbody','begin'); ?>

        <?php
            $this->applyTemplateHook('opportunity-diligences--table-tbody-tr','begin');

            /** @var array{
             *     array{
             *      registration: \MapasCulturais\Entities\Registration,
             *      diligences: array<\Diligence\Entities\Diligence>
             *     }
             * } $registrationsWithDiligences
             */
            foreach ($registrationsWithDiligences as $dataGroup):
                $registration = $dataGroup['registration'];
                foreach ($dataGroup['diligences'] as $diligence):
                   
        ?>
        <tr 
            data-evaluator-filter="<?= $diligence->openAgent->id ?>" 
            data-status-filter="<?= $diligence->status ?>"
            data-subject-filter="<?= $diligence->getSubject() ?>"
        >
            <?php $this->applyTemplateHook('opportunity-diligences--table-tbody-tr','before'); ?>
            <td class="registration-id-col">
                <a href="<?= $registration->singleUrl . 'uid:' . $diligence->openAgent->user->id ?>" rel='noopener
                noreferrer'>
                    <strong><?= $registration->number ?></strong>
                </a>
            </td>
            <td class="registration-id-col">
                <?= $diligence->openAgent->name ?>
            </td>
            <td ng-if="data.entity.registrationCategories" class="registration-option-col">
                <?= $registration->category ?>
            </td>
            <td class="registration-agents-col">
                <p>
                    <span class="label"><?php \MapasCulturais\i::_e("Responsável");?></span><br />
                    <a href="<?= $registration->owner->singleUrl ?>" rel='noopener noreferrer'>
                        <?= $registration->owner->name ?>
                    </a>
                </p>
            </td>
            <td class="registration-status-col">
                <?= $registration->opportunity->use_multiple_diligence === 'Sim' ? 'Múltipla' : 'Simples' ?>
            </td>
            <td class="registration-status-col">
                <?= $diligence->getStatusLabel() ?>
                <?= $diligence->status == 3 ? '<br>' . $diligence->createTimestamp->format('d-m-Y H:i:s') : '' ?>
            </td>
            <td class="registration-status-col">
                <?= $diligence->getSubject(); ?>
            </td>
            <?php $this->applyTemplateHook('opportunity-diligences--table-tbody-tr','end'); ?>
        </tr>

        <?php endforeach;
        endforeach; ?>

        <?php $this->applyTemplateHook('opportunity-diligences--table-tbody-tr','after'); ?>
        <?php $this->applyTemplateHook('opportunity-diligences--table-tbody','end'); ?>
        </tbody>
        <?php $this->applyTemplateHook('opportunity-diligences--table-tbody','after'); ?>
    </table>
</div>
