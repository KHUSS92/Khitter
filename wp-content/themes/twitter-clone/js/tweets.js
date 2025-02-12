jQuery(document).ready(function($) {
    $('#new-tweet-form').submit(function(e) {
        e.preventDefault();
        
        var tweetContent = $('textarea[name="tweet_content"]').val();

        $.ajax({
            url: '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                action: 'post_tweet',
                tweet_content: tweetContent
            },
            success: function() {
                location.reload(); // Refresh tweets
            }
        });
    });
});
