<?php
function twitter_clone_format_date($date) {
    return date('M d, Y', strtotime($date));
}
