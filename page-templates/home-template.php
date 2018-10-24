<?php
/**
 * Template Name: Homepage Template
 */
global $dsp_theme_options;
get_header();

$theme_function = new Theme_Functions();
$main_carousel = $theme_function->home_page_main_carousel();
?>

<!-- Home page Main carousal section start-->
<div class="row no-gutters">
    <div class="col-sm-12 blog-main">
        <?php if ($main_carousel) { ?>
            <div class="columns slick-wrapper small-12 slider" >
                <?php foreach ($main_carousel as $slide) { ?>
                    <div class="slide">
                        <div class="slide_image">
                            <img src="<?php echo $slide['image'] . '/1920/600'; ?>" title="<?php echo $slide['title']; ?>" alt="<?php echo $slide['title']; ?>">
                        </div>
                        <div class="slide_content">
                            <div class="container">
                                <div class="watch_now">
                                    <a href="<?php echo $slide['url']; ?>" class="btn btn-primary"><i class="el el-arrow-right"></i>Play Now</a>
                                </div>
                                <h2 class="title"><?php echo $slide['title']; ?></h2>
                                <p class="desc"><?php echo $slide['description']; ?></p>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div><!-- /.blog-main -->
</div><!-- no-gutters -->
<!-- Home page Main carousal section end-->

<!-- Home page other carousal section start-->
<div class="row no-gutters">
    <div class="container">
        <div class="other-categories">
            <?php
            $home = get_page_by_path($dsp_theme_options['opt-home-carousel'], OBJECT, 'category');

            $category_args = array(
                'post_type' => 'category',
                'posts_per_page' => -1,
                'post_not_in' => $home->ID,
            );
            $categories = new WP_Query($category_args);

            if ($categories->have_posts()) {
                $cnt = 1;
                $class_array = [];
                foreach ($categories->posts as $category) {
                    $category_slug = $category->post_name;
                    $category_name = $category->post_title;
                    $channels = $theme_function->home_page_other_carousel($category_slug);
                    if ($channels) {
                        ?>
                        <div class="col-sm-12 no-gutters">
                            <h2 class="post-title"><?php echo $category_name; ?></h2>
                            <?php
                            $class = 'home-carousel' . $cnt;
                            $class_array[] = $class;
                            $width = filter_var($dsp_theme_options['opt-image-dimensions']['width'], FILTER_SANITIZE_NUMBER_INT);
                            $height = filter_var($dsp_theme_options['opt-image-dimensions']['height'], FILTER_SANITIZE_NUMBER_INT);
                            ?>
                            <div class="slick-wrapper <?php echo $class ?>">
                                <?php foreach ($channels as $channel) { ?>
                                    <div class="slide">
                                        <div class="slide_image">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/placeholder.jpg" class="lazy" data-src="<?php echo $channel['image'] . '/' . $width . '/' . $height; ?>" title="<?php echo $channel['title']; ?>" alt="<?php echo $channel['title']; ?>">
                                        </div>
                                        <div class="slide_content">
                                            <?php $title =($dsp_theme_options['opt-title-trim-word'] != 0) ? wp_trim_words($channel['title'], $dsp_theme_options['opt-title-trim-word'], '...') : $channel['title']?>
                                            <h6><?php echo $title; ?></h6>
                                            <?php $description =($dsp_theme_options['opt-description-trim-word'] != 0) ? wp_trim_words($channel['description'], $dsp_theme_options['opt-description-trim-word'], '...') : $channel['description']?>
                                            <p><?php echo $description; ?></p>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <?php
                        $cnt++;
                    }
                }
            }
            $theme_function->slick_init_options($class_array);
            ?>
        </div>
    </div><!-- container -->
</div><!-- no-gutters -->
<!-- Home page other carousal section end-->
<?php get_footer(); ?>