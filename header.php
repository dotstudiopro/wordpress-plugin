<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php
        global $dsp_theme_options;
        if (isset($dsp_theme_options['opt-favicon-url']['url'])) {
            echo '<link rel="shortcut icon" href="' . $dsp_theme_options['opt-favicon-url']['url'] . '" />';
        }
        ?>
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
                <script src="<?php echo esc_url(get_template_directory_uri()); ?>/bootstrap/js/html5.min.js"></script>
                <script src="<?php echo esc_url(get_template_directory_uri()); ?>/bootstrap/js/respond.min.js"></script>
                <![endif]-->
        <?php wp_head(); ?>
    </head>

    <body <?php theme_body_class(); ?>>
        <?php
        $back = $dsp_theme_options['opt-back-to-top'];
        if ($back) {
            ?>
            <a href="javascript:" id="return-to-top"><i class="fa fa-arrow-up"></i></a>
            <?php
        }
        $header_align = $dsp_theme_options['opt-logo-align'];
        get_template_part('page-templates/templates-part/header/' . $header_align . '-align');

        