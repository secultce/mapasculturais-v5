<!DOCTYPE html>
<html lang="<?php echo $app->getCurrentLCode(); ?>" dir="ltr">

<head>
    <meta charset="UTF-8" />
    <link rel="profile" href="http://gmpg.org/xfn/11" />
    <link rel="shortcut icon" href="<?php $this->asset('img/favicon.ico') ?>" />
</head>

<body <?php $this->bodyProperties() ?>>
    <section id="main-section" class="clearfix">
        <?php echo $TEMPLATE_CONTENT; ?>
        <?php $this->part('footer', $render_data); ?>
