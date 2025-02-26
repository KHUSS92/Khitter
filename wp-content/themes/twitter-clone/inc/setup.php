<?php
// Theme setup
function twitter_clone_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    register_nav_menus([
        'primary' => __('Primary Menu', 'twitter-clone'),
    ]);
}
add_action('after_setup_theme', 'twitter_clone_setup');
