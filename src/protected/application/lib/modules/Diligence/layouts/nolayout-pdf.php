<!DOCTYPE html>
<html  lang="<?php echo $app->getCurrentLCode(); ?>" dir="ltr">
    <head>
        <meta charset="UTF-8" />
        <link rel="profile" href="http://gmpg.org/xfn/11" />
        <link rel="shortcut icon" href="<?php $this->asset('img/favicon.ico') ?>" />
        <?php //$this->head(isset($entity) ? $entity : null); ?>
        <!--[if lt IE 9]>
        <script src="<?php $this->asset('js/html5.js'); ?>" type="text/javascript"></script>
        <![endif]-->
    </head>

    <body  <?php $this->bodyProperties() ?>>
        <section id="main-section" class="clearfix">
		<?php echo $TEMPLATE_CONTENT;?>
		<?php $this->part('footer', $render_data);?>