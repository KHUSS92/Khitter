<?php get_header(); ?>

<div class="page-wrapper">
    <?php get_template_part('sidebar', 'left'); ?>

    <main class="twitter-feed">
        <section class="tweets">
            <h3>Latest Tweets</h3>

            <form id="new-tweet-form" method="post">
                <textarea name="tweet_content" rows="3" placeholder="What's happening?"></textarea>
                <button type="submit">Tweet</button>
            </form>

            <?php
            $tweets = get_posts(['post_type' => 'tweet', 'posts_per_page' => 10]);
            if ($tweets):
                foreach ($tweets as $tweet): ?>
                    <article class="tweet">
                        <p><strong>@<?php echo get_the_author_meta('user_nicename', $tweet->post_author); ?>:</strong>
                            <?php echo esc_html($tweet->post_content); ?></p>
                    </article>
                <?php endforeach;
            else: ?>
                <p>No tweets found.</p>
            <?php endif; ?>
        </section>

    </main>

    <aside class="sidebar-right">
        <h3>Trending Topics</h3>
        <ul id="trending-topics">
            <li>Loading trends...</li>
        </ul>
    </aside>
</div>