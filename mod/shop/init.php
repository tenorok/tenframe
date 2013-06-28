<?php

$settings = \ten\core::requireFile('/mod/admin/conf/settings.php');

$adminpage = ten\text::del($settings['urls']['page'], '/') . '/modshop/categories/';
$ctr = 'ten\mod\shop\ctr\\';

array_push(ten\route::$routes, array(
    'url'      => array(
        '/' . $adminpage . 'add/',
        '/' . $adminpage . '{parentid}/addcategory/',
        '/' . $adminpage . '{categoryid}/'
    ),
    'callback' => $ctr . 'categories->add_category_form'
), array(
    'type'     => 'POST',
    'url'      => '/' . $adminpage . 'insert/',
    'callback' => $ctr . 'categories->insert_category'
));