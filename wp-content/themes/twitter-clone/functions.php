<?php
// Enqueue styles & scripts
function twitter_clone_enqueue_assets() {
    wp_enqueue_style('main-style', get_stylesheet_uri());
    wp_enqueue_style('twitter-icons', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css');
    wp_enqueue_script('jquery');
    wp_enqueue_script('tweets-js', get_template_directory_uri() . '/js/tweets.js', array('jquery'), null, true);
}

add_action('wp_enqueue_scripts', 'twitter_clone_enqueue_assets');

// Register the "Tweets" custom post type
function register_tweet_post_type() {
    $args = array(
        'label' => 'Tweets',
        'public' => true,
        'supports' => array('title', 'editor', 'author', 'thumbnail', 'comments'),
        'menu_icon' => 'dashicons-twitter',
    );
    register_post_type('tweet', $args);
}

add_action('init', 'register_tweet_post_type');

// AJAX Handler for Posting a Tweet
function post_tweet() {
    // Check if tweet content is set
    if (!isset($_POST['tweet_content']) || empty($_POST['tweet_content'])) {
        wp_send_json_error(['message' => 'Tweet content is empty.']);
        wp_die();
    }

    $tweet_content = sanitize_text_field($_POST['tweet_content']);

    $tweet = array(
        'post_title'  => wp_trim_words($tweet_content, 10), // Title is first 10 words
        'post_content' => $tweet_content,
        'post_status'  => 'publish',
        'post_type'    => 'tweet',
        'post_author'  => get_current_user_id(), // Assign tweet to the logged-in user
    );

    $new_tweet_id = wp_insert_post($tweet);

    if ($new_tweet_id) {
        wp_send_json_success(['message' => 'Tweet posted successfully!']);
    } else {
        wp_send_json_error(['message' => 'Error posting tweet.']);
    }

    wp_die();
}

add_action('wp_ajax_post_tweet', 'post_tweet');
add_action('wp_ajax_nopriv_post_tweet', 'post_tweet'); // Allow non-logged-in users to tweet

?>
