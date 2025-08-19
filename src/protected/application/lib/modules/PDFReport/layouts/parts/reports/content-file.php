<?php

$controllerId = $app->getControllerIdByEntity("MapasCulturais\Entities\File");
$id = 0;
$name = '';

echo $iniSpan;

if (!empty($fields['config'])) {
    foreach ($fields['config'] as $conf) {
        $id = $conf['id'];
        $name = $conf['name'];
        $url = $app->createUrl($controllerId, 'privateFile', [$id]);

        echo '<a href="' . $url . '" class="my-reg-font-10"> ' . $name . '</a> <br />';
    }
} else {
    echo '<span class="my-reg-font-10">Não informado</span>';
}

echo $endSpan;
