<?php
function fetch_trending_topics() {
    $response = wp_remote_get("https://trends.google.com/trends/api/dailytrends?hl=en-US&tz=-480&geo=US");

    if (is_wp_error($response)) {
        wp_send_json_error(['error' => 'Failed to fetch trends.']);
    }

    $body = wp_remote_retrieve_body($response);
    $cleaned = preg_replace('/^.+?\n/', '', $body); // Clean JSON
    $data = json_decode($cleaned, true);

    if (!$data || empty($data['default']['trendingSearchesDays'][0]['trendingSearches'])) {
        wp_send_json_error(['error' => 'No trending topics found.']);
    }

    $trends = array_map(fn($item) => $item['title']['query'], $data['default']['trendingSearchesDays'][0]['trendingSearches']);
    wp_send_json_success($trends);
}

add_action('wp_ajax_get_trending_topics', 'fetch_trending_topics');
add_action('wp_ajax_nopriv_get_trending_topics', 'fetch_trending_topics');
