<?php get_header(); ?>

<main class="twitter-feed">
    <h1>Latest Tweets</h1>

    <!-- New Tweet Form -->
    <form method="POST" id="new-tweet-form">
        <textarea name="tweet_content" placeholder="What's happening?" required></textarea>
        <button type="submit">Tweet</button>
    </form>

    <!-- Tweets Loop -->
    <?php
    $args = array('post_type' => 'tweet', 'posts_per_page' => 10);
    $tweets = new WP_Query($args);
    
    if ($tweets->have_posts()) :
        while ($tweets->have_posts()) : $tweets->the_post();
    ?>
        <article class="tweet">
            <div class="tweet-header">
                <img class="tweet-avatar" src="<?php echo get_avatar_url(get_the_author_meta('ID')); ?>" alt="User Avatar">
                <h2 class="tweet-user">
                    <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>">
                        @<?php the_author(); ?>
                    </a>
                </h2>
            </div>

            <p class="tweet-content"><?php the_content(); ?></p>

            <div class="tweet-actions">
                <button class="like-button" data-tweet="<?php the_ID(); ?>">
                    <i class="fa fa-heart"></i> <span><?php echo get_post_meta(get_the_ID(), 'tweet_likes', true) ?: 0; ?></span>
                </button>
                <button class="retweet-button" data-tweet="<?php the_ID(); ?>">
                    <i class="fa fa-retweet"></i> <span><?php echo get_post_meta(get_the_ID(), 'tweet_retweets', true) ?: 0; ?></span>
                </button>
            </div>
        </article>
    <?php 
        endwhile;
    else :
        echo '<p>No tweets found.</p>';
    endif;
    ?>
</main>

<?php get_footer(); ?>
