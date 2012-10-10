<?php

// Формирование элемента для добавления в роутинг

require ROOT . '/mod/admin/conf/settings.php';

$adminpage = ten_text::del($settings['urls']['page'], '/');

file_put_contents(ROOT . '/mod/shop/view/include/routes.js', "routes.push({
	url:  [
		'" . $settings['urls']['page'] . "',
		'" . $settings['urls']['page'] . "{page}/',
		'" . $settings['urls']['page'] . "{page}/{tab}'
	],
	ctrl: 'mod_shop_categories',
	func: 'list'
}, {
	url: [
		'/" . $adminpage . "/modshop/{categories}/add/',
		'/" . $adminpage . "/modshop/{categories}/{categoryid}/addcategory/'
	],
	ctrl: 'mod_shop_categories',
	func: 'add'
});");

array_push(core::$routes, array(
	
	'url'      => '/' . $adminpage . '/modshop/categories/add/',
	'callback' => 'mod_shop_categories->add_category_form'
), array(
	
	'type'     => 'POST',
	'url'      => '/' . $adminpage . '/modshop/categories/insert/',
	'callback' => 'mod_shop_categories->insert_category'
), array(
	
	'url'      => '/' . $adminpage . '/modshop/categories/{categoryid}/addcategory/',
	'callback' => 'mod_shop_categories->add_category_form'
));