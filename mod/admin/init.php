<?php

$settings = \ten\core::requireFile('/mod/admin/conf/settings.php');

$page = $settings['urls']['page'];
$ctr = 'ten\mod\admin\ctr\\';

array_push(ten\route::$routes, array(
    'url'      => array(
        $page,
        $page . '{page}/',
        $page . '{page}/{tab}/'
    ),
    'callback' => $ctr . 'page->page'
), array(
    'url'      => $page . 'auth/',
    'callback' => $ctr . 'auth->auth',
    'type'     => 'POST'
), array(
    'url'      => $page . 'quit/',
    'callback' => $ctr . 'auth->quit',
    'type'     => 'POST'
));