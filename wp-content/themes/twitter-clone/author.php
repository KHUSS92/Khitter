<?php get_header(); ?>

<main class="user-profile">
    <div class="profile-header">
        <img class="profile-avatar" src="<?php echo get_avatar_url(get_the_author_meta('ID')); ?>" alt="Profile Picture">
        <h1>@<?php the_author(); ?></h1>
        <p><?php the_author_meta('description'); ?></p>
    </div>

    <h2>User Tweets</h2>
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <article class="tweet">
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
