<?php
$theme_function = new Theme_Functions();

global $dsp_theme_options;
if ($dsp_theme_options['opt-related-option'] == 'channel') {
    $type = 'channel';
    $id = get_post_meta(get_the_ID(), 'dspro_channel_id', true);
} else {
    $type = 'video';
    $id = $theme_function->first_video_id(get_the_ID());
}

$recommendation_content = $theme_function->get_recommendation_content($type, $id);
?>

<div class="related_content_section">
    <h2><?php echo $dsp_theme_options['opt-related-content-text']; ?></h2>
    <?php
    $width = filter_var($dsp_theme_options['opt-related-image-dimensions']['width'], FILTER_SANITIZE_NUMBER_INT);
    $height = filter_var($dsp_theme_options['opt-related-image-dimensions']['height'], FILTER_SANITIZE_NUMBER_INT);
    ?>
    <div class="slick-wrapper related_content">
        <?php
        $i = 1;
        if (!empty($recommendation_content)):
            foreach ($recommendation_content as $channel):
                ?>
                <div class="slide">
                    <a href="<?php echo $channel['url']; ?>" title="<?php echo $channel['title']; ?>">
                        <div class="slide_image tooltippp" data-tooltip-content="#<?php echo 'tooltip_content_' . $cnt . $i; ?>">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/channel_default_thumbnail.jpg" class="lazy" data-src="<?php echo $channel['image'] . '/' . $width . '/' . $height; ?>" title="<?php echo $channel['title']; ?>" alt="<?php echo $channel['title']; ?>">
                        </div>
                        <!-- Condition to check display the content on tool-tip or below the images-->
                        <?php
                        $title = ($dsp_theme_options['opt-title-trim-word'] != 0) ? wp_trim_words($channel['title'], $dsp_theme_options['opt-title-trim-word'], '...') : $channel['title'];
                        $description = ($dsp_theme_options['opt-description-trim-word'] != 0) ? wp_trim_words($channel['description'], $dsp_theme_options['opt-description-trim-word'], '...') : $channel['description'];
                        ?>
                        <?php if ($dsp_theme_options['opt-layout-slider-content'] == 1): ?>
                            <div class="slide_content">
                                <h6><?php echo $title; ?></h6>
                                <p><?php echo $description; ?></p>
                            </div>
                        <?php else: ?>
                            <div class="tooltip_templates">
                                <span id="<?php echo 'tooltip_content_' . $cnt . $i; ?>">
                                    <h4><?php echo $title; ?></h4>
                                    <p><?php echo $description; ?></p>
                                </span>
                            </div>
                        <?php
                        endif;
                        $i++;
                        ?>
                    </a>
                </div> <!-- slide -->
                <?php
            endforeach;
        endif;
        ?>
    </div><!-- related_content -->
</div><!-- related_content_section -->



