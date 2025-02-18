<?php
// Enqueue Styles & Scripts
function twitter_clone_enqueue_assets() {
    wp_enqueue_style('main-style', get_stylesheet_uri()); // Load style.css
    wp_enqueue_style('twitter-icons', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css');

    // Load jQuery (Default in WordPress)
    wp_enqueue_script('jquery');
    
    // Load Custom JavaScript for Tweet Actions
    wp_enqueue_script('tweet-actions-js', get_template_directory_uri() . '/js/tweet-actions.js', array('jquery'), null, true);
}

add_action('wp_enqueue_scripts', 'twitter_clone_enqueue_assets');

// Register the "Tweets" Custom Post Type
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

// Handle Like Action via AJAX
function tweet_like() {
    $tweet_id = intval($_POST['tweet_id']);
    $likes = get_post_meta($tweet_id, 'tweet_likes', true);
    $likes = $likes ? $likes + 1 : 1;

    update_post_meta($tweet_id, 'tweet_likes', $likes);

    wp_send_json_success(['likes' => $likes]);
}

add_action('wp_ajax_tweet_like', 'tweet_like');
add_action('wp_ajax_nopriv_tweet_like', 'tweet_like'); // Allow guest users to like

// Handle Retweet Action via AJAX
function tweet_retweet() {
    $tweet_id = intval($_POST['tweet_id']);
    $retweets = get_post_meta($tweet_id, 'tweet_retweets', true);
    $retweets = $retweets ? $retweets + 1 : 1;

    update_post_meta($tweet_id, 'tweet_retweets', $retweets);

    wp_send_json_success(['retweets' => $retweets]);
}

add_action('wp_ajax_tweet_retweet', 'tweet_retweet');
add_action('wp_ajax_nopriv_tweet_retweet', 'tweet_retweet'); // Allow guest users to retweet

// Handle Posting a Tweet via Form
function post_tweet() {
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
        'post_author'  => get_current_user_id(), // Assign tweet to logged-in user
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
add_action('wp_ajax_nopriv_post_tweet', 'post_tweet'); // Allow guest users to tweet

?>
