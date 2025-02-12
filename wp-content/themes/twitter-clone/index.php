<?php get_header(); ?>

<main class="twitter-feed">
    <h1>Latest Tweets</h1>
    
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <article class="tweet">
            <h2 class="tweet-user">@<?php the_author(); ?></h2>
            <p class="tweet-content"><?php the_content(); ?></p>
            <div class="tweet-actions">
                <i class="fa fa-heart"></i>
                <i class="fa fa-retweet"></i>
                <i class="fa fa-comment"></i>
            </div>
        </article>
    <?php endwhile; else : ?>
        <p>No tweets found.</p>
    <?php endif; ?>
</main>

<?php get_footer(); ?>
