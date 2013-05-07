<?php

require 'conf/settings.php';

array_push(ten\route::$routes, array(

    'url'      => array(
        $settings['urls']['page'],
        $settings['urls']['page'] . '{page}/',
        $settings['urls']['page'] . '{page}/{tab}/'
    ),
    'callback' => 'mod_admin_page->page'
), array(

    'url'      => $settings['urls']['page'] . 'auth/',
    'callback' => 'mod_admin_auth->auth',
    'type'     => 'POST'
), array(

    'url'      => $settings['urls']['page'] . 'quit/',
    'callback' => 'mod_admin_auth->quit',
    'type'     => 'POST'
));