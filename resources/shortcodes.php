<?php

use \ZypeMedia\Controllers\Consumer;
use Themosis\Facades\Input;
use Themosis\Facades\Asset;

add_shortcode('zype_grid', function() {
    switch (Input::get('zype_type')) {
        case 'video_single':
            return Consumer\Videos::single();
            break;
        case 'video_index':
            return Consumer\Videos::index();
            break;
    }

    return Consumer\Gridscreen::index();
});

if (Config::get('zype.livestream_enabled')) {
    add_shortcode('zype_livestream', function() {
        return Consumer\Live::show();
    });
}

add_shortcode('zype_categories', function() {
    return Consumer\Category::categories_list();
});

add_shortcode('zype_auth', function($attrs = array()) {
    $type = !empty($attrs['type'])? $attrs['type']: Input::get('zype_auth_type', 'login');

    $loginController = new Consumer\Auth();
    $profileController = new Consumer\Profile();

    switch ($type) {
        case 'login':
            return $loginController->login();
        case 'register':
            return $loginController->signup();
        case 'forgot':
            return $profileController->forgot_password();
    }
});

add_shortcode('zype_signup', function(){
    $loginController = new Consumer\Auth();
    return $loginController->signup();
});

add_shortcode('zype_video', function($attrs) {
    $id = $attrs['id'];
    $view = !empty($attrs['view'])? $attrs['view']: 'full';

    if (!$id) {
        return;
    }

    $videos = new Consumer\Videos();

    return $videos->single($id, $view);
});

add_shortcode('zype_playlist', function($attrs) {
    $id = $attrs['id'];

    if (!$id) {
        return;
    }

    if (Input::get('zype_type') != 'video_single') {
        global $parent_id;
        $parent_id = $id;

        $playlist = \Zype::get_playlist($id);
        if ($playlist) {
            global $items;
            $items = $playlist->playlist_item_count;
        }

        $Gridscreen = new Consumer\Gridscreen();
        return $Gridscreen->index();
    }

    $videos = new Consumer\Videos();
    return $videos->single();
});
