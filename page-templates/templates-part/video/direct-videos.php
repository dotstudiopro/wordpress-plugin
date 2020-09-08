<?php
global $client_token, $wp;

$video_id = $video_slug;

$checkDefaultSubscriptionBehavior = $dsp_api->get_default_subscription_behavior();

if (!is_wp_error($checkDefaultSubscriptionBehavior) && !empty($checkDefaultSubscriptionBehavior)){
    if($checkDefaultSubscriptionBehavior['behavior'] == 'lock_videos'){
        $user_subscribe = $dsp_api->get_user_subscription($client_token);
        if (is_wp_error($user_subscribe) || !$user_subscribe || empty($user_subscribe['subscriptions'][0]['subscription']['product']['id'])) {
            get_header(); 
            ?>
            <div class="custom-container container pt-5 pb-5  pt-5 pb-5 center-page-content">
                <div class="row no-gutters">
                    <h3 class="col-12 text-center">In order to view this video you need to subscribe first</h3>
                    <div class="col-12 text-center pt-3"><a href="/packages" title="Subscribe Now" class="btn btn-secondary btn-ds-secondary">Subscribe Now</a></div>
                </div>
            </div>
            <?php
        }
        else{
            include(locate_template('page-templates/templates-part/video/direct-videos-inner.php'));
        }
    }
    else{
        include(locate_template('page-templates/templates-part/video/direct-videos-inner.php'));
    }
    
}
else{
   wp_redirect(home_url()); 
}

?>
    
