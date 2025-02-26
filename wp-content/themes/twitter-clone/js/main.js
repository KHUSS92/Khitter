jQuery(document).ready(function ($) {
    $('#new-tweet-form').on('submit', function (e) {
        e.preventDefault();

        const content = $('textarea[name="tweet_content"]').val();
        const button = $(this).find('button');

        if (!content.trim()) {
            alert('Tweet cannot be empty!');
            return;
        }

        button.prop('disabled', true).text('Tweeting...');

        $.ajax({
            url: khitter_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'submit_tweet',
                content: content,
                nonce: khitter_ajax.nonce,
            },
            success: function (response) {
                button.prop('disabled', false).text('Tweet');

                if (response.success) {
                    $('textarea[name="tweet_content"]').val('');
                    $('.tweets').prepend(`
                        <article class="tweet">
                            <p><strong>@${response.data.author}:</strong> ${response.data.content}</p>
                        </article>
                    `);
                } else {
                    alert(response.data.message || 'An error occurred.');
                }
            },
            error: function () {
                button.prop('disabled', false).text('Tweet');
                alert('An error occurred. Please try again.');
            },
        });
    });
});
