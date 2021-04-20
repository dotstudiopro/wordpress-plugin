<?php
global $dsp_theme_options, $client_token, $post, $wp, $share_banner, $share_desc, $share_title;
$video_slug = '';

$channel_meta = get_post_meta(get_the_ID());

$company_id = "";
$chnl_id = isset($channel_meta['chnl_id'][0]) ? $channel_meta['chnl_id'][0] : '';
$dspro_channel_id = isset($channel_meta['dspro_channel_id'][0]) ? $channel_meta['dspro_channel_id'][0] : '';

$share_title = $title = get_the_title();
$share_desc = $desc =  apply_filters('the_content', get_post_field('post_content', get_the_ID()));
//$share_banner = $poster = ($dsp_theme_options['opt-channel-poster-type'] == 'poster') ? $channel_meta['chnl_poster'][0] : $channel_meta['chnl_spotlight_poster'][0];
if($dsp_theme_options['opt-channel-poster-type'] == 'poster'){
   $share_banner = $poster = $channel_meta['chnl_poster'][0];
}
elseif($dsp_theme_options['opt-channel-poster-type'] == 'spotlight_poster'){
    $share_banner = $poster = $channel_meta['chnl_spotlight_poster'][0];
}
else{
    $share_banner = $poster = $channel_meta['chnl_wallpaper'][0];
}

get_header();

if (have_posts()) {

    while (have_posts()) : the_post();

        $theme_function = new Theme_Functions();
        // Code to check if user subscribe to watch this channel
        $dsp_api = new Dsp_External_Api_Request();
        $country_code = $dsp_api->get_country();
        $dspro_channel_geo = unserialize($channel_meta['dspro_channel_geo'][0]);
        if($country_code && !in_array("ALL", $dspro_channel_geo) && !in_array($country_code, $dspro_channel_geo) && !empty($dspro_channel_geo)){
            ?>
            <div class="custom-container container pb-5">
                <div class="row no-gutters other-categories text-center">
                    <h4 class="p-4 w-100">The owner of this content has made it unavailable in your country.</h4>
                    <h4 class="p-4 w-100">Please explore our other selections from <a href="/home-page" title="Explore">here</a></h4>
                </div>
            </div>
            <?php
            break;
        }

        if (isset($channel_meta['chnl_categories'][0])) {
            $categories = array_filter(explode(',', $channel_meta['chnl_categories'][0]));
            $chnl_title = get_the_title();

            // Condition to check platform is web true or not for this channel category
            $plateform_web = false;
            foreach ($categories as $channel_cat) {
                $args = array('name' => $channel_cat, 'post_type' => 'channel-category');
                $cache_key = "single_video_categories_" . $channel_cat;
                $slug_query = $theme_function->query_categories_posts($args, $cache_key);
                if ($slug_query) {
                    $plateform_web = true;
                    break;
                }
            }
        } else {
            $plateform_web = true;
        }

        if ($plateform_web) {
            $check_subscription_status = $dsp_api->check_subscription_status($client_token, $channel_meta['dspro_channel_id'][0]);
            if (!is_wp_error($check_subscription_status) && empty($check_subscription_status['unlocked']))
                $parant_channel_unlocked = false;
            else
                $parant_channel_unlocked = true;

            $svod_products = array();
            if (class_exists('Dotstudiopro_Subscription')) {
                $dsp_subscription_object = new Dotstudiopro_Subscription_Request();
                $check_product_by_channel = $dsp_subscription_object->getProductsByChannel($channel_meta['dspro_channel_id'][0]);
                if (!is_wp_error($check_product_by_channel) && !empty($check_product_by_channel['products'])){
                   $svod_products = array_values(array_filter($check_product_by_channel['products'], function($cp) {
                        return $cp && !empty($cp['product_type']) && $cp['product_type'] === 'svod';
                    }));
                }
            }

            $childchannels = $theme_function->is_child_channels(get_the_ID());
            //$channel_banner_image = ($dsp_theme_options['opt-channel-poster-type'] == 'poster') ? $channel_meta['chnl_poster'][0] : $channel_meta['chnl_spotlight_poster'][0];
            if($dsp_theme_options['opt-channel-poster-type'] == 'poster'){
               $channel_banner_image = $poster = $channel_meta['chnl_poster'][0];
            }
            elseif($dsp_theme_options['opt-channel-poster-type'] == 'spotlight_poster'){
                $channel_banner_image = $poster = $channel_meta['chnl_spotlight_poster'][0];
            }
            else{
                $channel_banner_image = $poster = $channel_meta['chnl_wallpaper'][0];
            }
            $banner = ($channel_banner_image) ? $channel_banner_image : 'https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1';
            $first_child_id = get_post(get_the_ID(), OBJECT);
			$p_channel_id = 0;
            if (!empty($first_child_id)) {
                $p_channel_meta = get_post_meta($first_child_id->ID);
                $p_channel_id = $p_channel_meta['chnl_id'][0];
                if(!isset($p_channel_meta['chnl_child_channels'][0]) && empty($p_channel_meta['chnl_child_channels'][0])){
                    $p_channel_id = 0;
                }
            }
            if ($childchannels) {
                $first_child_id = get_page_by_path($childchannels[0], OBJECT, 'channel');
                $channel_videos = $theme_function->get_channel_videos($first_child_id->ID);
                $videoSlug = ($channel_videos[0]['slug']) ? $channel_videos[0]['slug'] : $channel_videos[0]['_id'];
                $first_video_url = get_site_url() . '/channel/' . $post->post_name . '/' . $first_child_id->post_name . '/video/' . $videoSlug;
            } else {
                $channel_videos = $theme_function->get_channel_videos(get_the_ID());
                $videoSlug = ($channel_videos[0]['slug']) ? $channel_videos[0]['slug'] : $channel_videos[0]['_id'];
                $first_video_url = get_site_url() . '/channel/' . $post->post_name . '/video/' . $videoSlug;
            }
            ?>

            <!-- Channel Banner image or video section start -->
            <div class="chnl inner-banner-bg">
                <!-- Channel Banner image section start -->
                <div class="chanl_background_img">
                    <div class="inner-banner-img"><img src="<?php echo $banner . '/1920/900'; ?>" alt="<?php echo get_the_title(); ?>"></div>
                    <div class="inner-banner-content_bg">
                        <div class="inner-banner-content row no-gutters">
                            <?php if($dsp_theme_options['opt-channel-poster-logo-title'] == 'logo' && !empty($channel_meta['chnl_logo'][0])){?>
                                <img class="title_logo pb-3" src="<?php echo $channel_meta['chnl_logo'][0]. '/400'; ?>" alt="<?php echo get_the_title(); ?>">
                            <?php }else{ ?>
                            <h2><?php echo get_the_title(); ?></h2>
                            <?php }?>
                            <p class="w-100 pb-3"><?php echo dsp_get_channel_publication_meta(get_the_ID()); ?></p>
                            <?php the_content(); ?>
                            <div class="subscribe_now mt-3">
                                <?php if (!empty($svod_products) && empty($parant_channel_unlocked)): ?>
                                    <a href="/packages" class="btn btn-secondary btn-ds-secondary">Subscribe Now</a>
                                <?php elseif(!empty($parant_channel_unlocked)): ?>
                                    <a href="<?php echo $first_video_url; ?>" class="btn btn-secondary btn-ds-secondary">Watch Now</a>
                                <?php endif; ?>
                            </div>
                            <div class="more_ways_to_watch_now ml-2 mt-3 mr-2">
                                <?php 
                                 if (empty($parant_channel_unlocked) && class_exists('Dotstudiopro_Subscription')) {
                                    $subscription_fornt_object = new Dotstudiopro_Subscription_Front('dotstudiopro-subscription', '1.1.0');
                                    $subscription_fornt_object->show_more_ways_to_watch($dspro_channel_id);

                                }
                                ?>
                            </div>
                            <?php if (class_exists('WP_Auth0_Options')) { ?>
                                <div class="my_list_button mt-3">
                                    <?php
                                    if ($first_child_id) {
                                        if ($client_token) {
                                            $channel_id = get_post_meta($first_child_id->ID, 'chnl_id', true);
                                            $obj = new Dsp_External_Api_Request();
                                            $list_channel = $obj->get_user_watchlist($client_token);
                                            $in_list = array();
                                            if (!is_wp_error($list_channel) && $list_channel['channels'] && !empty($list_channel['channels'])) {
                                                foreach ($list_channel['channels'] as $ch) {
                                                    $in_list[] = $ch['_id'];
                                                }
                                            }
                                            if (in_array($channel_id, $in_list)) { // $channel->isChannelInList($utoken)
                                                ?>
                                                <a href="#" class="btn btn-danger manage_my_list" data-channel_id="<?php echo $channel_id; ?>" data-parent_channel_id="<?php echo $p_channel_id; ?>" data-action="removeFromMyList" data-nonce="<?php echo wp_create_nonce('removeFromMyList'); ?>"><i class="fa fa-minus-circle"></i> Remove from My List</a>
                                            <?php } else { ?>
                                                <a href="#" class="btn btn-secondary btn-ds-secondary manage_my_list" data-channel_id="<?php echo $channel_id; ?>" data-parent_channel_id="<?php echo $p_channel_id; ?>" data-action="addToMyList" data-nonce="<?php echo wp_create_nonce('addToMyList'); ?>"><i class="fa fa-plus-circle"></i> Add to My List</a>
                                                <span data-nonce="<?php echo wp_create_nonce('removeFromMyList'); ?>" style="display: none;"></span>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <a href="<?php echo wp_login_url(home_url($wp->request)); ?>" class="btn btn-secondary btn-ds-secondary"><i class="fa fa-plus-circle"></i> Add to My List</a>
                                            <?php
                                        }
                                    }
                                    ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <!-- Channel Banner image section end -->

                <!-- Display video insted of background image if user is ideal section start-->
                <?php
                $trailer_id = '';
                $video_id = $theme_function->first_video_id(get_the_ID());
                if (!empty($video_id)) {
                    $video = $dsp_api->get_video_by_id($video_id);
                    if (!is_wp_error($video) && !empty($video)) {
                        if (isset($video['teaser_trailer']) && !empty($video['teaser_trailer'])) {
                            $trailer_id = $video['teaser_trailer']['_id'];
                            $company_id = isset($video['company_id']) ? $video['company_id'] : '';
                            $mute_on_load = (get_option('dsp_video_muteload_field')) ? true : false;
                            ?>
                            <div id="video-overlay" class="channel-teaser">
                                <div class="player" data-video_id="<?php echo $trailer_id; ?>"></div>
                                <div class="inner-banner-content_bg channel-teaser-info">
                                    <div class="inner-banner-content row no-gutters">
                                        <?php if($dsp_theme_options['opt-channel-poster-logo-title'] == 'logo' && !empty($channel_meta['chnl_logo'][0])){?>
                                            <img class="title_logo pb-3" src="<?php echo $channel_meta['chnl_logo'][0]. '/400'; ?>" alt="<?php echo get_the_title(); ?>">
                                        <?php }else{ ?>
                                        <h2><?php echo get_the_title(); ?></h2>
                                        <?php }?>
                                        <p class="w-100 pb-3"><?php echo dsp_get_channel_publication_meta(get_the_ID()); ?></p>
                                        <?php the_content(); ?>
                                        <div class="subscribe_now mt-3">
                                            <?php if (!empty($svod_products) && empty($parant_channel_unlocked)): ?>
                                                <a href="/packages" class="btn btn-secondary btn-ds-secondary">Subscribe Now</a>
                                            <?php elseif(!empty($parant_channel_unlocked)): ?>
                                                <a href="<?php echo $first_video_url; ?>" class="btn btn-secondary btn-ds-secondary">Watch Now</a>
                                            <?php endif; ?>
                                        </div>
                                        <div class="more_ways_to_watch_now ml-2 mt-3 mr-2">
                                            <?php 
                                             if (empty($parant_channel_unlocked) && class_exists('Dotstudiopro_Subscription')) {
                                                $subscription_fornt_object = new Dotstudiopro_Subscription_Front('dotstudiopro-subscription', '1.1.0');
                                                $subscription_fornt_object->show_more_ways_to_watch($dspro_channel_id);

                                            }
                                            ?>
                                        </div>
                                        <?php if (class_exists('WP_Auth0_Options')) { ?>
                                            <div class="my_list_button mt-3">
                                                <?php
                                                if ($first_child_id) {
                                                    if ($client_token) {
                                                        $channel_id = get_post_meta($first_child_id->ID, 'chnl_id', true);
                                                        $obj = new Dsp_External_Api_Request();
                                                        $list_channel = $obj->get_user_watchlist($client_token);
                                                        $in_list = array();
                                                        if (!is_wp_error($list_channel) && $list_channel['channels'] && !empty($list_channel['channels'])) {
                                                            foreach ($list_channel['channels'] as $ch) {
                                                                $in_list[] = $ch['_id'];
                                                            }
                                                        }
                                                        if (in_array($channel_id, $in_list)) { // $channel->isChannelInList($utoken)
                                                            ?>
                                                            <a href="#" class="btn btn-danger manage_my_list" data-channel_id="<?php echo $channel_id; ?>" data-parent_channel_id="<?php echo $p_channel_id; ?>" data-action="removeFromMyList" data-nonce="<?php echo wp_create_nonce('removeFromMyList'); ?>"><i class="fa fa-minus-circle"></i> Remove from My List</a>
                                                        <?php } else { ?>
                                                            <button class="btn btn-primary btn-revry-primary manage_my_list" data-channel_id="<?php echo $channel_id; ?>" data-parent_channel_id="<?php echo $p_channel_id; ?>" data-action="addToMyList" data-nonce="<?php echo wp_create_nonce('addToMyList'); ?>"><i class="fa fa-plus-circle"></i> Add to My List</button>
                                                            <span data-nonce="<?php echo wp_create_nonce('removeFromMyList'); ?>" style="display: none;"></span>
                                                        <?php } ?>
                                                    <?php } else { ?>
                                                        <a href="<?php echo wp_login_url(home_url($wp->request)); ?>" class="btn btn-primary btn-revry-primary"><i class="fa fa-plus-circle"></i> Add to My List</a>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    }
                }
                ?>
                <!-- Display video insted of background image if user is ideal section end-->
            </div>
            <!-- Channel Banner image or video section end -->

            <div class="custom-container container pb-5">
                <div class="row no-gutters other-categories">
                    <?php
                    if (!$childchannels) {
                        $videos = $theme_function->show_videos($post, 'other_carousel');
                        $cnt = 0;
                        $channel_unlocked = $parant_channel_unlocked;
                        if ($videos) {
                            ?>
                            <!-- Single Channel Video section start -->
                            <div class="col-sm-12 no-gutters pt-7">
                                <h3 class="post-title mb-5"><?php echo get_the_title(); ?></h3>
                                <?php
                                $class = 'home-carousel' . $cnt;
                                $class_array[] = $class;
                                if( $dsp_theme_options['opt-channel-video-image-size'] == '0' ) {
                                    $width = filter_var($dsp_theme_options['opt-channel-video-image-dimensions']['width'], FILTER_SANITIZE_NUMBER_INT);
                                    $height = filter_var($dsp_theme_options['opt-channel-video-image-dimensions']['height'], FILTER_SANITIZE_NUMBER_INT);
                                } else {
                                    $width = filter_var($dsp_theme_options['opt-channel-video-image-width']['width'], FILTER_SANITIZE_NUMBER_INT);

                                    $ratio_width = filter_var($dsp_theme_options['opt-channel-video-image-aspect-ratio']['width'], FILTER_SANITIZE_NUMBER_INT);
                                    $ratio_height = filter_var($dsp_theme_options['opt-channel-video-image-aspect-ratio']['height'], FILTER_SANITIZE_NUMBER_INT);

                                    $ratio = $ratio_height / $ratio_width;
                                }
                                include(locate_template('page-templates/templates-part/channel-videos.php'));
                                ?>

                            </div>
                            <!-- Single Channel Video section end -->
                            <?php
                        }
                    } else {
                        $p_channel_slug = $post->post_name;
                        $cnt = 0;
                        foreach ($childchannels as $channel) {
                            $channel_unlocked = '';
                            $single_channel = get_page_by_path($channel, OBJECT, 'channel');
                            $single_channel_meta = get_post_meta($single_channel->ID);
                            // $check_subscription_status_single = $dsp_api->check_subscription_status($client_token, $single_channel_meta['dspro_channel_id'][0]);
                            // if (!is_wp_error($check_subscription_status_single) && empty($check_subscription_status_single['unlocked']))
                            //     $channel_unlocked = false;
                            // else
                            //     $channel_unlocked = true;
                            if (!is_wp_error($check_subscription_status) && !empty($check_subscription_status['childchannels'])){
                                $checkIfChannelExists = array_search($single_channel_meta['dspro_channel_id'][0], array_column($check_subscription_status['childchannels'],'dspro_id'));
                                if($checkIfChannelExists || $checkIfChannelExists == 0){
                                    if (empty($check_subscription_status['childchannels'][$checkIfChannelExists]['unlocked']))
                                        $channel_unlocked = false;
                                    else
                                        $channel_unlocked = true;
                                }
                                else{
                                    $channel_unlocked = true;
                                }
                            }
                            else{
                                $channel_unlocked = true;
                            }
                            $videos = $theme_function->show_videos($single_channel, 'other_carousel', null, $p_channel_slug);
                            if ($videos) {
                                ?>
                                <!-- Single Channel Video section start -->
                                <div class="col-sm-12 no-gutters pt-7">
                                    <h3 class="post-title mb-5"><?php echo $single_channel->post_title; ?></h3>
                                    <?php
                                    $class = 'home-carousel' . $cnt;
                                    $class_array[] = $class;
                                    if( $dsp_theme_options['opt-channel-video-image-size'] == '0' ) {
                                        $width = filter_var($dsp_theme_options['opt-channel-video-image-dimensions']['width'], FILTER_SANITIZE_NUMBER_INT);
                                        $height = filter_var($dsp_theme_options['opt-channel-video-image-dimensions']['height'], FILTER_SANITIZE_NUMBER_INT);
                                    } else {
                                        $width = filter_var($dsp_theme_options['opt-channel-video-image-width']['width'], FILTER_SANITIZE_NUMBER_INT);

                                        $ratio_width = filter_var($dsp_theme_options['opt-channel-video-image-aspect-ratio']['width'], FILTER_SANITIZE_NUMBER_INT);
                                        $ratio_height = filter_var($dsp_theme_options['opt-channel-video-image-aspect-ratio']['height'], FILTER_SANITIZE_NUMBER_INT);

                                        $ratio = $ratio_height / $ratio_width;
                                    }
                                    include(locate_template('page-templates/templates-part/channel-videos.php'));
                                    ?>
                                </div>
                                <!-- Single Channel Video section end -->
                                <?php
                                $cnt++;
                            }
                        }
                    }
                    // Display Recomendation section
                    if ($dsp_theme_options['opt-related-section'] == 1) {
                        if ($dsp_theme_options['opt-related-option'] == 'channel') {
                            $type = 'channel';
                            $related_id = get_post_meta(get_the_ID(), 'dspro_channel_id', true);
                        } else {
                            $type = 'video';
                            $related_id = $theme_function->first_video_id(get_the_ID());
                        }
                        ?>
                        <div class="col-sm-12 no-gutters pt-7">
                            <?php
                            include(locate_template('page-templates/templates-part/related-content.php'));
                            ?>
                        </div>
                        <?php
                        //array_push($class_array, 'related_content');
                    }
                    $theme_function->slick_init_options('slick_related_carousel', 'related_content', 'related');
                    $theme_function->slick_init_options('slick_carousel', $class_array, 'video');
                    ?>
                </div> <!-- other-categories -->
            </div><!-- container -->
            <?php
        } else {
            include(locate_template('page-templates/templates-part/not-in-web-platform.php'));
        }
    endwhile;
}
if (!empty($trailer_id)) {
    ?>
    <!-- Script to display video of user is ideal for 5 seconds -->
    <script type="text/javascript">
        idleTimer = null;
        idleState = false;
        idleWait = 5000;
        (function ($) {

            $.fn.isInViewport = function () {
                var elementTop = $(this).offset().top;
                var elementBottom = elementTop + $(this).outerHeight();
                var viewportTop = $(window).scrollTop();
                var viewportBottom = viewportTop + $(window).height();
                return elementBottom > viewportTop && elementTop < viewportBottom;
            };

            // $settings[] = 'loopplayback=true';

            const mountObj = {
                video_id: "<?php echo $trailer_id; ?>",
                company_id: "<?php echo $company_id; ?>",
                target: ".player",
                autostart: true,
                muted: <?php echo $mute_on_load ? "true" : "false"; ?>,
                fluid: false,
                // We need to loop but don't have a value for it yet...ugh
                controls: false,
                <?php // This flag controls ads; we have this set to false since we are just displaying a trailer ?>
                show_interruptions: false,
                theme: {}
            }

            /* PLAYER THEMEING */
            <?php  if (!empty($dsp_theme_options["opt-player-icon-color"])) { ?>
                mountObj.theme.fontColor = "<?php echo $dsp_theme_options["opt-player-icon-color"]; ?>";
            <?php } ?>

            <?php  if (!empty($dsp_theme_options["opt-player-font-color-hover"])) { ?>
                mountObj.theme.fontColorHover = "<?php echo $dsp_theme_options["opt-player-font-color-hover"]; ?>";
            <?php } ?>

            <?php  if (!empty($dsp_theme_options["opt-player-progress-slider-main"])) { ?>
                mountObj.theme.progressSliderMain = "<?php echo $dsp_theme_options["opt-player-progress-slider-main"]; ?>";
            <?php } ?>

            <?php  if (!empty($dsp_theme_options["opt-player-progress-slider-bg"])) { ?>
                mountObj.theme.progressSliderBackground = "<?php echo $dsp_theme_options["opt-player-progress-slider-bg"]; ?>";
            <?php } ?>

            <?php  if (!empty($dsp_theme_options["opt-player-control-bar-color"])) { ?>
                mountObj.theme.controlBar = "<?php echo $dsp_theme_options["opt-player-control-bar-color"]; ?>";
            <?php } ?>
            /* /END PLAYER THEMEING */


            <?php if (!empty($chnl_id)) { ?>
                mountObj.channel_id = "<?php echo $chnl_id; ?>";
                mountObj.channel_title = <?php echo json_encode($chnl_title); ?>;
            <?php } ?>
            <?php if(!empty($dspro_channel_id)) { ?>
                mountObj.dspro_channel_id = "<?php echo $dspro_channel_id; ?>";
            <?php } ?>


            $("#video-overlay").on('mousemove', function (e) {
                if ((e.pageX - this.offsetLeft) < $(this).width() / 2) {
                    $('.channel-teaser-info').fadeOut();
                } else {
                    $('.channel-teaser-info').fadeIn();
                }
            });

            $('#video-overlay').bind('mouseleave', function (e) {
                $('.channel-teaser-info').fadeOut();
            });

            let playerMounted = false;

            $(document).ready(function () {
                $(window).bind('resize mousemove keydown scroll', function (e) {
                    if ($('.inner-banner-bg').isInViewport()) {
                        clearTimeout(idleTimer);
                        idleState = false;
                        idleTimer = setTimeout(function () {
                            $('.channel-teaser').show();
                            $('.chanl_background_img').hide();
                            DotPlayer.mount(mountObj);
                            idleState = true;
                        }, idleWait);
                        if (playerMounted) {
                            DotPlayer.play();
                        }
                    } else {
                        if (playerMounted) {
                            DotPlayer.pause();
                        }
                        clearTimeout(idleTimer);
                    }
                });
                $("body").trigger("mousemove");
            });


            var dspPlayerCheck = setInterval(function () {
                if (typeof DotPlayer.on !== "undefined") {
                    clearInterval(dspPlayerCheck);
                    playerMounted = true;
                }
            }, 250);

        })(jQuery)
    </script>
<?php } ?>
<?php get_footer(); ?>
