<?php 
$app->view->enqueueScript('app', 'diligence', 'js/diligence/proponent.js');

$this->applyTemplateHook('tabs', 'before');
$this->part('diligence/ul-buttons');
?>

<?php $this->applyTemplateHook('tabs', 'after'); ?>