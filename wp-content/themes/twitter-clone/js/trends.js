jQuery(document).ready(function($) {
    $.ajax({
        url: MyTrends.ajax_url,
        method: 'POST',
        dataType: 'json',
        data: {
            action: 'get_searchapiio_trends'
        },
        success: function(response) {
            if (response.success) {
                var topics = response.data; // The array of trends from PHP
                var $list = $('#trending-topics');
                $list.empty();
                topics.forEach(function(topic) {
                    $list.append('<li>' + topic + '</li>');
                });
            } else {
                // The server returned success=false
                $('#trending-topics').html('<li>' + response.data.error + '</li>');
            }
        },
        error: function() {
            $('#trending-topics').html('<li>An error occurred while fetching trends.</li>');
        }
    });
});
