<?php

/**
 * @var array<string, mixed> $agent
 */
?>
<div class="quotas-set__quotas-container">
    <p>Esse agente contém associação com as políticas:</p><br>
    <?php if (empty($agent['quotas_policy'])): ?>
        <b>Não há políticas associadas ao agente</b>
    <?php else: ?>
        <?php foreach ($agent['quotas_policy'] as $quota):
            $endDate = Carbon\Carbon::create($quota['end_date'])->locale('pt-BR')->isoFormat('ll'); ?>
            <div class="quotas-set__quota">
                <span class="quotas-set__quota-name"><?= $quota['quotas_policy']['name'] ?> até</span>
                <strong class="quotas-set__quota-value"><?= $endDate ?></strong>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
