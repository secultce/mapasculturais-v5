<?php
/**
 * @var array<string, mixed> $agent
 * @var array<string> $quotas
 *
 */
?><div class="quotas-set__quotas-container">
    Esse agente contém associação com as políticas:
    <?php foreach ($agent['quotas_policy'] as $quota):

        $endDate = Carbon\Carbon::create($quota['end_date'])->locale('pt-BR')->isoFormat('ll'); ?>
        <div class="quotas-set__quota">
            <span class="quotas-set__quota-name"><?= $quota['quotas_policy']['name'] ?> até</span>
            <strong class="quotas-set__quota-value"><?= $endDate ?></strong>
        </div>
    <?php endforeach; ?>
</div>
<div class="assign_container">
    <hr>
    <div>A data de término ficará: <output name="quotas_policy_end_date" ></output></div>
    <strong>Associar nova política: </strong>
    <select name="quotas_policy">
        <?php foreach ($quotas as $quota):
            if (in_array($quota['id'], array_column(array_column($agent['quotas_policy'], 'quotas_policy'), 'id')) || $quota['id'] == 9):
                continue;
            endif; ?>
            <option value="<?= $quota['id'] ?>"><?= $quota['name'] ?></option>
        <?php endforeach; ?>
    </select>
    <button class="btn btn-default" id="quotas-set__associate" onclick="assignQuota(<?= $agent['id'] ?>)">Associar</button>
</div>

<script>
    const dateOptions = {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    };

    const assignQuota = (agentId) => {
        const racialQuotaId = $('[name="quotas_policy"]').val()
        $.ajax({
            url: `/api/agent/assignQuota`,
            method: 'POST',
            data: JSON.stringify({
                agent_id: agentId,
                quota_id: racialQuotaId,
                start_date: new Date().toISOString().split('T')[0]
            }),
            contentType: 'application/json',
            success: (response) => {
                console.log(response);
                MapasCulturais.Messages.success('Associado com sucesso');
                location.reload();
            },
            error: (error) => {
                MapasCulturais.Messages.error('Erro ao associar política. Recarregue a página e tente novamente!');
                console.error(error);
            }
        });
    }

    const quotas = <?= json_encode($quotas) ?>;
    $('[name="quotas_policy"]').on('change', function() {
        const currentSelectedQuotaId = $(this).val();
        const validityDuration = quotas.find(quota => quota.id == currentSelectedQuotaId).validity_duration;
        const d = new Date();
        const year = d.getFullYear() + validityDuration;
        const month = d.getMonth();
        const day = d.getDate();
        let endDate = new Date(year, month, day).toLocaleDateString('pt-BR', dateOptions);
        $('[name="quotas_policy_end_date"]').text(endDate);
    });
    $('[name="quotas_policy"]').trigger('change');
</script>
