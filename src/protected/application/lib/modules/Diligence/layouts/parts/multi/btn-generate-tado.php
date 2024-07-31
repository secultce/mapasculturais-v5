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
    $this->part('multi/multi-select', ['reg' => $reg]);
?>

