jQuery(document).ready(function($) {
    // Like Tweet
    $('.like-button').click(function() {
        var button = $(this);
        var tweetID = button.data('tweet');

        $.ajax({
            url: '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                action: 'tweet_like',
                tweet_id: tweetID
            },
            success: function(response) {
                if (response.success) {
                    button.find('span').text(response.data.likes);
                }
            }
        });
    });

    // Retweet
    $('.retweet-button').click(function() {
        var button = $(this);
        var tweetID = button.data('tweet');

        $.ajax({
            url: '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                action: 'tweet_retweet',
                tweet_id: tweetID
            },
            success: function(response) {
                if (response.success) {
                    button.find('span').text(response.data.retweets);
                }
            }
        });
    });
});
