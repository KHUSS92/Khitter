<?php
/**
 * functions.php
 * Theme functions and definitions for Khitter
 */

// ─────────────────────────────────────────────
// 🚀 THEME SETUP
// ─────────────────────────────────────────────
function khitter_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', [
        'height'      => 60,
        'width'       => 60,
        'flex-width'  => true,
        'flex-height' => true,
    ]);

    // Register navigation menus
    register_nav_menus([
        'primary' => __('Primary Menu', 'khitter'),
    ]);
}
add_action('after_setup_theme', 'khitter_theme_setup');

// ─────────────────────────────────────────────
// 📝 ENQUEUE STYLES & SCRIPTS
// ─────────────────────────────────────────────
function khitter_enqueue_assets() {
    // Styles
    wp_enqueue_style('khitter-style', get_stylesheet_uri(), [], filemtime(get_template_directory() . '/style.css'));
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');

    // Scripts
    wp_enqueue_script('jquery');

    // Main script for tweet submissions
    wp_enqueue_script(
        'khitter-main',
        get_template_directory_uri() . '/js/main.js',
        ['jquery'],
        filemtime(get_template_directory() . '/js/main.js'),
        true
    );

    // Localize script for tweet submission
    wp_localize_script('khitter-main', 'khitter_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('submit_tweet_nonce'),
    ]);
}
add_action('wp_enqueue_scripts', 'khitter_enqueue_assets');

/**
 * Enqueue the script that handles fetching trending topics.
 */
function khitter_enqueue_trends_script() {
    wp_enqueue_script(
        'khitter-trends',
        get_template_directory_uri() . '/js/trends.js',
        ['jquery'],
        '1.0',
        true
    );

    // Localize script for trending topics – variable name must match in JS.
    wp_localize_script('khitter-trends', 'MyTrends', [
        'ajax_url' => admin_url('admin-ajax.php'),
    ]);
}
add_action('wp_enqueue_scripts', 'khitter_enqueue_trends_script');

// ─────────────────────────────────────────────
// 🔄 REGISTER SIDEBARS
// ─────────────────────────────────────────────
function khitter_register_sidebars() {
    register_sidebar([
        'name'          => __('Left Sidebar', 'khitter'),
        'id'            => 'left-sidebar',
        'before_widget' => '<div class="widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3>',
        'after_title'   => '</h3>',
    ]);

    register_sidebar([
        'name'          => __('Right Sidebar', 'khitter'),
        'id'            => 'right-sidebar',
        'before_widget' => '<div class="widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3>',
        'after_title'   => '</h3>',
    ]);
}
add_action('widgets_init', 'khitter_register_sidebars');

// ─────────────────────────────────────────────
// 🚫 REMOVE DEFAULT WIDGETS
// ─────────────────────────────────────────────
function khitter_remove_default_widgets() {
    unregister_widget('WP_Widget_Pages');
    unregister_widget('WP_Widget_Calendar');
    unregister_widget('WP_Widget_Archives');
    unregister_widget('WP_Widget_Meta');
    unregister_widget('WP_Widget_Categories');
    unregister_widget('WP_Widget_Search');
}
add_action('widgets_init', 'khitter_remove_default_widgets', 11);

// ─────────────────────────────────────────────
// 📈 TRENDING TOPICS (USING searchapi.io)
// ─────────────────────────────────────────────
function khitter_fetch_trending_topics() {
    // Updated API endpoint based on the documentation.
    // Note: Replace the API key if necessary.
    $api_url = '';
    
    // Set a User-Agent header to mimic a browser request.
    $response = wp_remote_get($api_url, [
        'headers' => [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
        ],
    ]);
    
    if ( is_wp_error($response) ) {
        error_log("Trend API error: " . $response->get_error_message());
        wp_send_json_error(['error' => 'Failed to fetch trends from searchapi.io']);
    }
    
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    
    // Log the raw response for debugging.
    error_log("Trend API raw response: " . substr($body, 0, 500));
    
    if ( empty($data) ) {
        error_log("Trend API response empty or unexpected structure: " . print_r($data, true));
        wp_send_json_error(['error' => 'No trending topics found.']);
    }
    
    // Based on the documentation, trending topics are returned in the 'results' key.
    if ( ! isset($data['trends']) || empty($data['trends']) ) {
        wp_send_json_error(['error' => 'No trending topics found.']);
    }
    
    $trends = [];
    foreach ($data['trends'] as $trend_item) {
        // The trending “title” is actually under `query`
        $trends[] = $trend_item['query'] ?? 'Untitled Topic';
    }
    
    wp_send_json_success($trends);
}
add_action('wp_ajax_get_trending_topics', 'khitter_fetch_trending_topics');
add_action('wp_ajax_nopriv_get_trending_topics', 'khitter_fetch_trending_topics');

// ─────────────────────────────────────────────
// 🐦 TWEET CUSTOM POST TYPE
// ─────────────────────────────────────────────
function khitter_register_tweet_post_type() {
    register_post_type('tweet', [
        'labels' => [
            'name'          => __('Tweets', 'khitter'),
            'singular_name' => __('Tweet', 'khitter'),
        ],
        'public'       => true,
        'has_archive'  => true,
        'supports'     => ['title', 'editor', 'author'],
        'menu_icon'    => 'dashicons-twitter',
        'show_in_rest' => true,
    ]);
}
add_action('init', 'khitter_register_tweet_post_type');

// ─────────────────────────────────────────────
// ✏️ TWEET SUBMISSION AJAX
// ─────────────────────────────────────────────
function khitter_submit_tweet() {
    if ( !is_user_logged_in() ) {
        wp_send_json_error(['message' => 'You must be logged in to tweet.']);
    }

    if ( !isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'submit_tweet_nonce') ) {
        wp_send_json_error(['message' => 'Invalid request.']);
    }

    $content = sanitize_text_field($_POST['content']);
    if ( empty($content) ) {
        wp_send_json_error(['message' => 'Tweet cannot be empty.']);
    }

    $tweet_id = wp_insert_post([
        'post_title'   => 'Tweet by ' . wp_get_current_user()->user_login,
        'post_content' => $content,
        'post_type'    => 'tweet',
        'post_status'  => 'publish',
        'post_author'  => get_current_user_id(),
    ]);

    if ( is_wp_error($tweet_id) ) {
        wp_send_json_error(['message' => 'Failed to submit tweet.']);
    }

    $tweet = get_post($tweet_id);
    wp_send_json_success([
        'author'  => get_the_author_meta('user_nicename', $tweet->post_author),
        'content' => esc_html($tweet->post_content),
    ]);
}
add_action('wp_ajax_submit_tweet', 'khitter_submit_tweet');
add_action('wp_ajax_nopriv_submit_tweet', 'khitter_submit_tweet');

// ─────────────────────────────────────────────
// 🔗 REMOVE WORDPRESS VERSION FOR SECURITY
// ─────────────────────────────────────────────
remove_action('wp_head', 'wp_generator');

// ─────────────────────────────────────────────
// 🧹 CLEAN UP WORDPRESS HEAD
// ─────────────────────────────────────────────
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'wp_shortlink_wp_head');

// ─────────────────────────────────────────────
// 🛡️ DISABLE EMOJI SCRIPTS
// ─────────────────────────────────────────────
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

// ─────────────────────────────────────────────
// 🏠 REDIRECT DEFAULT LOGIN LOGO TO SITE
// ─────────────────────────────────────────────
function khitter_custom_login_logo_url() {
    return home_url();
}
add_filter('login_headerurl', 'khitter_custom_login_logo_url');
?>
