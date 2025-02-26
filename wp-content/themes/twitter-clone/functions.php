<?php
// 🚀 Theme Setup
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

// 📝 Enqueue Styles & Scripts
function khitter_enqueue_assets() {
    // Styles
    wp_enqueue_style('khitter-style', get_stylesheet_uri(), [], filemtime(get_template_directory() . '/style.css'));

    // Font Awesome for icons
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');

    // Scripts
    wp_enqueue_script('jquery');
    wp_enqueue_script('khitter-main', get_template_directory_uri() . '/js/main.js', ['jquery'], filemtime(get_template_directory() . '/js/main.js'), true);

    // Pass AJAX URL to scripts
    wp_localize_script('khitter-main', 'trending_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
    ]);
}

add_action('wp_enqueue_scripts', 'khitter_enqueue_assets');

// 🔄 Register Sidebars
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

// 🚫 Remove Default Widgets
function khitter_remove_default_widgets() {
    unregister_widget('WP_Widget_Pages');
    unregister_widget('WP_Widget_Calendar');
    unregister_widget('WP_Widget_Archives');
    unregister_widget('WP_Widget_Meta');
    unregister_widget('WP_Widget_Categories');
    unregister_widget('WP_Widget_Search');
}

add_action('widgets_init', 'khitter_remove_default_widgets', 11);

// 📈 Trending Topics AJAX Handler
function khitter_fetch_trending_topics() {
    $response = wp_remote_get("https://trends.google.com/trends/api/dailytrends?hl=en-US&tz=-480&geo=US");

    if (is_wp_error($response)) {
        wp_send_json_error(['error' => 'Failed to fetch trends.']);
    }

    $body = wp_remote_retrieve_body($response);
    $cleaned = preg_replace('/^.+?\n/', '', $body); // Remove unwanted characters
    $data = json_decode($cleaned, true);

    if (!$data || empty($data['default']['trendingSearchesDays'][0]['trendingSearches'])) {
        wp_send_json_error(['error' => 'No trending topics found.']);
    }

    $trends = array_map(fn($item) => $item['title']['query'], $data['default']['trendingSearchesDays'][0]['trendingSearches']);
    wp_send_json_success($trends);
}

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
        'show_in_rest' => true, // Enables Gutenberg and API support
    ]);
}

function khitter_submit_tweet() {
    // Check user login
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'You must be logged in to tweet.']);
    }

    // Check nonce for security
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'submit_tweet_nonce')) {
        wp_send_json_error(['message' => 'Invalid request.']);
    }

    $content = sanitize_text_field($_POST['content']);

    if (empty($content)) {
        wp_send_json_error(['message' => 'Tweet cannot be empty.']);
    }

    // Create the tweet as a post
    $tweet_id = wp_insert_post([
        'post_title'  => 'Tweet by ' . wp_get_current_user()->user_login,
        'post_content'=> $content,
        'post_type'   => 'tweet',
        'post_status' => 'publish',
        'post_author' => get_current_user_id(),
    ]);

    if (is_wp_error($tweet_id)) {
        wp_send_json_error(['message' => 'Failed to submit tweet.']);
    }

    // Return the newly created tweet data
    $tweet = get_post($tweet_id);
    wp_send_json_success([
        'author'  => get_the_author_meta('user_nicename', $tweet->post_author),
        'content' => esc_html($tweet->post_content),
    ]);
}

function khitter_enqueue_scripts() {
    wp_enqueue_script('khitter-main', get_template_directory_uri() . '/js/main.js', ['jquery'], null, true);

    wp_localize_script('khitter-main', 'khitter_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('submit_tweet_nonce'),
    ]);
}

add_action('wp_enqueue_scripts', 'khitter_enqueue_scripts');
add_action('wp_ajax_submit_tweet', 'khitter_submit_tweet');
add_action('wp_ajax_nopriv_submit_tweet', 'khitter_submit_tweet'); 
add_action('wp_ajax_get_trending_topics', 'khitter_fetch_trending_topics');
add_action('wp_ajax_nopriv_get_trending_topics', 'khitter_fetch_trending_topics');
add_action('init', 'khitter_register_tweet_post_type');

// 🔗 Remove WordPress Version for Security
remove_action('wp_head', 'wp_generator');

// 🧹 Clean Up WordPress Head
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'wp_shortlink_wp_head');

// 🛡️ Disable Emoji Scripts
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

// 🏠 Redirect Default Login Logo to Site
function khitter_custom_login_logo_url() {
    return home_url();
}

add_filter('login_headerurl', 'khitter_custom_login_logo_url');
