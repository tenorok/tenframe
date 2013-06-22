<?php

// Формирование элемента для добавления в роутинг

$settings = \ten\core::requireFile('/mod/admin/conf/settings.php');

$adminpage = ten\text::del($settings['urls']['page'], '/') . '/modshop/categories/';
$page = $settings['urls']['page'];
$ctr = 'ten\mod\shop\ctr\\';

ten\file::autogen('/mod/shop/view/include/routes.js', "core.addRoute({
    url:  [
        '" . $page . "',
        '" . $page . "{page}/',
        '" . $page . "{page}/{tab}'
    ],
    ctrl: 'core.mod.shop.categories.controller',
    func: 'list'
}, {
    url: [
        '/" . $adminpage . "add/',
        '/" . $adminpage . "{parentid}/addcategory/'
    ],
    ctrl: 'core.mod.shop.categories.controller',
    func: 'add'
}, {
    url:  '/" . $adminpage . "{categoryid}/',
    ctrl: 'core.mod.shop.categories.controller',
    func: 'edit'
});");

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