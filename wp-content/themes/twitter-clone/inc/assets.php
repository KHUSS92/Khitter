<?php
function twitter_clone_assets() {
    wp_enqueue_style('twitter-clone-style', get_stylesheet_uri());
    wp_enqueue_script('trending-js', get_template_directory_uri() . '/js/main.js', ['jquery'], null, true);

    wp_localize_script('trending-js', 'trending_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
    ]);
}
add_action('wp_enqueue_scripts', 'twitter_clone_assets');
