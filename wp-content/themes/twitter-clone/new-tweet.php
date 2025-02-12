<form method="POST" id="new-tweet-form">
    <textarea name="tweet_content" placeholder="What's happening?" required></textarea>
    <button type="submit">Tweet</button>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tweet_content = sanitize_text_field($_POST['tweet_content']);

    $tweet = array(
        'post_title'  => wp_trim_words($tweet_content, 10), // Title is first 10 words
        'post_content' => $tweet_content,
        'post_status'  => 'publish',
        'post_type'    => 'tweet'
    );

    wp_insert_post($tweet);
}
?>
