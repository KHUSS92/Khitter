<aside class="sidebar-left">
    <div class="user-profile">
        <img src="<?php echo get_avatar_url(get_current_user_id()); ?>" alt="User Avatar" class="profile-avatar">
        <div class="user-info">
            <p class="username"><?php echo wp_get_current_user()->display_name; ?></p>
            <p class="handle">@<?php echo wp_get_current_user()->user_login; ?></p>
        </div>
    </div>

    <nav>
        <ul>
            <li><a href="#"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="#"><i class="fas fa-search"></i> Explore</a></li>
            <li><a href="#"><i class="fas fa-bell"></i> Notifications</a></li>
            <li><a href="#"><i class="fas fa-envelope"></i> Messages</a></li>
            <li><a href="#"><i class="fas fa-list"></i> Lists</a></li>
            <li><a href="#"><i class="fas fa-user"></i> Profile</a></li>
            <li><a href="#"><i class="fas fa-ellipsis-h"></i> More</a></li>
        </ul>
    </nav>
</aside>
