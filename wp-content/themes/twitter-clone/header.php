<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php wp_title('|', true, 'right'); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<header class="twitter-header">
    <h1><a href="<?php echo home_url(); ?>">Twitter Clone</a></h1>
</header>
