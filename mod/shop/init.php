<?php

// Формирование элемента для добавления в роутинг

require ROOT . '/mod/admin/conf/settings.php';

$adminpage = ten\text::del($settings['urls']['page'], '/');

ten\file::autogen('/mod/shop/view/include/routes.js', "core.addRoute({
    url:  [
        '" . $settings['urls']['page'] . "',
        '" . $settings['urls']['page'] . "{page}/',
        '" . $settings['urls']['page'] . "{page}/{tab}'
    ],
    ctrl: 'core.mod.shop.categories.controller',
    func: 'list'
}, {
    url: [
        '/" . $adminpage . "/modshop/categories/add/',
        '/" . $adminpage . "/modshop/categories/{parentid}/addcategory/'
    ],
    ctrl: 'core.mod.shop.categories.controller',
    func: 'add'
}, {
    url:  '/" . $adminpage . "/modshop/categories/{categoryid}/',
    ctrl: 'core.mod.shop.categories.controller',
    func: 'edit'
});");

array_push(ten\route::$routes, array(

    'url'      => array(
        '/' . $adminpage . '/modshop/categories/add/',
        '/' . $adminpage . '/modshop/categories/{parentid}/addcategory/',
        '/' . $adminpage . '/modshop/categories/{categoryid}/'
    ),
    'callback' => 'mod_shop_categories->add_category_form'
), array(

    'type'     => 'POST',
    'url'      => '/' . $adminpage . '/modshop/categories/insert/',
    'callback' => 'mod_shop_categories->insert_category'
));