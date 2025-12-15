<?php

$this->layout = 'panel';

$this->applyTemplateHook('view', 'before');
$this->part('counter-argument/view', ['counterArguments' => $counterArguments]);
$this->applyTemplateHook('view', 'after');
