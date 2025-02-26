<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php bloginfo('name'); ?></title>
    <?php wp_head(); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

<!-- Left Sidebar Navigation -->
<aside class="sidebar-left">
    <nav>
        <ul>
            <li><a href="<?php echo home_url(); ?>"><i class="fa fa-home"></i> Home</a></li>
            <li><a href="#"><i class="fa fa-search"></i> Explore</a></li>
            <li><a href="#"><i class="fa fa-bell"></i> Notifications</a></li>
            <li><a href="#"><i class="fa fa-envelope"></i> Messages</a></li>
            <li><a href="#"><i class="fa fa-list"></i> Lists</a></li>
            <li><a href="<?php echo get_permalink(get_option('page_for_posts')); ?>"><i class="fa fa-user"></i> Profile</a></li>
            <li><a href="#"><i class="fa fa-ellipsis-h"></i> More</a></li>
        </ul>
    </nav>
</aside>

<!-- Main Content Wrapper -->
<div class="page-wrapper">

    <!-- Twitter Header -->
    <header class="twitter-header">
    <a href="<?php echo home_url(); ?>">
        <?php if (has_custom_logo()) {
            the_custom_logo();
        } else { ?>
            <img src="<?php echo get_template_directory_uri(); ?>/assets/logo.svg" alt="Logo" class="logo">
        <?php } ?>
    </a>
</header>



    <main class="twitter-feed">
