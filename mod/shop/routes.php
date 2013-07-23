<?php

namespace ten;

$settings = core::requireFile(__DIR__ . '/../admin/conf/settings.php');

$adminpage = text::rdel($settings['urls']['page'], '/') . '/modshop/categories';
$ctr = 'ten\mod\shop\ctr\\';

route::get([
    'url' => [
        $adminpage . '/add/',
        $adminpage . '/{parentid}/addcategory/',
        $adminpage . '/{categoryid}/'
    ],
    'call' => $ctr . 'categories::add_category_form'
]);

route::post([
    'url' => $adminpage . '/insert/',
    'call' => $ctr . 'categories::insert_category'
]);