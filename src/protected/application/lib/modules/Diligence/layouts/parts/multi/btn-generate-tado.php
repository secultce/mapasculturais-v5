<?php 
$opp = $opportunity;
foreach ($opp->getMetadata() as $key => $value) {
   $key == 'use_multiple_diligence' && $value == 'Sim' ? $enableBtn = true : $enableBtn = false;
}
?>
<div>
    <p>
        <br>
        <hr>
    </p>
</div>
<?php
if($enableBtn) {
?>
<p style="text-align: center">
    <button
        class="btn btn-primary"
        title="Gera o relatório TADO"
    >
        Emitir TADO
    </button>
</p>
<?php
  }
?>