jQuery(document).ready(function($) {
    $.ajax({
        url: MyTrends.ajax_url,
        type: 'POST',
        dataType: 'json',
        data: { action: 'get_trending_topics' },
        success: function(response) {
            if (response.success && Array.isArray(response.data)) {
                let topics = response.data; // now this is the array of topic strings
                let html = '';
                topics.forEach(function(topic) {
                    html += '<li>' + topic + '</li>';
                });
                $('#trending-topics').html(html);
            } else {
                console.error("Error fetching trends:", response.data.error);
            }
        },
        error: function(xhr, status, error) {
            console.error("Error fetching trends.", error);
        }
    });
});
