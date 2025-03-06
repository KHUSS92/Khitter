console.log("twitter-trends.js loaded!");
console.log("MyAjax in script:", typeof MyAjax);

jQuery(document).ready(function($) {
    $.ajax({
        url: MyAjax.ajax_url,
        method: 'POST',
        dataType: 'json',
        data: { action: 'get_trending_topics' },
        success: function(response) {
        },
        error: function() {
        }
    });
});
