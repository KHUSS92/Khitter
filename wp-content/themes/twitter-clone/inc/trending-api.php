<?php
function fetch_trending_topics() {
    // Fetch the data from Google Trends
    $response = wp_remote_get("https://trends.google.com/trends/api/dailytrends?hl=en-US&tz=-480&geo=US");

    if ( is_wp_error($response) ) {
        wp_send_json_error(['error' => 'Failed to fetch trends.']);
    }

    $body = wp_remote_retrieve_body($response);

    // Log the raw response for debugging purposes
    error_log("Google Trends raw response: " . print_r($body, true));

    // Remove the API prefix that looks like ")]}'," or similar patterns
    $body = preg_replace('/^\)\]\}\'\,?\s*/', '', $body);

    $data = json_decode($body, true);

    // Check if the expected data exists
    if ( !isset($data['default']['trendingSearchesDays'][0]['trendingSearches']) || empty($data['default']['trendingSearchesDays'][0]['trendingSearches']) ) {
        wp_send_json_error(['error' => 'No trending topics found.']);
    }

    // Extract trending topics
    $trends = array_map(function($item) {
        return $item['title']['query'];
    }, $data['default']['trendingSearchesDays'][0]['trendingSearches']);

    wp_send_json_success($trends);
}

add_action('wp_ajax_get_trending_topics', 'fetch_trending_topics');
add_action('wp_ajax_nopriv_get_trending_topics', 'fetch_trending_topics');
?>
