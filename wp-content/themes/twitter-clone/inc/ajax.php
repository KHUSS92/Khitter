<?php
// Example AJAX handler
function twitter_clone_example_ajax() {
    wp_send_json_success(['message' => 'AJAX works!']);
}
add_action('wp_ajax_example_action', 'twitter_clone_example_ajax');
add_action('wp_ajax_nopriv_example_action', 'twitter_clone_example_ajax');
