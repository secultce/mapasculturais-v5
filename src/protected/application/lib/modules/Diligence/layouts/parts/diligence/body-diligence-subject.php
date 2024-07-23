<?php
?>

<div class="container-multi-description" id="subject_info_status_diligence">
    <h2 class="multi-title-h2 ">Escrever diligência</h2>
    <div class="box">
        <label class="multi-title-label">Assunto da diligência</label>
        <div class="radio-group">
            <label style="margin-right: 50px;">
                <input type="checkbox" name="assunto[]" <?= $checkPhysical; ?> id="subject_exec_physical">
                Execução física do objeto
            </label>
            <label>
                <input type="checkbox" name="assunto[]" <?= $checkFinance; ?> id="subject_report_finance">
                Relatório financeiro
            </label>
        </div>
    </div>
</div>
