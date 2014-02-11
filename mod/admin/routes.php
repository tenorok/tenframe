<?php

namespace ten;

$settings = core::requireFile(__DIR__ . '/conf/settings.php');

$page = core::resolveRelativePath($settings['urls']['page']);
$ctr = 'ten\mod\admin\ctr\\';

route::get([
    'url' => [
        $page,
        $page . '/{page}/',
        $page . '/{page}/{tab}/'
    ],
    'call' => $ctr . 'page::page'
]);

route::post([
    'url' => $page . '/auth/',
    'call' => $ctr . 'auth::auth'
]);

route::post([
    'url' => $page . '/quit/',
    'call' => $ctr . 'auth::quit'
]);