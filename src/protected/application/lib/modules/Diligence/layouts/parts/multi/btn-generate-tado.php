<?php 
$enableBtn = false;
foreach ($reg->opportunity->getMetadata() as $key => $value) {
   if($key == 'use_multiple_diligence' && $value == 'Sim')
   {
    $enableBtn = true;
   }
}
?>
<div>
    <p>
        <hr>
    </p>
</div>
<?php
if($enableBtn) {
?>
<p style="text-align: center">
    <a href="<?= $app->createUrl('tado', 'emitir/' . $reg->id); ?>"
        class="btn btn-primary"
        title="Gera o relatório TADO"
    >
        Emitir TADO
    </a>
</p>
<?php
  }
?>
<div style="width: 100%;
    text-align: center;
    margin-top: 10px;" class="form-group-multi">
    <label style="font-weight: bold">Situação do REFO</label>
    <select name="" id="situacion-refo-multi" class="form-control-multi">
        <option value="">-- Selecione --</option>
        <option value="approved">Aprovada</option>
        <option value="partially">Parcialmente aprovada</option>
        <option value="disapproved">Reprovada</option>
    </select>

</div>
