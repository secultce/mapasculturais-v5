<?php
$map_latitude = '-5.058114374355702';
$map_longitude = '-39.4134521484375';
$map_zoom = '7';

return [
    'maps.includeGoogleLayers' => true,
    'maps.center' => array($map_latitude, $map_longitude),
    'maps.zoom.default' => $map_zoom,
];