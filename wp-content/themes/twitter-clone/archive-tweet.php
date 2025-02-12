<?php get_header(); ?>

<main class="twitter-feed">
    <h1>All Tweets</h1>
    <?php
    $args = array('post_type' => 'tweet', 'posts_per_page' => 10);
    $tweets = new WP_Query($args);

    if ($tweets->have_posts()) :
        while ($tweets->have_posts()) : $tweets->the_post();
    ?>
        <article class="tweet">
            <h2 class="tweet-user">@<?php the_author(); ?></h2>
            <p class="tweet-content"><?php the_content(); ?></p>
        </article>
    <?php endwhile; else : ?>
        <p>No tweets found.</p>
    <?php endif; ?>
</main>

<?php get_footer(); ?>
