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
    <!-- <a href="<?= $app->createUrl('tado', 'emitir/' . $reg->id); ?>"
        class="btn btn-primary"
        title="Gera o relatório TADO"
    >
        Emitir TADO
    </a> -->
</p>
<?php
  }

    $this->part('multi/multi-select', ['reg' => $reg]);
?>

