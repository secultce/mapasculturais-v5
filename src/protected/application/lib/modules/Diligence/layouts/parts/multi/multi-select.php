<?php

use Diligence\Entities\Opinion as OpinionEntity;
use MapasCulturais\App;

$opinion = App::i()->repo(OpinionEntity::class)->findOneBy(['registration' => $reg]);

if (is_null($tado) || ($tado->status == 0)) : ?>
    <div class="form-group-multi">
        <label style="font-weight: 500">Selecione o status da prestação de contas</label>
        <select name="" id="situacion-refo-multi" class="form-control-multi">
            <option value="all" disabled selected>-- Selecione --</option>
            <option value="under_analysis">Em Análise</option>
            <option value="approved">Regular</option>
            <option value="partially">Regular com ressalva</option>
            <option value="disapproved">Irregular</option>
        </select>
    </div>

    <div id="multi-div-btn-status" class="d-none">
        <p class="multi-itens-select">
            <a
                href="<?= $app->createUrl('refo', 'report/' . $reg->id); ?>" target="_blank" class="btn btn-default"
                title="Gera o relatório para o financeiro analisar"
                style="display: block;">
                <i class="fas fa-solid fa-file-pdf"></i>
                Gerar relatório para Financeiro
            </a>
        </p>

        <p class="multi-itens-select">
            <a
                class="btn btn-default send js-open-editbox hltip" data-target="#import-financial-report"
                title="Importar Relatório Financeiro" style="display: block;">
                Importar Relatório Financeiro
            </a>
        </p>

        <?php if ($opinion && $opinion->status === OpinionEntity::STATUS_ENABLED) : ?>
            <p class="multi-itens-select" id="p-btn-tado">
                <a
                    href="<?= $app->createUrl('tado', 'emitir/' . $reg->id); ?>" id="btn-generate-tado" target="_blank"
                    class="btn btn-primary" title="Gera o relatório TADO" style="display: block;">
                    Finalizar e emitir TADO
                </a>
            </p>
        <?php endif; ?>
    </div>
<?php endif; ?>
