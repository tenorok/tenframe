<?php

namespace ten;

$adminSettings = core::requireFile(__DIR__ . '/../admin/conf/settings.php');

$page = core::resolveRelativePath($adminSettings['urls']['page'], '/modshop');

$ctr = 'ten\mod\shop\ctr\\';
$baseUrl = '/modshop/';

route::get([
    'url' => [
        $page . '/categories/add/',
        $page . '/categories/{parentid}/addcategory/',
        $page . '/categories/{categoryid}/edit/',
        $page . '/categories/{categoryid}/'
    ],
    'call' => $ctr . 'categories::add_category_form'
]);

route::post([
    'url' => $page . '/categories/insert/',
    'call' => $ctr . 'categories::insert_category'
]);

$categories = core::resolveRelativePath($baseUrl, '/categories/');

route::ajax([
    'url' => core::resolveRelativePath($categories, '/sort/'),
    'call' => $ctr . 'categories::sort',
    'type' => 'post'
]);